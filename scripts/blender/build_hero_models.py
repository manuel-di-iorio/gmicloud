"""Aether Run — steampunk hero-game models (player drone, enemies, sky-deck platforms).

Run headless from the repo root:
  "D:\\Blender Foundation\\Blender 5.0\\blender.exe" -b --factory-startup \
      --python scripts/blender/build_hero_models.py -- assets/models/hero [preview_dir] [only]

Axes: Blender X = run direction, Z = up, -Y = side facing the game camera.
The glTF exporter converts to Y-up, so -Y here becomes +Z in three.js.
"""
import bpy
import bmesh
import json
import math
import os
import struct
import sys
from mathutils import Vector

argv = sys.argv[sys.argv.index('--') + 1:] if '--' in sys.argv else []
HERE = os.path.dirname(os.path.abspath(__file__))
OUT_DIR = os.path.abspath(argv[0]) if argv else os.path.abspath(os.path.join(HERE, '..', '..', 'assets', 'models', 'hero'))
PREVIEW_DIR = os.path.abspath(argv[1]) if len(argv) > 1 and argv[1] != '-' else None
ONLY = set(argv[2].split(',')) if len(argv) > 2 else None
R = math.radians


# ----------------------------------------------------------------- basics --
def lin(h):
    h = h.lstrip('#')
    c = [int(h[i:i + 2], 16) / 255 for i in (0, 2, 4)]
    return tuple(v / 12.92 if v <= 0.04045 else ((v + 0.055) / 1.055) ** 2.4 for v in c)


def reset_scene():
    bpy.ops.wm.read_factory_settings(use_empty=True)


def link(o):
    bpy.context.scene.collection.objects.link(o)
    return o


def select_only(objs):
    for o in bpy.context.view_layer.objects:
        o.select_set(False)
    for o in objs:
        o.select_set(True)
    bpy.context.view_layer.objects.active = objs[0]


def orient(n):
    return Vector(n).normalized().to_track_quat('Z', 'Y').to_euler()


def make_mat(name, color, metal=0.0, rough=0.5, emit=None, alpha=1.0, coat=0.0):
    m = bpy.data.materials.new(name)
    try:
        m.use_nodes = True
    except Exception:
        pass
    b = next(n for n in m.node_tree.nodes if n.type == 'BSDF_PRINCIPLED')

    def s(key, val):
        if key in b.inputs:
            b.inputs[key].default_value = val

    s('Base Color', (*lin(color), 1.0))
    s('Metallic', metal)
    s('Roughness', rough)
    if coat:
        s('Coat Weight', coat)
        s('Coat Roughness', 0.08)
    if emit:
        s('Emission Color', (*lin(emit), 1.0))
        s('Emission Strength', 1.0)
    if alpha < 1.0:
        s('Alpha', alpha)
        for attr, val in (('blend_method', 'BLEND'), ('surface_render_method', 'BLENDED')):
            try:
                setattr(m, attr, val)
            except Exception:
                pass
    m.diffuse_color = (*lin(color), alpha)
    return m


def palette():
    return {
        'brass': make_mat('Brass', '#d29f45', 1.0, 0.28),
        'brass_old': make_mat('BrassAged', '#8c6531', 1.0, 0.46),
        'copper': make_mat('Copper', '#c8703f', 1.0, 0.32),
        'iron': make_mat('Iron', '#465060', 0.9, 0.44),
        'iron_dark': make_mat('IronDark', '#1f252e', 0.85, 0.52),
        'navy': make_mat('EnamelNavy', '#143a7e', 0.15, 0.24, coat=0.7),
        'blue': make_mat('EnamelBlue', '#1d8fe6', 0.15, 0.26, coat=0.7),
        'white': make_mat('EnamelWhite', '#eef3f8', 0.05, 0.3, coat=0.5),
        'wood': make_mat('Wood', '#a86a3a', 0.0, 0.58),
        'wood2': make_mat('WoodDark', '#7a4a28', 0.0, 0.64),
        'leather': make_mat('Leather', '#5a3219', 0.0, 0.55),
        'scarf': make_mat('FabricOrange', '#f39a2e', 0.0, 0.72),
        'rubber': make_mat('Rubber', '#16181d', 0.0, 0.7),
        'ivory': make_mat('Ivory', '#efe6cf', 0.0, 0.45),
        'skin': make_mat('Skin', '#e2b089', 0.0, 0.6),
        'crimson': make_mat('FabricCrimson', '#7a1d1b', 0.0, 0.8),
        'rust': make_mat('Rust', '#6a3a22', 0.6, 0.62),
        'glass': make_mat('Glass', '#c4ebff', 0.0, 0.04, alpha=0.26),
        'glow_blue': make_mat('GlowBlue', '#3cb8ff', 0.0, 0.3, emit='#3cb8ff'),
        'glow_orange': make_mat('GlowOrange', '#ffa53a', 0.0, 0.3, emit='#ffa53a'),
        'glow_red': make_mat('GlowRed', '#ff3a26', 0.0, 0.3, emit='#ff3a26'),
    }


# -------------------------------------------------------------- primitives --
def _finish(o, name, mat, bev=0.0, seg=2, smooth=True, subsurf=0):
    o.name = name
    if mat is not None:
        o.data.materials.append(mat)
    if tuple(o.scale) != (1.0, 1.0, 1.0):
        from mathutils import Matrix
        o.data.transform(Matrix.Diagonal((*o.scale, 1.0)))
        o.scale = (1.0, 1.0, 1.0)
    if subsurf:
        md = o.modifiers.new('Sub', 'SUBSURF')
        md.levels = subsurf
        md.render_levels = subsurf
    if bev:
        md = o.modifiers.new('Bevel', 'BEVEL')
        md.width = bev
        md.segments = seg
        md.limit_method = 'ANGLE'
        md.angle_limit = R(35)
        md.harden_normals = True
    if smooth:
        o.data.shade_smooth()
    else:
        o.data.shade_flat()
    return o


def box(name, size, loc, mat, rot=(0, 0, 0), bev=0.012, seg=2):
    bpy.ops.mesh.primitive_cube_add(size=1, location=loc, rotation=rot)
    o = bpy.context.active_object
    o.scale = size
    return _finish(o, name, mat, bev, seg)


def cyl(name, r, depth, loc, mat, rot=(0, 0, 0), verts=28, bev=0.006, seg=2, r2=None):
    if r2 is None:
        bpy.ops.mesh.primitive_cylinder_add(vertices=verts, radius=r, depth=depth, location=loc, rotation=rot)
    else:
        bpy.ops.mesh.primitive_cone_add(vertices=verts, radius1=r, radius2=r2, depth=depth, location=loc, rotation=rot)
    return _finish(bpy.context.active_object, name, mat, bev, seg)


def sphere(name, r, loc, mat, scale=(1, 1, 1), seg=32, rings=16, rot=(0, 0, 0), flat=False):
    bpy.ops.mesh.primitive_uv_sphere_add(segments=seg, ring_count=rings, radius=r, location=loc, rotation=rot)
    o = bpy.context.active_object
    o.scale = scale
    return _finish(o, name, mat, smooth=not flat)


def ico(name, r, loc, mat, subdiv=1, scale=(1, 1, 1), flat=True):
    bpy.ops.mesh.primitive_ico_sphere_add(subdivisions=subdiv, radius=r, location=loc)
    o = bpy.context.active_object
    o.scale = scale
    return _finish(o, name, mat, smooth=not flat)


def torus(name, major, minor, loc, mat, rot=(0, 0, 0), seg=36, mseg=10, scale=(1, 1, 1)):
    bpy.ops.mesh.primitive_torus_add(major_radius=major, minor_radius=minor, major_segments=seg,
                                     minor_segments=mseg, location=loc, rotation=rot)
    o = bpy.context.active_object
    o.scale = scale
    return _finish(o, name, mat)


def rivet(loc, mat, normal=(0, -1, 0), r=0.016):
    return sphere('rivet', r, loc, mat, scale=(1, 1, 0.55), seg=10, rings=6, rot=orient(normal))


def prism(name, pts, thick, mat, plane='XZ', offset=0.0, bev=0.008, seg=2):
    """Planar polygon extruded along the plane's normal (pts are 2D, CCW)."""
    bm = bmesh.new()

    def P(u, v, w):
        return {'XZ': (u, w, v), 'XY': (u, v, w), 'YZ': (w, u, v)}[plane]

    verts = [bm.verts.new(P(u, v, offset - thick / 2)) for u, v in pts]
    face = bm.faces.new(verts)
    ext = bmesh.ops.extrude_face_region(bm, geom=[face])
    moved = [e for e in ext['geom'] if isinstance(e, bmesh.types.BMVert)]
    d = {'XZ': (0, thick, 0), 'XY': (0, 0, thick), 'YZ': (thick, 0, 0)}[plane]
    bmesh.ops.translate(bm, vec=Vector(d), verts=moved)
    bmesh.ops.recalc_face_normals(bm, faces=bm.faces)
    me = bpy.data.meshes.new(name)
    bm.to_mesh(me)
    bm.free()
    return _finish(link(bpy.data.objects.new(name, me)), name, mat, bev, seg)


def gear(name, r_out, r_root, r_in, teeth, thick, mat, loc=(0, 0, 0), rot=(0, 0, 0), bev=0.004):
    """Toothed ring in the local XY plane (axis = local Z)."""
    bm = bmesh.new()
    n = teeth * 4
    of, ob, inf, inb = [], [], [], []
    for i in range(n):
        a = 2 * math.pi * i / n
        r = r_out if i % 4 in (1, 2) else r_root
        ca, sa = math.cos(a), math.sin(a)
        of.append(bm.verts.new((ca * r, sa * r, thick / 2)))
        ob.append(bm.verts.new((ca * r, sa * r, -thick / 2)))
        inf.append(bm.verts.new((ca * r_in, sa * r_in, thick / 2)))
        inb.append(bm.verts.new((ca * r_in, sa * r_in, -thick / 2)))
    for i in range(n):
        j = (i + 1) % n
        bm.faces.new((of[i], of[j], inf[j], inf[i]))
        bm.faces.new((ob[j], ob[i], inb[i], inb[j]))
        bm.faces.new((ob[i], ob[j], of[j], of[i]))
        bm.faces.new((inf[i], inf[j], inb[j], inb[i]))
    bmesh.ops.recalc_face_normals(bm, faces=bm.faces)
    me = bpy.data.meshes.new(name)
    bm.to_mesh(me)
    bm.free()
    o = link(bpy.data.objects.new(name, me))
    o.location = loc
    o.rotation_euler = rot
    return _finish(o, name, mat, bev, 2)


def spoked_gear(name, r_out, r_root, teeth, thick, mat, loc, rot, spokes=5, hub_mat=None):
    """Gear rim + spokes + hub, built around the origin then moved to loc/rot."""
    rim_in = r_root * 0.78
    parts = [gear(name + '_rim', r_out, r_root, rim_in, teeth, thick, mat)]
    for k in range(spokes):
        a = 2 * math.pi * k / spokes
        mid = (rim_in + r_root * 0.22) / 2
        parts.append(box(name + '_sp', (rim_in - r_root * 0.2, thick * 0.55, thick * 0.7),
                         (math.cos(a) * mid, math.sin(a) * mid, 0), mat, rot=(0, 0, a), bev=0.003))
    parts.append(cyl(name + '_hub', r_root * 0.24, thick * 1.5, (0, 0, 0), hub_mat or mat, verts=20, bev=0.004))
    o = join(name, parts)
    o.location = loc
    o.rotation_euler = rot
    return o


def pipe(name, pts, r, mat, res=10, caps=True, handles='AUTO'):
    cu = bpy.data.curves.new(name, 'CURVE')
    cu.dimensions = '3D'
    cu.bevel_depth = r
    cu.bevel_resolution = 3
    cu.use_fill_caps = caps
    cu.resolution_u = res
    sp = cu.splines.new('BEZIER')
    sp.bezier_points.add(len(pts) - 1)
    for bp, p in zip(sp.bezier_points, pts):
        bp.co = p
        bp.handle_left_type = handles
        bp.handle_right_type = handles
    o = link(bpy.data.objects.new(name, cu))
    o.data.materials.append(mat)
    select_only([o])
    bpy.ops.object.convert(target='MESH')
    o = bpy.context.view_layer.objects.active
    o.data.shade_smooth()
    return o


def join(name, objs, pivot=None):
    objs = [o for o in objs if o is not None]
    select_only(objs)
    bpy.ops.object.convert(target='MESH')
    if len(objs) > 1:
        bpy.ops.object.join()
    o = bpy.context.view_layer.objects.active
    bpy.ops.object.transform_apply(location=True, rotation=True, scale=True)
    o.name = name
    o.data.name = name
    if pivot is not None:
        from mathutils import Matrix
        p = Vector(pivot)
        o.data.transform(Matrix.Translation(-p))
        o.location = p
    return o


def empty(name):
    e = link(bpy.data.objects.new(name, None))
    e.empty_display_size = 0.3
    return e


def parent(children, root):
    for c in children:
        mw = c.matrix_world.copy()
        c.parent = root
        c.matrix_world = mw


# ------------------------------------------------------------- AO + export --
def bake_ao(obj, hide=(), distance=0.3, floor=0.32):
    sc = bpy.context.scene
    sc.render.engine = 'CYCLES'
    sc.cycles.samples = 64
    sc.cycles.device = 'CPU'
    if sc.world is None:
        sc.world = bpy.data.worlds.new('World')
    try:
        sc.world.light_settings.distance = distance
    except Exception:
        pass
    prev = {o: o.hide_render for o in hide}
    for o in hide:
        o.hide_render = True
    me = obj.data
    attr = me.color_attributes.new('AO', 'BYTE_COLOR', 'POINT')
    me.color_attributes.active_color = attr
    try:
        me.color_attributes.render_color_index = me.color_attributes.active_color_index
    except Exception:
        pass
    select_only([obj])
    try:
        sc.render.bake.target = 'VERTEX_COLORS'
        bpy.ops.object.bake(type='AO', target='VERTEX_COLORS')
        for d in attr.data:
            v = floor + (1.0 - floor) * d.color[0]
            d.color = (v, v, v, 1.0)
    except Exception as exc:
        print('AO bake failed for', obj.name, exc)
        me.color_attributes.remove(attr)
    for o, h in prev.items():
        o.hide_render = h


def export_glb(path, objs):
    os.makedirs(os.path.dirname(path), exist_ok=True)
    select_only(objs)
    op = bpy.ops.export_scene.gltf
    keys = set(op.get_rna_type().properties.keys())
    kw = dict(filepath=path, export_format='GLB', use_selection=True, export_apply=True, export_yup=True,
              export_cameras=False, export_lights=False, export_extras=False, export_animations=False,
              export_texcoords=False, export_normals=True, export_tangents=False,
              export_vertex_color='ACTIVE', export_all_vertex_colors=False,
              export_draco_mesh_compression_enable=False, export_materials='EXPORT')
    kw = {k: v for k, v in kw.items() if k in keys}
    try:
        op(**kw)
    except TypeError:
        kw.pop('export_vertex_color', None)
        kw['export_colors'] = True
        op(**{k: v for k, v in kw.items() if k in keys or k == 'filepath'})
    glb_summary(path)


def glb_summary(path):
    with open(path, 'rb') as f:
        data = f.read()
    ln = struct.unpack_from('<I', data, 12)[0]
    js = json.loads(data[20:20 + ln])
    attrs, tris = set(), 0
    for m in js.get('meshes', []):
        for p in m['primitives']:
            attrs |= set(p['attributes'].keys())
            tris += js['accessors'][p['indices']]['count'] // 3
    print('GLB %s: %.0f KB, %d tris, nodes=%s, attrs=%s, materials=%s' % (
        os.path.basename(path), len(data) / 1024, tris, [n.get('name') for n in js['nodes']],
        sorted(attrs), [m.get('name') for m in js.get('materials', [])]))


def render_preview(name, target, dist, elev=0.32, azim=-0.62, lens=50):
    if not PREVIEW_DIR:
        return
    sc = bpy.context.scene
    for eng in ('BLENDER_EEVEE', 'BLENDER_EEVEE_NEXT'):
        try:
            sc.render.engine = eng
            break
        except TypeError:
            continue
    sc.render.resolution_x, sc.render.resolution_y = 1100, 700
    sc.render.film_transparent = False
    w = sc.world or bpy.data.worlds.new('World')
    sc.world = w
    try:
        w.use_nodes = True
        bg = w.node_tree.nodes.get('Background')
        bg.inputs[0].default_value = (*lin('#22365a'), 1.0)
        bg.inputs[1].default_value = 0.9
    except Exception:
        w.color = lin('#22365a')
    key = link(bpy.data.objects.new('KeyLight', bpy.data.lights.new('KeyLight', 'SUN')))
    key.data.energy = 3.4
    key.data.color = lin('#ffdcb0')
    key.rotation_euler = (R(52), 0, R(-38))
    rim = link(bpy.data.objects.new('RimLight', bpy.data.lights.new('RimLight', 'SUN')))
    rim.data.energy = 2.0
    rim.data.color = lin('#9fd0ff')
    rim.rotation_euler = (R(-60), 0, R(25))
    cam = link(bpy.data.objects.new('Cam', bpy.data.cameras.new('Cam')))
    cam.data.lens = lens
    t = Vector(target)
    d = Vector((math.sin(-azim) * math.cos(elev), -math.cos(azim) * math.cos(elev), math.sin(elev)))
    cam.location = t + d * dist
    cam.rotation_euler = (t - cam.location).to_track_quat('-Z', 'Y').to_euler()
    sc.camera = cam
    os.makedirs(PREVIEW_DIR, exist_ok=True)
    sc.render.filepath = os.path.join(PREVIEW_DIR, name + '.png')
    bpy.ops.render.render(write_still=True)


# =================================================================== DRONE ==
def build_drone(M):
    a, b, c = 0.56, 0.40, 0.35

    def hull_point(x, z, side=-1):
        k = 1 - (x / a) ** 2 - (z / c) ** 2
        y = side * b * math.sqrt(max(k, 0.0))
        n = Vector((x / a ** 2, y / b ** 2, z / c ** 2)).normalized()
        return Vector((x, y, z)), n

    body = []
    body.append(sphere('hull', 1.0, (0, 0, 0), M['navy'], scale=(a, b, c), seg=56, rings=28))
    body.append(sphere('belly', 1.0, (-0.04, 0, -0.16), M['copper'], scale=(0.47, 0.34, 0.25), seg=48, rings=24))

    # Riveted brass bands.
    for bx in (-0.18, -0.45):
        f = math.sqrt(1 - (bx / a) ** 2)
        body.append(torus('band', b * f + 0.006, 0.022, (bx, 0, 0), M['brass'], rot=(0, R(90), 0),
                          scale=(c / b, 1, 1), seg=48, mseg=10))
        for k in range(9):
            t = R(105 + k * 18.75)
            p = Vector((bx, (b * f + 0.024) * math.cos(t), (c * f + 0.024) * math.sin(t)))
            body.append(rivet(p, M['brass_old'], normal=(0, math.cos(t), math.sin(t)), r=0.013))

    # Nose: copper cone, brass collar and muzzle.
    body.append(cyl('nose', 0.215, 0.36, (0.62, 0, 0), M['copper'], rot=(0, R(90), 0), r2=0.07, verts=40))
    body.append(torus('collar', 0.212, 0.022, (0.45, 0, 0), M['brass'], rot=(0, R(90), 0), seg=40))
    body.append(torus('muzzle', 0.075, 0.024, (0.80, 0, 0), M['brass'], rot=(0, R(90), 0), seg=24))
    body.append(sphere('nosecap', 0.055, (0.83, 0, 0), M['brass_old'], scale=(1.2, 1, 1), seg=20, rings=12))
    for k in range(6):
        t = R(k * 60 + 30)
        body.append(rivet((0.49, 0.2 * math.cos(t), 0.18 * math.sin(t)), M['brass_old'],
                          normal=(0.4, math.cos(t), math.sin(t)), r=0.012))

    # Canopy (separate object: alpha-blended glass) + brass frame.
    bpy.ops.mesh.primitive_uv_sphere_add(segments=40, ring_count=20, radius=1.0, location=(0.14, 0, 0.22))
    canopy = bpy.context.active_object
    bm = bmesh.new()
    bm.from_mesh(canopy.data)
    bmesh.ops.delete(bm, geom=[v for v in bm.verts if v.co.z < -1e-4], context='VERTS')
    bm.to_mesh(canopy.data)
    bm.free()
    canopy.scale = (0.30, 0.26, 0.26)
    _finish(canopy, 'Canopy', M['glass'])
    body.append(torus('canopyRim', 0.262, 0.02, (0.14, 0, 0.22), M['brass'], scale=(0.30 / 0.262, 1, 1), seg=48))
    body.append(torus('rib1', 0.262, 0.012, (0.14, 0, 0.22), M['brass'], rot=(R(90), 0, 0),
                      scale=(0.30 / 0.262, 1, 1), seg=48, mseg=8))
    body.append(torus('rib2', 0.262, 0.012, (0.14, 0, 0.22), M['brass'], rot=(0, R(90), 0), seg=48, mseg=8))

    # Pilot with goggles and an orange scarf (GMI orange).
    body.append(sphere('shoulders', 0.1, (0.1, 0, 0.23), M['leather'], scale=(1.0, 1.3, 0.6)))
    body.append(torus('scarf', 0.058, 0.022, (0.12, 0, 0.265), M['scarf'], seg=20, mseg=8))
    body.append(sphere('head', 0.072, (0.13, 0, 0.32), M['skin'], seg=24, rings=14))
    body.append(sphere('cap', 0.078, (0.12, 0, 0.335), M['leather'], scale=(1.0, 1.0, 0.82), seg=24, rings=14))
    body.append(torus('strap', 0.074, 0.009, (0.13, 0, 0.325), M['leather'], seg=24, mseg=6))
    for s in (-1, 1):
        body.append(torus('goggle', 0.026, 0.009, (0.19, s * 0.032, 0.327), M['brass'], rot=(0, R(90), 0), seg=16, mseg=6))
        body.append(sphere('lens', 0.022, (0.192, s * 0.032, 0.327), M['glow_blue'], scale=(0.45, 1, 1), seg=14, rings=8))

    # Aether furnace cage under the belly (the glowing core is a separate node).
    cx, cz = -0.06, -0.385
    body.append(torus('cageTop', 0.15, 0.024, (cx, 0, -0.29), M['brass'], seg=32))
    body.append(torus('cageBot', 0.13, 0.024, (cx, 0, -0.48), M['brass'], seg=32))
    for k in range(8):
        t = 2 * math.pi * k / 8
        body.append(cyl('bar', 0.011, 0.2, (cx + 0.14 * math.cos(t), 0.14 * math.sin(t), -0.385), M['iron_dark'],
                        rot=(0, 0, 0), verts=8, bev=0))
    body.append(cyl('cageCap', 0.12, 0.07, (cx, 0, -0.52), M['iron'], rot=(R(180), 0, 0), r2=0.045, verts=28))
    core = ico('Core', 0.095, (cx, 0, cz), M['glow_blue'], subdiv=2)

    # Side nacelles with ducted propellers.
    props = []
    for s in (-1, 1):
        px, py, pz = -0.08, s * 0.53, 0.12
        body.append(cyl('pod', 0.085, 0.26, (px, py, pz), M['copper'], rot=(R(90), 0, 0), verts=28))
        body.append(sphere('podCap', 0.085, (px, py + s * 0.13, pz), M['brass'], scale=(1, 0.55, 1), seg=24, rings=12))
        body.append(torus('duct', 0.25, 0.03, (px, py, pz), M['brass'], rot=(R(90), 0, 0), seg=48, mseg=12))
        body.append(torus('ductLip', 0.25, 0.018, (px, py - s * 0.05, pz), M['brass_old'], rot=(R(90), 0, 0), seg=48, mseg=8))
        for k in range(4):
            t = R(45 + k * 90)
            mid = 0.165
            body.append(box('strut', (0.17, 0.018, 0.02), (px + mid * math.cos(t), py - s * 0.05, pz + mid * math.sin(t)),
                            M['iron_dark'], rot=(0, -t, 0), bev=0.003))
        body.append(box('arm', (0.13, 0.2, 0.05), (px, s * 0.36, pz), M['brass_old'], bev=0.01))
        body.append(pipe('feed', [(px + 0.06, s * 0.3, 0.0), (px + 0.06, s * 0.42, 0.04), (px + 0.03, s * 0.47, 0.08)],
                         0.016, M['copper']))
        blades = []
        for k in range(4):
            t = k * math.pi / 2
            blades.append(box('blade', (0.07, 0.012, 0.19), (px + 0.14 * math.sin(t), py, pz + 0.14 * math.cos(t)),
                              M['brass_old'], rot=(0, t, 0), bev=0.004))
            blades[-1].rotation_euler.rotate_axis('Z', R(24))
        blades.append(sphere('spinner', 0.045, (px, py, pz), M['brass'], seg=16, rings=10))
        props.append(join('PropL' if s < 0 else 'PropR', blades, pivot=(px, py, pz)))

    # Tail: enamel fin with GMI emblem, copper stabilizers, exhausts.
    body.append(prism('finTop', [(-0.36, 0.2), (-0.56, 0.0), (-0.82, 0.12), (-0.88, 0.6), (-0.72, 0.6)],
                      0.04, M['navy'], plane='XZ'))
    body.append(pipe('finTrim', [(-0.72, 0, 0.615), (-0.88, 0, 0.615), (-0.835, 0, 0.12)], 0.014, M['brass'],
                     handles='VECTOR'))
    body.append(prism('finLow', [(-0.42, -0.1), (-0.72, -0.34), (-0.8, -0.34), (-0.74, -0.02)],
                      0.03, M['copper'], plane='XZ'))
    body.append(prism('stab', [(-0.4, 0.0), (-0.76, -0.36), (-0.88, -0.36), (-0.84, 0.0), (-0.88, 0.36), (-0.76, 0.36)],
                      0.03, M['copper'], plane='XY', offset=0.03))
    ey = -0.027
    body.append(cyl('emblem', 0.1, 0.012, (-0.72, ey, 0.37), M['white'], rot=(R(90), 0, 0), verts=36, bev=0.003))
    body.append(torus('emblemRim', 0.1, 0.011, (-0.72, ey - 0.004, 0.37), M['brass'], rot=(R(90), 0, 0), seg=36, mseg=6))
    body.append(box('crossH', (0.075, 0.008, 0.024), (-0.745, ey - 0.008, 0.37), M['navy'], bev=0.002))
    body.append(box('crossV', (0.024, 0.008, 0.075), (-0.745, ey - 0.008, 0.37), M['navy'], bev=0.002))
    body.append(cyl('dot1', 0.015, 0.008, (-0.682, ey - 0.008, 0.352), M['glow_orange'], rot=(R(90), 0, 0), verts=14, bev=0))
    body.append(cyl('dot2', 0.015, 0.008, (-0.652, ey - 0.008, 0.382), M['glow_orange'], rot=(R(90), 0, 0), verts=14, bev=0))
    for s in (-1, 1):
        body.append(pipe('exhaust', [(-0.42, s * 0.12, -0.06), (-0.6, s * 0.13, 0.0), (-0.7, s * 0.14, 0.1)], 0.026, M['copper']))
        body.append(cyl('exhaustTip', 0.034, 0.06, (-0.71, s * 0.14, 0.12), M['brass'], rot=(0, R(-35), 0), r2=0.045, verts=20))

    # Chimney, antenna, beacon.
    body.append(cyl('chimney', 0.06, 0.24, (-0.32, -0.14, 0.34), M['iron_dark'], rot=(0, R(-14), 0), r2=0.045, verts=24))
    body.append(torus('chimneyLip', 0.058, 0.016, (-0.349, -0.14, 0.457), M['brass'], rot=(0, R(-14), 0), seg=24, mseg=8))
    body.append(cyl('antenna', 0.008, 0.4, (-0.2, 0.08, 0.52), M['brass_old'], rot=(0, R(-9), 0), verts=8, bev=0))
    beacon = sphere('Beacon', 0.04, (-0.231, 0.08, 0.72), M['glow_orange'], seg=16, rings=10)

    # Portholes and a pressure gauge on the camera side.
    for px in (-0.33, -0.06, 0.2):
        p, n = hull_point(px, -0.03)
        rot = orient(n)
        body.append(torus('portRing', 0.046, 0.013, p + n * 0.004, M['brass'], rot=rot, seg=24, mseg=8))
        body.append(cyl('portGlass', 0.038, 0.016, p, M['glow_orange'], rot=rot, verts=24, bev=0))
    p, n = hull_point(0.36, 0.1)
    rot = orient(n)
    body.append(cyl('gauge', 0.045, 0.02, p + n * 0.006, M['ivory'], rot=rot, verts=28, bev=0.003))
    body.append(torus('gaugeRim', 0.047, 0.01, p + n * 0.014, M['brass'], rot=rot, seg=28, mseg=6))
    needle = box('needle', (0.006, 0.034, 0.004), p + n * 0.018, M['glow_red'], rot=rot, bev=0)
    needle.rotation_euler.rotate_axis('Z', R(35))
    body.append(needle)

    canopy_obj = canopy
    body_obj = join('Body', body)
    core = join('Core', [core], pivot=(cx, 0, cz))
    beacon = join('Beacon', [beacon], pivot=(-0.231, 0.08, 0.72))
    root = empty('Drone')
    parts = [body_obj, canopy_obj, core, beacon] + props
    parent(parts, root)
    return {'root': root, 'objects': [root] + parts,
            'bake': [(body_obj, [canopy_obj])] + [(p, [canopy_obj]) for p in props],
            'preview': dict(target=(0, 0, 0.08), dist=4.2)}


# ================================================================ SPROCKET ==
def build_sprocket(M):
    body = []
    body.append(sphere('core', 0.34, (0, 0, 0), M['iron'], seg=56, rings=28))
    body.append(torus('equator', 0.345, 0.042, (0, 0, 0), M['brass'], seg=56, mseg=12))
    for z in (-0.17, 0.17):
        body.append(torus('seam', 0.297, 0.014, (0, 0, z), M['brass_old'], seg=48, mseg=8))
    for k in range(16):
        t = 2 * math.pi * k / 16
        n = Vector((math.cos(t), math.sin(t), 0))
        body.append(rivet(n * 0.387, M['brass_old'], normal=n, r=0.014))

    # Mine horns.
    dirs = []
    for lat in (R(52), R(-52)):
        for k in range(6):
            lon = R(k * 60 + 30)
            dirs.append(Vector((math.cos(lat) * math.cos(lon), math.cos(lat) * math.sin(lon), math.sin(lat))))
    dirs += [Vector((0, 0, -1))]
    for d in dirs:
        if d.dot(Vector((-1, 0, 0))) > 0.55:
            continue
        rot = orient(d)
        body.append(cyl('horn', 0.05, 0.16, d * 0.39, M['iron_dark'], rot=rot, r2=0.014, verts=16))
        body.append(torus('hornCollar', 0.05, 0.012, d * 0.322, M['brass'], rot=rot, seg=16, mseg=6))
        body.append(sphere('hornTip', 0.018, d * 0.47, M['brass'], seg=10, rings=6))

    # Exhaust stack + bottom thruster.
    body.append(cyl('stack', 0.04, 0.16, (-0.12, 0.12, 0.36), M['iron_dark'], rot=(R(-12), R(-10), 0), r2=0.03, verts=16))
    body.append(torus('stackLip', 0.036, 0.01, (-0.132, 0.137, 0.437), M['brass'], rot=(R(-12), R(-10), 0), seg=16, mseg=6))
    body.append(cyl('thruster', 0.09, 0.1, (0, 0, -0.39), M['iron'], rot=(R(180), 0, 0), r2=0.06, verts=24))
    body.append(cyl('thrusterGlow', 0.055, 0.012, (0, 0, -0.44), M['glow_red'], verts=20, bev=0))

    # Top cog axle.
    body.append(cyl('axle', 0.025, 0.1, (0.04, 0, 0.36), M['iron_dark'], verts=12))
    body.append(cyl('axlePin', 0.02, 0.12, (0.04, 0, 0.42), M['brass'], rot=(R(90), 0, 0), verts=12))

    # Cyclops eye facing the player (-X).
    eye = []
    eye.append(cyl('eyeHousing', 0.16, 0.14, (-0.3, 0, 0.02), M['brass'], rot=(0, R(90), 0), verts=36, bev=0.01))
    eye.append(torus('eyeBezel', 0.135, 0.022, (-0.375, 0, 0.02), M['iron_dark'], rot=(0, R(90), 0), seg=36, mseg=10))
    eye.append(sphere('eyeLens', 0.115, (-0.37, 0, 0.02), M['glow_red'], scale=(0.5, 1, 1), seg=28, rings=14))
    eye.append(box('lidTop', (0.04, 0.3, 0.06), (-0.385, 0, 0.14), M['iron'], rot=(0, R(-20), 0), bev=0.01))
    for k in range(8):
        t = 2 * math.pi * k / 8
        eye.append(sphere('bolt', 0.014, (-0.372, 0.155 * math.cos(t), 0.02 + 0.155 * math.sin(t)), M['brass_old'],
                          scale=(0.6, 1, 1), seg=8, rings=6))

    # Spinning gear ring (rotates around the vertical axis).
    ring = [gear('ringGear', 0.56, 0.52, 0.47, 22, 0.05, M['copper'])]
    ring.append(torus('ringInner', 0.405, 0.016, (0, 0, 0), M['brass'], seg=48, mseg=8))
    for k in range(6):
        t = 2 * math.pi * k / 6 + 0.26
        ring.append(box('ringSpoke', (0.075, 0.03, 0.02), (0.44 * math.cos(t), 0.44 * math.sin(t), 0), M['brass_old'],
                        rot=(0, 0, t), bev=0.004))

    cog = gear('Cog', 0.16, 0.13, 0.04, 10, 0.04, M['brass'], loc=(0.04, 0, 0.42), rot=(R(90), 0, 0))

    body_obj = join('Body', body)
    eye_obj = join('Eye', eye, pivot=(-0.37, 0, 0.02))
    ring_obj = join('Ring', ring, pivot=(0, 0, 0))
    cog_obj = join('Cog', [cog], pivot=(0.04, 0, 0.42))
    root = empty('Sprocket')
    parts = [body_obj, eye_obj, ring_obj, cog_obj]
    parent(parts, root)
    return {'root': root, 'objects': [root] + parts,
            'bake': [(o, []) for o in parts],
            'preview': dict(target=(0, 0, 0), dist=3.6)}


# ================================================================ ZEPPELIN ==
def build_zeppelin(M):
    a, b = 1.0, 0.33
    body = []
    body.append(sphere('envelope', 1.0, (0, 0, 0), M['crimson'], scale=(a, b, b), seg=64, rings=32))
    for k in range(8):
        phi = 2 * math.pi * k / 8
        pts = []
        for i in range(15):
            th = math.pi * (0.06 + 0.88 * i / 14)
            r = (b + 0.006) * math.sin(th)
            pts.append((a * 1.004 * math.cos(th), r * math.cos(phi), r * math.sin(phi)))
        body.append(pipe('rib', pts, 0.008, M['brass_old'], res=4))
    for bx in (-0.55, 0.0, 0.5):
        f = math.sqrt(1 - (bx / a) ** 2)
        body.append(torus('band', b * f + 0.008, 0.018, (bx, 0, 0), M['brass'], rot=(0, R(90), 0), seg=48, mseg=8))
    body.append(sphere('tailCap', 0.07, (0.99, 0, 0), M['brass_old'], scale=(1.3, 1, 1), seg=20, rings=10))
    body.append(cyl('axle', 0.025, 0.16, (1.08, 0, 0), M['iron_dark'], rot=(0, R(90), 0), verts=12))

    # Cruciform tail fins with brass trim and a red nav light.
    fin_pts = [(0.52, 0.16), (0.9, 0.52), (1.02, 0.52), (0.98, 0.12), (0.78, 0.08)]
    for k in range(4):
        f = prism('fin', fin_pts, 0.026, M['iron_dark'], plane='XZ')
        trim = pipe('finTrim', [(0.9, 0, 0.535), (1.02, 0, 0.535), (0.985, 0, 0.12)], 0.011, M['brass'],
                    handles='VECTOR')
        for o in (f, trim):
            o.rotation_euler = (k * math.pi / 2, 0, 0)
        body += [f, trim]
    body.append(sphere('navLight', 0.022, (0.96, 0, 0.56), M['glow_red'], seg=12, rings=8))

    # Gondola.
    gz = -0.47
    body.append(box('cabin', (0.54, 0.2, 0.15), (0, 0, gz), M['wood2'], bev=0.015))
    body.append(box('roof', (0.6, 0.24, 0.03), (0, 0, gz + 0.09), M['brass_old'], bev=0.008))
    body.append(box('keel', (0.5, 0.16, 0.04), (0, 0, gz - 0.095), M['iron'], bev=0.012))
    body.append(cyl('skid', 0.012, 0.56, (0, 0, gz - 0.135), M['brass'], rot=(0, R(90), 0), verts=10))
    for x in (-0.18, 0.18):
        body.append(box('skidPost', (0.02, 0.02, 0.05), (x, 0, gz - 0.115), M['brass'], bev=0.003))
    for x in (-0.16, 0.0, 0.16):
        body.append(cyl('window', 0.032, 0.012, (x, -0.101, gz + 0.005), M['glow_red'], rot=(R(90), 0, 0), verts=18, bev=0))
        body.append(torus('windowRing', 0.036, 0.009, (x, -0.106, gz + 0.005), M['brass'], rot=(R(90), 0, 0), seg=18, mseg=6))
    for sx in (-0.2, 0.2):
        for sy in (-0.08, 0.08):
            body.append(pipe('strut', [(sx, sy, gz + 0.1), (sx * 0.75, sy * 0.6, -0.29)], 0.009, M['iron_dark'], res=2))
    body.append(cyl('cannon', 0.03, 0.22, (-0.33, -0.07, gz - 0.02), M['iron_dark'], rot=(0, R(90), 0), verts=16))
    body.append(torus('muzzle', 0.032, 0.01, (-0.44, -0.07, gz - 0.02), M['brass'], rot=(0, R(90), 0), seg=16, mseg=6))
    for y in (-0.05, 0.05):
        body.append(cyl('stack', 0.018, 0.12, (0.24, y, gz + 0.14), M['iron_dark'], verts=12))

    # Nose searchlight.
    lantern = []
    lantern.append(torus('lampRing', 0.085, 0.022, (-1.0, 0, 0), M['brass'], rot=(0, R(90), 0), seg=32, mseg=10))
    lantern.append(sphere('lampLens', 0.08, (-1.0, 0, 0), M['glow_red'], scale=(0.5, 1, 1), seg=24, rings=12))
    lantern.append(cyl('lampHood', 0.1, 0.06, (-0.985, 0, 0.0), M['brass_old'], rot=(0, R(90), 0), verts=28, bev=0.006))

    prop = []
    for k in range(4):
        t = k * math.pi / 2
        bl = box('blade', (0.012, 0.07, 0.24), (1.15, 0.15 * math.sin(t), 0.15 * math.cos(t)), M['wood'],
                 rot=(-t, 0, 0), bev=0.004)
        bl.rotation_euler.rotate_axis('Z', R(22))
        prop.append(bl)
        prop.append(box('bladeTip', (0.014, 0.072, 0.04), (1.15, 0.255 * math.sin(t), 0.255 * math.cos(t)), M['brass'],
                        rot=(-t, 0, 0), bev=0.003))
    prop.append(cyl('spinner', 0.05, 0.08, (1.17, 0, 0), M['brass'], rot=(0, R(90), 0), r2=0.012, verts=20))

    body_obj = join('Body', body)
    lantern_obj = join('Lantern', lantern, pivot=(-1.0, 0, 0))
    prop_obj = join('Prop', prop, pivot=(1.15, 0, 0))
    root = empty('Zeppelin')
    parts = [body_obj, lantern_obj, prop_obj]
    parent(parts, root)
    return {'root': root, 'objects': [root] + parts,
            'bake': [(o, []) for o in parts],
            'preview': dict(target=(0, 0, -0.1), dist=5.2)}


# ================================================================ PLATFORM ==
D = 1.55       # half depth of the deck (three.js PLATFORM_D = 3.1)
CAP = 0.55     # end-cap length; the mid module is 1.0 long and gets stretched


def deck_strip(x0, x1, M, nails=True):
    """Planks + girders + front skirt + aether conduit between x0 and x1."""
    parts = []
    L, cx = x1 - x0, (x0 + x1) / 2
    pw = (2 * D - 0.12) / 6
    for i in range(6):
        y = -D + 0.06 + pw * (i + 0.5)
        parts.append(box('plank', (L, pw - 0.014, 0.07), (cx, y, -0.035 - 0.003 * (i % 2)),
                         M['wood'] if i % 2 == 0 else M['wood2'], bev=0.008))
        if nails:
            for yy in (y - pw * 0.3, y + pw * 0.3):
                parts.append(cyl('nail', 0.011, 0.01, (x0 + 0.035, yy, 0.0), M['iron_dark'], verts=8, bev=0))
    for s in (-1, 1):
        parts.append(box('girder', (L, 0.1, 0.3), (cx, s * (D - 0.05), -0.14), M['iron'], bev=0.01))
        parts.append(box('girderCap', (L, 0.15, 0.04), (cx, s * (D - 0.05), 0.0), M['brass'], bev=0.01))
    # Front enamel skirt with riveted brass trims.
    parts.append(box('skirt', (L, 0.04, 0.42), (cx, -D - 0.02, -0.42), M['navy'], bev=0.006))
    for z in (-0.21, -0.63):
        parts.append(box('skirtTrim', (L, 0.06, 0.035), (cx, -D - 0.03, z), M['brass_old'], bev=0.008))
    n = max(1, int(round(L / 0.2)))
    for i in range(n):
        x = x0 + L * (i + 0.5) / n
        for z in (-0.255, -0.585):
            parts.append(rivet((x, -D - 0.042, z), M['brass']))
    return parts


def build_mid(M):
    parts = deck_strip(-0.5, 0.5, M)
    # Aether conduit (GMI blue) with a clamp.
    parts.append(cyl('conduit', 0.034, 1.0, (0, -D - 0.08, -0.42), M['glow_blue'], rot=(0, R(90), 0), verts=16, bev=0))
    parts.append(torus('clamp', 0.046, 0.013, (0, -D - 0.08, -0.42), M['brass'], rot=(0, R(90), 0), seg=20, mseg=8))
    parts.append(box('clampFoot', (0.03, 0.06, 0.03), (0, -D - 0.045, -0.42), M['brass'], bev=0.004))
    # Under-structure.
    parts.append(box('crossBeam', (0.1, 2 * D - 0.2, 0.12), (0, 0, -0.14), M['iron_dark'], bev=0.01))
    for y in (-0.75, 0.75):
        parts.append(box('stringer', (1.0, 0.08, 0.1), (0, y, -0.12), M['iron_dark'], bev=0.008))
    parts.append(box('keel', (1.0, 0.16, 0.16), (0, 0, -1.0), M['iron'], bev=0.015))
    parts.append(box('keelCap', (1.0, 0.2, 0.03), (0, 0, -0.91), M['brass_old'], bev=0.006))
    for s in (-1, 1):
        y0, z0, y1, z1 = s * (D - 0.1), -0.3, s * 0.08, -0.95
        ln = math.hypot(y1 - y0, z1 - z0)
        ang = math.atan2(z1 - z0, y1 - y0)
        parts.append(box('diag', (0.07, ln, 0.06), (0, (y0 + y1) / 2, (z0 + z1) / 2), M['iron_dark'],
                         rot=(ang, 0, 0), bev=0.006))
    # Back railing.
    yb = D - 0.07
    parts.append(cyl('post', 0.024, 0.5, (0, yb, 0.27), M['brass'], verts=14))
    parts.append(sphere('postBall', 0.04, (0, yb, 0.53), M['brass'], seg=14, rings=8))
    parts.append(cyl('rail', 0.022, 1.0, (0, yb, 0.5), M['brass'], rot=(0, R(90), 0), verts=14, bev=0))
    parts.append(cyl('railMid', 0.013, 1.0, (0, yb, 0.27), M['iron_dark'], rot=(0, R(90), 0), verts=10, bev=0))
    return join('DeckMid', parts)


def build_cap(M, side):
    """End cap from x=0 (seam with the mid module) to x=side*CAP."""
    S = side

    def X(x):
        return S * x

    parts = []
    for p in deck_strip(min(0, X(0.42)), max(0, X(0.42)), M, nails=False):
        parts.append(p)
    parts.append(box('endBeam', (0.14, 2 * D + 0.02, 0.36), (X(0.47), 0, -0.16), M['brass_old'], bev=0.02, seg=3))
    parts.append(box('endBeamTop', (0.17, 2 * D + 0.06, 0.04), (X(0.47), 0, 0.0), M['brass'], bev=0.012))
    for y in (-1.2, -0.6, 0.0, 0.6, 1.2):
        parts.append(rivet((X(0.542), y, -0.08), M['brass'], normal=(S, 0, 0)))
        parts.append(rivet((X(0.542), y, -0.26), M['brass'], normal=(S, 0, 0)))
    # Conduit coupler.
    parts.append(cyl('coupler', 0.05, 0.08, (X(0.04), -D - 0.08, -0.42), M['brass'], rot=(0, R(90), 0), verts=20))
    parts.append(sphere('couplerCap', 0.05, (X(0.08), -D - 0.08, -0.42), M['brass_old'], seg=16, rings=8))
    # Corner posts.
    for y in (-D, D):
        parts.append(cyl('cornerPost', 0.1, 0.82, (X(0.45), y, -0.25), M['navy'], verts=28, bev=0.01))
        parts.append(torus('cornerRing', 0.1, 0.02, (X(0.45), y, 0.12), M['brass'], seg=28, mseg=8))
        parts.append(torus('cornerRing', 0.1, 0.02, (X(0.45), y, -0.62), M['brass'], seg=28, mseg=8))
        parts.append(sphere('cornerDome', 0.1, (X(0.45), y, 0.16), M['brass'], scale=(1, 1, 0.6), seg=24, rings=12))
    # Decorative gear on the camera-facing skirt.
    parts.append(spoked_gear('skirtGear', 0.19, 0.16, 12, 0.05, M['brass'], (X(0.2), -D - 0.07, -0.47),
                             (R(90), 0, 0), spokes=5, hub_mat=M['iron_dark']))
    # Back lamp post (GMI orange lantern).
    yb = D - 0.07
    lz = 0.84
    parts.append(cyl('lampPost', 0.028, lz - 0.08, (X(0.3), yb, (lz - 0.08) / 2), M['iron_dark'], verts=14))
    parts.append(cyl('lampBase', 0.06, 0.06, (X(0.3), yb, 0.03), M['brass'], verts=18))
    parts.append(cyl('lampFloor', 0.08, 0.02, (X(0.3), yb, lz - 0.08), M['brass'], verts=18))
    parts.append(cyl('lampRoof', 0.1, 0.09, (X(0.3), yb, lz + 0.11), M['brass_old'], r2=0.015, verts=18))
    for k in range(4):
        t = math.pi / 4 + k * math.pi / 2
        parts.append(cyl('lampBar', 0.008, 0.16, (X(0.3) + 0.065 * math.cos(t), yb + 0.065 * math.sin(t), lz),
                         M['brass'], verts=6, bev=0))
    parts.append(sphere('lampLight', 0.055, (X(0.3), yb, lz), M['glow_orange'], seg=16, rings=10))
    parts.append(cyl('rail', 0.022, 0.3, (X(0.15), yb, 0.5), M['brass'], rot=(0, R(90), 0), verts=14, bev=0))
    parts.append(cyl('railMid', 0.013, 0.3, (X(0.15), yb, 0.27), M['iron_dark'], rot=(0, R(90), 0), verts=10, bev=0))
    # Under-structure + aether thruster pod.
    parts.append(box('keel', (0.32, 0.16, 0.16), (X(0.16), 0, -1.0), M['iron'], bev=0.015))
    parts.append(box('keelCap', (0.32, 0.2, 0.03), (X(0.16), 0, -0.91), M['brass_old'], bev=0.006))
    parts.append(box('crossBeam', (0.1, 2 * D - 0.2, 0.12), (X(0.36), 0, -0.14), M['iron_dark'], bev=0.01))
    for y in (-0.75, 0.75):
        parts.append(box('stringer', (0.42, 0.08, 0.1), (X(0.21), y, -0.12), M['iron_dark'], bev=0.008))
    px, py = X(0.3), -0.55
    parts.append(cyl('pod', 0.17, 0.55, (px, py, -0.62), M['copper'], verts=32, bev=0.012))
    for z in (-0.38, -0.86):
        parts.append(torus('podRing', 0.172, 0.022, (px, py, z), M['brass'], seg=32, mseg=8))
    parts.append(cyl('nozzle', 0.13, 0.16, (px, py, -0.97), M['iron_dark'], r2=0.17, rot=(R(180), 0, 0), verts=32))
    parts.append(cyl('nozzleGlow', 0.14, 0.015, (px, py, -1.05), M['glow_blue'], verts=28, bev=0))
    parts.append(box('podArm', (0.08, 0.5, 0.08), (px, -0.28, -0.75), M['iron_dark'], bev=0.008))
    return join('CapR' if side > 0 else 'CapL', parts)


def build_platform(M):
    mid = build_mid(M)
    cap_r = build_cap(M, 1)
    cap_l = build_cap(M, -1)
    root = empty('Platform')
    parts = [mid, cap_l, cap_r]
    parent(parts, root)
    others = lambda o: [p for p in parts if p is not o]
    return {'root': root, 'objects': [root] + parts,
            'bake': [(o, others(o)) for o in parts],
            'layout': {cap_l: (-0.5, 0, 0), cap_r: (0.5, 0, 0)},
            'preview': dict(target=(0, 0, -0.35), dist=6.0, elev=0.28, azim=-0.45)}


# ==================================================================== MAIN ==
ASSETS = [('drone', build_drone), ('sprocket', build_sprocket), ('zeppelin', build_zeppelin),
          ('platform', build_platform)]


def main():
    for name, builder in ASSETS:
        if ONLY and name not in ONLY:
            continue
        reset_scene()
        M = palette()
        info = builder(M)
        for obj, hide in info['bake']:
            bake_ao(obj, hide)
        export_glb(os.path.join(OUT_DIR, name + '.glb'), info['objects'])
        for obj, loc in info.get('layout', {}).items():
            obj.location = loc
        pv = info['preview']
        render_preview(name, pv['target'], pv['dist'], pv.get('elev', 0.32), pv.get('azim', -0.62))
    print('DONE ->', OUT_DIR)


main()
