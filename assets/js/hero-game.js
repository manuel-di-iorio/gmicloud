/* Hero game — 2.5D side-view speedrun platformer (Three.js r128).
 * States: attract -> playing -> finished (recap) ; fall/enemy hit -> ready.
 * Goal: reach the portal as fast as possible; diamonds add bonus points.
 * One-button controls: Space / ArrowUp / click / tap = jump (coyote + buffer).
 * Runs behind hero content; overlay fades out on start. Sounds via WebAudio. */
(function () {
  var canvas = document.getElementById('hero-game');
  var hero = document.querySelector('.ds-hero');
  if (!canvas || !hero || !window.THREE) return;

  var hintEl = document.getElementById('hero-hint');
  var timeEl = document.getElementById('hero-time');
  var gemsEl = document.getElementById('hero-gems');
  var bestEl = document.getElementById('hero-best');
  var livesEl = document.getElementById('hero-lives');
  var progEl = document.getElementById('hero-progress-fill');
  var recapEl = document.getElementById('hero-recap');

  var reduceMotion = window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  var renderer;
  try {
    renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true, preserveDrawingBuffer: true });
  } catch (e) { return; }
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.5));

  var scene = new THREE.Scene();
  var camera = new THREE.PerspectiveCamera(42, 1, 0.1, 120);
  camera.position.set(2.4, 2.6, 10.5);

  scene.add(new THREE.HemisphereLight(0x9fb2cc, 0x0b1220, 0.95));
  var sun = new THREE.DirectionalLight(0xffffff, 0.85);
  sun.position.set(4, 8, 6);
  scene.add(sun);

  // ---- Tiny synth (no assets), created on first user gesture ----
  var FX = {
    ctx: null, master: null,
    ensure: function () {
      if (!this.ctx) {
        var AC = window.AudioContext || window.webkitAudioContext;
        if (!AC) return;
        this.ctx = new AC();
        this.master = this.ctx.createGain();
        this.master.gain.value = 0.15;
        this.master.connect(this.ctx.destination);
      }
      if (this.ctx.state === 'suspended') this.ctx.resume();
    },
    tone: function (freq, dur, type, slideTo, delay) {
      if (!this.ctx) return;
      var t0 = this.ctx.currentTime + (delay || 0);
      var o = this.ctx.createOscillator(), g = this.ctx.createGain();
      o.type = type || 'sine';
      o.frequency.setValueAtTime(freq, t0);
      if (slideTo) o.frequency.exponentialRampToValueAtTime(slideTo, t0 + dur);
      g.gain.setValueAtTime(0.0001, t0);
      g.gain.exponentialRampToValueAtTime(0.6, t0 + 0.015);
      g.gain.exponentialRampToValueAtTime(0.0001, t0 + dur);
      o.connect(g); g.connect(this.master);
      o.start(t0); o.stop(t0 + dur + 0.05);
    },
    jump: function () { this.tone(300, 0.14, 'square', 620); },
    gem: function (n) { this.tone(620 + (n % 10) * 45, 0.12, 'sine', 1400); },
    die: function () { this.tone(240, 0.32, 'sawtooth', 55); },
    go: function () { this.tone(440, 0.09, 'square', 880); },
    win: function () {
      var seq = [523, 659, 784, 1047];
      for (var i = 0; i < seq.length; i++) this.tone(seq[i], 0.16, 'triangle', null, i * 0.09);
    }
  };

  // ---- Tiny looping soundtrack (synth only, no audio files) ----
  // A-minor pentatonic arpeggio over a soft bass. Scheduled a little ahead of
  // the audio clock from the frame loop, so it stays in time and stops with
  // the tab. Off until the first gesture (autoplay rules).
  var Music = {
    on: false, bus: null, next: 0, step: 0,
    pattern: [0, 2, 1, 3, 4, 3, 2, 1],
    notes: [220.00, 261.63, 293.66, 329.63, 392.00],
    bass: 110.00, stepLen: 0.3, vol: 0.9,
    ensure: function () {
      if (this.bus || !FX.ctx) return;
      this.bus = FX.ctx.createGain();
      this.bus.gain.value = 0;
      this.bus.connect(FX.master);
    },
    toggle: function (want) {
      FX.ensure();
      this.ensure();
      this.on = want == null ? !this.on : !!want;
      if (this.bus && FX.ctx) {
        this.bus.gain.cancelScheduledValues(FX.ctx.currentTime);
        this.bus.gain.setTargetAtTime(this.on ? this.vol : 0, FX.ctx.currentTime, 0.12);
      }
      if (this.on) { this.next = Math.max(this.next, FX.ctx ? FX.ctx.currentTime + 0.05 : 0); }
      syncSoundButton();
      return this.on;
    },
    note: function (freq, at, dur, type, amp) {
      var o = FX.ctx.createOscillator(), g = FX.ctx.createGain();
      o.type = type;
      o.frequency.setValueAtTime(freq, at);
      g.gain.setValueAtTime(0.0001, at);
      g.gain.exponentialRampToValueAtTime(amp, at + 0.03);
      g.gain.exponentialRampToValueAtTime(0.0001, at + dur);
      o.connect(g); g.connect(this.bus);
      o.start(at); o.stop(at + dur + 0.02);
    },
    tick: function () {
      if (!this.on || !FX.ctx || !this.bus) return;
      if (this.next < FX.ctx.currentTime) this.next = FX.ctx.currentTime + 0.02;
      while (this.next < FX.ctx.currentTime + 0.4) {
        var at = this.next, i = this.step % 8;
        var f = this.notes[this.pattern[i]];
        this.note(f, at, 0.26, 'triangle', 0.2);
        if (i === 0 || i === 4) this.note(this.bass, at, 0.55, 'sine', 0.26);
        if (i === 6) this.note(f * 2, at, 0.16, 'sine', 0.09);
        this.step++;
        this.next += this.stepLen;
      }
    }
  };
  var soundBtn = document.getElementById('hero-sound');
  function syncSoundButton() {
    if (!soundBtn) return;
    soundBtn.textContent = Music.on ? '\u266A' : '\u266A\u0338';
    soundBtn.setAttribute('aria-pressed', Music.on ? 'true' : 'false');
    soundBtn.title = Music.on ? 'Music on' : 'Music off';
  }
  if (soundBtn) {
    syncSoundButton();
    soundBtn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      Music.toggle();
    });
  }

  function glowTexture(inner, outer) {
    var c = document.createElement('canvas');
    c.width = c.height = 64;
    var g = c.getContext('2d');
    var grad = g.createRadialGradient(32, 32, 2, 32, 32, 32);
    grad.addColorStop(0, inner);
    grad.addColorStop(1, outer);
    g.fillStyle = grad;
    g.fillRect(0, 0, 64, 64);
    var tex = new THREE.CanvasTexture(c);
    tex.minFilter = THREE.LinearFilter;
    tex.generateMipmaps = false;
    return tex;
  }
  var softGlowTex = glowTexture('rgba(255,255,255,0.9)', 'rgba(255,255,255,0)');
  var shadowTex = glowTexture('rgba(11,18,32,0.9)', 'rgba(11,18,32,0)');
  function mulberry32(a) {
    return function () {
      a |= 0; a = a + 0x6D2B79F5 | 0;
      var t = Math.imul(a ^ a >>> 15, 1 | a);
      t = t + Math.imul(t ^ t >>> 7, 61 | t) ^ t;
      return ((t ^ t >>> 14) >>> 0) / 4294967296;
    };
  }
  var R = mulberry32(1337);

  // ---- Level: same colliders as before, rendered as clouds ----
  var LEVEL = [
    { x: -3, y: -0.6, w: 8 },
    { x: 5, y: -0.6, w: 2.4 },
    { x: 9.0, y: -0.6, w: 3.4 },
    { x: 14, y: -0.6, w: 2.4 },
    { x: 18.5, y: 0.2, w: 2.6 },
    { x: 22.5, y: 1.0, w: 2.6 },
    { x: 26.5, y: 1.8, w: 3.2 },
    // HIGH road: precision jumps, no enemies, direct line.
    { x: 32.2, y: 2.6, w: 2.0 },
    { x: 36.4, y: 3.2, w: 2.0 },
    { x: 40.6, y: 3.2, w: 2.2 },
    { x: 45.0, y: 2.6, w: 2.2 },
    // LOW road: easy jumps, enemy gauntlet, dips under the split.
    { x: 31.5, y: -0.6, w: 5.0 },
    { x: 36.5, y: -0.6, w: 2.6 },
    { x: 40.4, y: -0.2, w: 2.4 },
    { x: 44.5, y: 0.0, w: 2.2 },
    // Shared finale: wider gaps, climbs, portal run.
    { x: 49.0, y: 0.8, w: 2.6 },
    { x: 53.5, y: 1.4, w: 2.2 },
    { x: 58.0, y: 1.4, w: 2.4 },
    { x: 62.0, y: 2.2, w: 2.0 },
    { x: 67.5, y: 1.6, w: 5.0 }
  ];
  var DIAMONDS = [
    [7.2, 1.3], [11.7, 1.3],
    [19.0, 1.8], [22.5, 3.4], [25.3, 3.1],
    [30.0, 4.1],
    [50.0, 2.6], [53.5, 2.0], [55.6, 3.6],
    [57.2, 2.5], [61.7, 3.3], [63.3, 3.5], [66.0, 3.3]
  ];
  var PORTAL_X = 69.0;

  var cloudMat = new THREE.MeshLambertMaterial({ color: 0x8e9fb5, emissive: 0x0e1728 });
  var platforms = [];
  LEVEL.forEach(function (d) {
    // Visible: cartoon cloud (puffy spheres). The puffs are laid out to stay
    // inside the collider footprint, so the whole visible cloud is landable
    // (before, the round caps stuck out ~0.6 past the edge and you fell
    // through the visible left tip of every cloud).
    var grp = new THREE.Group();
    var n = Math.max(2, Math.round(d.w / 1.5));
    var step = d.w / n;
    for (var i = 0; i < n; i++) {
      var r = Math.min(0.85, step * 0.95) * (0.92 + R() * 0.16);
      r = Math.min(r, d.w / 2 - 0.02);
      // Clamp the outer puffs so their silhouette ends exactly at the collider.
      var lim = Math.max(0, d.w / 2 - r);
      var cx = -d.w / 2 + (i + 0.5) * step;
      cx = Math.max(-lim, Math.min(lim, cx));
      var puff = new THREE.Mesh(new THREE.SphereGeometry(r, 14, 12), cloudMat);
      puff.position.set(cx, (R() - 0.5) * 0.18 + (i % 2 ? 0.08 : 0), -1.2 - R() * 1.0);
      puff.scale.y = 0.52;
      grp.add(puff);
    }
    grp.position.set(d.x, d.y + 0.1, 0);
    scene.add(grp);
    platforms.push({ x: d.x, y: d.y, hw: d.w / 2 });
  });

  var diamondGeo = new THREE.OctahedronGeometry(0.26);
  var diamondMat = new THREE.MeshLambertMaterial({ color: 0x9fdcff, emissive: 0x0e4a6e });
  var diamonds = [];
  DIAMONDS.forEach(function (d) {
    var grp = new THREE.Group();
    var core = new THREE.Mesh(diamondGeo, diamondMat);
    var glow = new THREE.Sprite(new THREE.SpriteMaterial({
      map: softGlowTex, color: 0x67e8f9, transparent: true, opacity: 0.55, depthWrite: false, blending: THREE.AdditiveBlending
    }));
    glow.scale.set(1.5, 1.5, 1);
    grp.add(core); grp.add(glow);
    grp.position.set(d[0], d[1], -0.35);
    grp.userData = { taken: false, phase: diamonds.length * 1.7, baseY: d[1] };
    scene.add(grp);
    diamonds.push(grp);
  });

  // Finish portal (always open — diamonds are bonus): layered spinning
  // rings, bright core and orbiting motes (all deterministic).
  var portal = new THREE.Group();
  var ring = new THREE.Mesh(
    new THREE.TorusGeometry(1.05, 0.13, 12, 40),
    new THREE.MeshBasicMaterial({ color: 0x67e8f9, transparent: true, opacity: 0.95 })
  );
  var ring2 = new THREE.Mesh(
    new THREE.TorusGeometry(1.38, 0.06, 10, 48),
    new THREE.MeshBasicMaterial({ color: 0xbfe9ff, transparent: true, opacity: 0.7 })
  );
  var disc = new THREE.Mesh(new THREE.CircleGeometry(0.92, 32),
    new THREE.MeshBasicMaterial({ color: 0x67e8f9, transparent: true, opacity: 0.22, depthWrite: false }));
  var core = new THREE.Mesh(new THREE.CircleGeometry(0.45, 24),
    new THREE.MeshBasicMaterial({ color: 0xe8fbff, transparent: true, opacity: 0.85, depthWrite: false }));
  var pglow = new THREE.Sprite(new THREE.SpriteMaterial({
    map: softGlowTex, color: 0x67e8f9, transparent: true, opacity: 0.5, depthWrite: false, blending: THREE.AdditiveBlending
  }));
  pglow.scale.set(4.5, 4.5, 1);
  portal.add(ring); portal.add(ring2); portal.add(disc); portal.add(core); portal.add(pglow);
  var motes = [];
  for (var pti = 0; pti < 10; pti++) {
    var pmote = new THREE.Sprite(new THREE.SpriteMaterial({
      map: softGlowTex, color: pti % 2 ? 0xffffff : 0x67e8f9, transparent: true, opacity: 0.9, depthWrite: false, blending: THREE.AdditiveBlending
    }));
    var pms = 0.22 + (pti % 3) * 0.08;
    pmote.scale.set(pms, pms, 1);
    pmote.userData = { a: pti / 10 * Math.PI * 2, r: 1.25 + (pti % 3) * 0.22, sp: 1.6 + (pti % 4) * 0.5 };
    portal.add(pmote);
    motes.push(pmote);
  }
  portal.position.set(PORTAL_X, 3.4, 0);
  scene.add(portal);

  // Enemies: dark spiky orbs patrolling over the track. Touch = death.
  var enemies = [];
  function addEnemy(cx, range, topY, speed, phase) {
    var grp = new THREE.Group();
    var body = new THREE.Mesh(
      new THREE.IcosahedronGeometry(0.42, 0),
      new THREE.MeshLambertMaterial({ color: 0x1f2937, emissive: 0x0b1220 })
    );
    var eyeMat = new THREE.MeshPhongMaterial({ color: 0x3a0d0d, emissive: 0xcc2222, specular: 0xffffff, shininess: 120 });
    var e1 = new THREE.Mesh(new THREE.SphereGeometry(0.09, 8, 8), eyeMat);
    var e2 = e1.clone();
    e1.position.set(-0.15, 0.08, 0.36);
    e2.position.set(0.15, 0.08, 0.36);
    var eglow = new THREE.Sprite(new THREE.SpriteMaterial({
      map: softGlowTex, color: 0xff5555, transparent: true, opacity: 0.35, depthWrite: false, blending: THREE.AdditiveBlending
    }));
    eglow.scale.set(1.6, 1.6, 1);
    grp.add(body); grp.add(e1); grp.add(e2); grp.add(eglow);
    grp.position.set(cx, topY, 0);
    scene.add(grp);
    enemies.push({ grp: grp, cx: cx, range: range, y: topY, speed: speed, phase: phase || 0, body: body });
  }
  addEnemy(10.0, 0.5, 0.45, 1.3, 4.15);
  addEnemy(45.0, 0.5, 3.65, 1.8, 4.0);
  addEnemy(32.0, 0.8, 0.45, 1.5, 2.3);
  addEnemy(36.5, 0.6, 0.45, 1.9, 3.32);
  // Weaver: new enemy type — purple cyclops shard drifting on an ellipse
  // (horizontal + double-speed vertical sine) instead of a flat patrol.
  function addWeaver(cx, baseY, ampX, ampY, speed, phase) {
    var grp = new THREE.Group();
    var shard = new THREE.Mesh(
      new THREE.OctahedronGeometry(0.42, 0),
      new THREE.MeshLambertMaterial({ color: 0x7c3aed, emissive: 0x2e1065 })
    );
    shard.scale.set(0.8, 1.5, 0.8);
    var wEye = new THREE.Mesh(new THREE.SphereGeometry(0.13, 10, 10),
      new THREE.MeshPhongMaterial({ color: 0xffffff, emissive: 0xf0abfc, specular: 0xffffff, shininess: 120 }));
    wEye.position.set(0, 0.18, 0.3);
    var wPupil = new THREE.Mesh(new THREE.SphereGeometry(0.06, 8, 8),
      new THREE.MeshBasicMaterial({ color: 0x1e1b4b }));
    wPupil.position.set(0, 0.18, 0.41);
    var wglow = new THREE.Sprite(new THREE.SpriteMaterial({
      map: softGlowTex, color: 0xa855f7, transparent: true, opacity: 0.4, depthWrite: false, blending: THREE.AdditiveBlending
    }));
    wglow.scale.set(1.8, 2.2, 1);
    grp.add(shard); grp.add(wEye); grp.add(wPupil); grp.add(wglow);
    grp.position.set(cx, baseY, 0);
    scene.add(grp);
    enemies.push({ grp: grp, cx: cx, range: ampX, y: baseY, speed: speed, phase: phase || 0, body: shard, weaver: true, ampY: ampY });
  }
  // Finale: a second flyer mops up the long enemy-free stretch before the
  // portal guard — it hovers just above the cloud, so you have to hop it.
  // It cruises above head height: safe to run under, lethal if you jump into it.
  addWeaver(62.4, 3.9, 0.45, 0.28, 1.6, 0.4);
  addWeaver(68.2, 2.71, 0.25, 0.06, 1.4, 1.02);

  // Player: Discord Clyde 3D model (CC-BY CarchrisBox, Sketchfab
  // "New discord logo"), with a procedural lookalike as fallback.
  var player = new THREE.Group();
  var fallback = new THREE.Group();
  var head = new THREE.Mesh(
    new THREE.SphereGeometry(0.52, 20, 16),
    new THREE.MeshLambertMaterial({ color: 0xffffff, emissive: 0x1c2a44 })
  );
  head.scale.set(1.25, 0.95, 0.9);
  var clydeEyeMat = new THREE.MeshPhongMaterial({ color: 0x111827, emissive: 0x1f2937, specular: 0xffffff, shininess: 90 });
  var clydeEyeGeo = new THREE.SphereGeometry(0.1, 12, 12);
  var eyeL = new THREE.Mesh(clydeEyeGeo, clydeEyeMat);
  var eyeR = new THREE.Mesh(clydeEyeGeo, clydeEyeMat);
  eyeL.position.set(-0.23, 0.08, 0.41);
  eyeR.position.set(0.23, 0.08, 0.41);
  eyeL.scale.z = 0.55;
  eyeR.scale.z = 0.55;
  fallback.add(head); fallback.add(eyeL); fallback.add(eyeR);
  var gripGeo = new THREE.SphereGeometry(0.22, 12, 10);
  var gripL = new THREE.Mesh(gripGeo, head.material);
  var gripR = new THREE.Mesh(gripGeo, head.material);
  gripL.position.set(-0.62, -0.28, 0);
  gripR.position.set(0.62, -0.28, 0);
  gripL.scale.set(0.8, 1.3, 0.7);
  gripR.scale.set(0.8, 1.3, 0.7);
  gripL.rotation.z = 0.3;
  gripR.rotation.z = -0.3;
  fallback.add(gripL); fallback.add(gripR);
  player.add(fallback);
  if (THREE.GLTFLoader) {
    new THREE.GLTFLoader().load('assets/models/discord-logo.glb', function (gltf) {
      var wrap = new THREE.Group();
      var model = gltf.scene;
      var bbox = new THREE.Box3().setFromObject(model);
      var bsize = new THREE.Vector3(); bbox.getSize(bsize);
      var bcenter = new THREE.Vector3(); bbox.getCenter(bcenter);
      console.log('[hero-game] Clyde model size', bsize.x.toFixed(2), bsize.y.toFixed(2), bsize.z.toFixed(2));
      player.userData.modelLoaded = true;
      var sc = 1.0 / Math.max(bsize.x, bsize.y, bsize.z);
      model.scale.set(sc, sc, sc);
      model.position.set(-bcenter.x * sc, -bcenter.y * sc, -bcenter.z * sc);
      model.traverse(function (o) {
        if (o.isMesh) {
          o.material = new THREE.MeshLambertMaterial({ color: 0xffffff, emissive: 0x1c2a44 });
        }
      });
      wrap.add(model);
      player.remove(fallback);
      player.add(wrap);
    });
  }
  var playerGlow = new THREE.Sprite(new THREE.SpriteMaterial({
    map: softGlowTex, color: 0xbfd4ee, transparent: true, opacity: 0.5, depthWrite: false, blending: THREE.AdditiveBlending
  }));
  playerGlow.scale.set(2.6, 2.6, 1);
  player.add(playerGlow);
  player.rotation.y = 0.4;
  scene.add(player);
  // Blob shadow on the clouds below (landing aid when jumping).
  var shadow = new THREE.Sprite(new THREE.SpriteMaterial({
    map: shadowTex, transparent: true, opacity: 0.45, depthWrite: false
  }));
  shadow.scale.set(1.15, 0.62, 1);
  shadow.position.set(-3, 0.1, -0.1);
  scene.add(shadow);

  // Parallax backdrop + dust.
  [[-8, 1.5, -18, 5], [14, 3, -24, 7], [36, 2, -20, 6], [58, 3.5, -26, 8], [78, 2, -20, 6]].forEach(function (d) {
    var m = new THREE.Mesh(new THREE.BoxGeometry(d[3], d[3] * 0.6, 1),
      new THREE.MeshBasicMaterial({ color: 0x182435, transparent: true, opacity: 0.85 }));
    m.position.set(d[0], d[1], d[2]);
    scene.add(m);
  });
  var dust = (function () {
    var n = 70, pos = new Float32Array(n * 3);
    for (var i = 0; i < n; i++) {
      pos[i * 3] = R() * 96 - 8;
      pos[i * 3 + 1] = R() * 7 - 1;
      pos[i * 3 + 2] = -R() * 14;
    }
    var g = new THREE.BufferGeometry();
    g.setAttribute('position', new THREE.BufferAttribute(pos, 3));
    var pts = new THREE.Points(g, new THREE.PointsMaterial({
      color: 0x8fa0b8, size: 0.35, map: softGlowTex, transparent: true, opacity: 0.5, depthWrite: false
    }));
    scene.add(pts);
    return pts;
  })();

  var sparkGeo = new THREE.BoxGeometry(0.1, 0.1, 0.1);
  function burst(x, y, z, color) {
    for (var i = 0; i < 8; i++) {
      var s = new THREE.Mesh(sparkGeo, new THREE.MeshBasicMaterial({
        color: color || 0xf6c76b, transparent: true
      }));
      s.position.set(x, y, z);
      s.userData.v = new THREE.Vector3(
        (Math.random() * 2 - 1) * 2.4, Math.random() * 2.6 + 0.8, (Math.random() - 0.5) * 1.2);
      s.userData.life = 0.55;
      scene.add(s);
      sparks.push(s);
    }
  }
  var sparks = [];

  // ---- Game state ----
  var GRAV = -26, RUN = 4.4, JUMP_V = 10.2, COYOTE = 0.1;
  var state = 'attract'; // attract | playing | ready | finished
  var p = { x: -3, y: 0.01, vy: 0, grounded: true, coyote: 0, buffer: 0, squash: 0, stretch: 0 };
  var time = 0, got = 0, total = diamonds.length;
  // Run-local clock (runT) reset on every start for deterministic speedruns.
  var runT = 0;
  var best = null;
  try { best = JSON.parse(localStorage.getItem('hero-game-best') || 'null'); } catch (e) { best = null; }
  // Legacy saves stored a bare number; current shape is { time, points }.
  if (best != null && (typeof best !== 'object' || typeof best.time !== 'number' || typeof best.points !== 'number')) {
    best = null;
  }

  var MAXDEATHS = 15;
  var deaths = 0;
  function fmt(t) { return t.toFixed(2) + 's'; }
  function refreshHud() {
    if (timeEl) timeEl.textContent = fmt(time);
    if (gemsEl) gemsEl.textContent = '\u25C6 ' + got + '/' + total;
    if (bestEl) bestEl.textContent = best != null ? 'best ' + fmt(best.time) + ' \u00B7 ' + best.points + ' pts' : '';
    if (livesEl) livesEl.textContent = '\u2764 ' + Math.max(0, MAXDEATHS - deaths);
    if (progEl) progEl.style.width = Math.max(0, Math.min(100, (p.x + 3) / (PORTAL_X + 3) * 100)).toFixed(1) + '%';
  }
  function setHint(t) { if (hintEl) hintEl.textContent = t; }
  refreshHud();

  function reset() {
    p.x = -3; p.y = 0.01; p.vy = 0; p.grounded = true;
    p.coyote = 0; p.buffer = 0; p.squash = 0; p.stretch = 0;
    time = 0; got = 0;
    diamonds.forEach(function (d) { d.userData.taken = false; d.visible = true; });
    refreshHud();
  }

  function toLanding() {
    reset();
    deaths = 0;
    sparks.forEach(function (s) { scene.remove(s); });
    sparks = [];
    player.rotation.set(0, 0.4, 0);
    state = 'attract';
    hero.classList.remove('is-playing');
    if (recapEl) recapEl.classList.remove('is-open');
    setHint('Click to play');
    refreshHud();
  }

  function start() {
    FX.ensure();
    if (state === 'attract') deaths = 0;
    reset();
    runT = 0;
    sparks.forEach(function (s) { scene.remove(s); });
    sparks = [];
    player.rotation.set(0, 0.4, 0);
    state = 'playing';
    hero.classList.add('is-playing');
    if (recapEl) recapEl.classList.remove('is-open');
    setHint('Space / click to jump');
    FX.go();
    if (!Music.on) Music.toggle(true);
  }

  function die() {
    FX.die();
    burst(p.x, p.y, 0, 0xff6666);
    deaths++;
    reset();
    if (deaths >= MAXDEATHS) { toLanding(); return; }
    state = 'ready';
    setHint('Space to retry \u00B7 ' + (MAXDEATHS - deaths) + ' left');
  }

  function points() {
    return Math.max(50, Math.round(1200 + got * 150 - time * 10));
  }

  function finish() {
    state = 'finished';
    setHint('');
    var pts = points();
    var isBest = best == null || pts > best.points;
    if (isBest) {
      best = { time: +time.toFixed(2), points: pts };
      try { localStorage.setItem('hero-game-best', JSON.stringify(best)); } catch (e) {}
    }
    FX.win();
    burst(PORTAL_X, 3.2, 0, 0x67e8f9);
    refreshHud();
    setTimeout(function () {
      if (state !== 'finished') return;
      var rt = document.getElementById('recap-time');
      var rg = document.getElementById('recap-gems');
      var rp = document.getElementById('recap-points');
      var rb = document.getElementById('recap-best');
      if (rt) rt.textContent = fmt(time);
      if (rg) rg.textContent = '\u25C6 ' + got + '/' + total;
      if (rp) rp.textContent = pts + ' pts';
      if (rb) rb.textContent = isBest ? 'New best!' : ('Best ' + best.points + ' pts');
      if (recapEl) recapEl.classList.add('is-open');
    }, 900);
  }

  function doJump() {
    if (p.grounded || p.coyote > 0) {
      p.vy = JUMP_V; p.grounded = false; p.coyote = 0; p.squash = 0; p.stretch = 1;
      FX.jump();
      burst(p.x, p.y - 0.3, 0, 0xbfd4ee);
    } else {
      p.buffer = 0.12;
    }
  }
  // A press while airborne only buffers a landing jump; it never re-arms by
  // itself, so the character cannot hop on its own.


  // Input.
  window.addEventListener('keydown', function (e) {
    if (e.code !== 'Space' && e.code !== 'ArrowUp') return;
    if (e.target && /^(INPUT|TEXTAREA|SELECT)$/.test(e.target.tagName)) return;
    e.preventDefault();
    // Keyboard auto-repeat: holding the key must not keep firing jumps.
    if (e.repeat) return;
    if (state !== 'playing') {
      if (e.target === document.body) {
        if (state === 'finished') {
          if (recapEl && recapEl.classList.contains('is-open')) toLanding();
        } else start();
      }
      return;
    }
    doJump();
  });
  function heroPress(e) {
    if (e.target.closest && e.target.closest('a,button')) return;
    FX.ensure();
    if (state === 'playing') doJump();
    else if (state === 'finished') {
      if (recapEl && recapEl.classList.contains('is-open')) toLanding();
    }
    else start();
  }
  hero.addEventListener('mousedown', heroPress);
  hero.addEventListener('touchstart', heroPress, { passive: true });
  // Variable jump height: releasing early cuts the ascent (speedrun-friendly).
  function cutJump() {
    if (state !== 'playing' || p.grounded) return;
    if (p.vy > 2.5) p.vy *= 0.5;
  }
  window.addEventListener('keyup', function (e) {
    if (e.code !== 'Space' && e.code !== 'ArrowUp') return;
    cutJump();
  });
  hero.addEventListener('mouseup', cutJump);
  hero.addEventListener('touchend', cutJump);

  function topAt(i) { return platforms[i].y + 0.3 + 0.31; }

  function step(dt, t) {
    // Gameplay-relevant motion runs on the run clock (deterministic per run);
    // ambient attract/ready motion runs on wall time.
    var at = (state === 'playing' || state === 'finished') ? runT : t;
    if (state === 'playing') {
      time += dt;
      runT += dt;
      p.x += RUN * dt;
      if (!p.grounded) {
        var prevY = p.y;
        p.vy += GRAV * dt;
        p.y += p.vy * dt;
        if (p.vy < 0) {
          for (var i = 0; i < platforms.length; i++) {
            var q = platforms[i], hw = q.hw + 0.25;
            if (Math.abs(p.x - q.x) < hw) {
              var top = topAt(i);
              if (p.y <= top + 0.1 && p.y > top - 0.7 && prevY >= top) {
                p.y = top; p.vy = 0; p.grounded = true; p.squash = 0.32;
                burst(p.x, top, 0, 0xe8edf1);
                if (p.buffer > 0) { p.buffer = 0; doJump(); }
                break;
              }
            }
          }
        }
        if (p.y < -5) { die(); return; }
      } else {
        var supported = false;
        for (var j = 0; j < platforms.length; j++) {
          var r = platforms[j];
          if (Math.abs(p.x - r.x) < r.hw + 0.25 &&
              Math.abs(p.y - (r.y + 0.3 + 0.31)) < 0.15) { supported = true; break; }
        }
        if (!supported && p.x > -6.5) { p.grounded = false; p.coyote = COYOTE; }
      }
      p.coyote = Math.max(0, p.coyote - dt);
      p.buffer = Math.max(0, p.buffer - dt);

      diamonds.forEach(function (d) {
        if (d.userData.taken) return;
        var dx = d.position.x - p.x, dy = d.position.y - p.y;
        if (dx * dx + dy * dy < 0.8) {
          d.userData.taken = true; d.visible = false; got++;
          FX.gem(got);
          burst(d.position.x, d.position.y, 0, 0x67e8f9);
          refreshHud();
        }
      });

      // Enemies kill on touch.
      for (var k = 0; k < enemies.length; k++) {
        var ex = enemies[k].grp.position.x - p.x;
        var ey = enemies[k].grp.position.y - p.y;
        if (ex * ex + ey * ey < 0.4) { die(); return; }
      }

      if (Math.abs(p.x - PORTAL_X) < 0.9 && p.y > -0.5 && p.y < 3.4) finish();
      if (timeEl) timeEl.textContent = fmt(time);
    } else if (state === 'attract' || state === 'ready') {
      p.y = 0.01 + Math.sin(t * 2.2) * 0.06;
    }

    // Ambient motion (always alive behind content).
    diamonds.forEach(function (d) {
      if (d.userData.taken) return;
      d.rotation.y += dt * 2.6;
      d.position.y = d.userData.baseY + Math.sin(at * 3 + d.userData.phase) * 0.08;
    });
    enemies.forEach(function (en) {
      if (en.weaver) {
        en.grp.position.x = en.cx + Math.sin(at * en.speed + en.phase) * en.range;
        en.grp.position.y = en.y + Math.sin(at * en.speed * 2 + en.phase * 1.7) * en.ampY;
      } else {
        en.grp.position.x = en.cx + Math.sin(at * en.speed + en.phase) * en.range;
        en.grp.position.y = en.y + Math.sin(at * 2.4 + en.phase) * 0.12;
      }
      var wantFace = p.x < en.grp.position.x ? -0.5 : 0.5;
      en.grp.rotation.y += (wantFace - en.grp.rotation.y) * Math.min(1, dt * 6);
      if (en.weaver) {
        en.body.rotation.z = Math.sin(at * 3.1 + en.phase) * 0.25;
        en.body.rotation.y += dt * 0.8;
      } else {
        en.body.rotation.y += dt * 1.5;
        en.body.rotation.x += dt * 0.6;
      }
    });
    var pulse = 0.5 + 0.5 * Math.sin(at * (state === 'playing' ? 6 : 2.2));
    ring.material.opacity = 0.7 + pulse * 0.3;
    disc.material.opacity = 0.15 + pulse * 0.2;
    pglow.material.opacity = 0.3 + pulse * 0.3;
    portal.rotation.z += dt * (state === 'playing' ? 1.2 : 0.15);
    ring2.rotation.z -= dt * (state === 'playing' ? 2.2 : 0.4);
    ring2.material.opacity = 0.45 + pulse * 0.35;
    core.material.opacity = 0.55 + pulse * 0.4;
    var cores = 1 + pulse * 0.12;
    core.scale.set(cores, cores, 1);
    for (var mti = 0; mti < motes.length; mti++) {
      var mmo = motes[mti];
      var maa = mmo.userData.a + at * mmo.userData.sp;
      mmo.position.set(Math.cos(maa) * mmo.userData.r, Math.sin(maa) * mmo.userData.r * 1.15, 0.1);
    }

    sparks = sparks.filter(function (s) {
      s.userData.life -= dt;
      s.position.addScaledVector(s.userData.v, dt);
      s.userData.v.y -= 7 * dt;
      s.material.opacity = Math.max(0, s.userData.life * 2);
      if (s.userData.life <= 0) { scene.remove(s); return false; }
      return true;
    });

    p.squash = Math.max(0, p.squash - dt * 2.4);
    p.stretch = Math.max(0, p.stretch - dt * 2.6);
    var airStretch = p.grounded ? 0 : Math.min(0.16, Math.abs(p.vy) * 0.01);
    // Disney blob: takeoff stretch + landing squash, both with wobble overshoot.
    var wob = Math.sin(p.stretch * 12.0) * p.stretch * 0.10;
    var wob2 = Math.sin(p.squash * 14.0) * p.squash * 0.10;
    var sY = 1 - p.squash * 0.5 + airStretch + p.stretch * 0.42 + wob + wob2;
    var sX = 1 + p.squash * 0.32 - airStretch * 0.6 - p.stretch * 0.26 - wob * 0.6 - wob2 * 0.6;
    player.scale.set(sX, sY, 1);
    player.position.set(p.x, p.y + 0.45, 0);
    // Project the blob shadow on the nearest cloud below the player.
    var shTop = -10, shX = p.x;
    for (var shi = 0; shi < platforms.length; shi++) {
      var shq = platforms[shi];
      if (Math.abs(p.x - shq.x) < shq.hw + 0.3) {
        var sht = topAt(shi);
        if (sht <= p.y + 0.12 && sht > shTop) {
          shTop = sht;
          shX = Math.max(shq.x - shq.hw, Math.min(shq.x + shq.hw, p.x));
        }
      }
    }
    var shD = p.y - shTop;
    if (shTop > -9 && shD < 7) {
      shadow.visible = true;
      // In front of the player glow so the additive halo can't wash it out.
      shadow.position.set(shX, shTop + 0.07, 0.2);
      shadow.material.opacity = Math.max(0.07, 0.3 - shD * 0.045);
      var shS = Math.max(0.6, 1.15 - shD * 0.05);
      shadow.scale.set(shS, shS * 0.54, 1);
    } else {
      shadow.visible = false;
    }

    var tx = (state === 'attract' || state === 'ready') ? -1 + Math.sin(t * 0.4) * 0.8 : p.x + 2.4;
    camera.position.x += (tx - camera.position.x) * Math.min(1, dt * 5);
    camera.position.y += (2.6 - camera.position.y) * Math.min(1, dt * 3.5);
    camera.lookAt(camera.position.x + 0.4, 0.7, 0);

    dust.rotation.y += dt * 0.008;
    Music.tick();
  }

  function resize() {
    var w = canvas.clientWidth || 1, h = canvas.clientHeight || 1;
    renderer.setSize(w, h, false);
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
  }
  window.addEventListener('resize', resize);
  resize();

  var clock = new THREE.Clock();
  var elapsed = 0;
  var running = true, raf = 0;
  function frame() {
    if (!running) return;
    var dt = Math.min(clock.getDelta(), 0.05);
    elapsed += dt;
    step(dt, elapsed);
    renderer.render(scene, camera);
    raf = requestAnimationFrame(frame);
  }

  if (reduceMotion) {
    step(0.016, 0.016);
    renderer.render(scene, camera);
    setHint('');
    var hud = document.querySelector('.ds-hero__hud');
    if (hud) hud.style.display = 'none';
  } else {
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (entries) {
        var vis = entries[0].isIntersecting && !document.hidden;
        if (vis && !running) { running = true; clock.getDelta(); raf = requestAnimationFrame(frame); }
        else if (!vis && running) { running = false; cancelAnimationFrame(raf); }
      }, { threshold: 0.05 }).observe(hero);
    }
    document.addEventListener('visibilitychange', function () {
      if (document.hidden && running) { running = false; cancelAnimationFrame(raf); }
      else if (!document.hidden && !running) { running = true; clock.getDelta(); raf = requestAnimationFrame(frame); }
    });
    raf = requestAnimationFrame(frame);
  }

  // Minimal hook for automated testing.
  window.heroGame = {
    start: start,
    jump: doJump,
    tick: function (n, silent) {
      for (var i = 0; i < (n || 1); i++) { elapsed += 1 / 60; step(1 / 60, elapsed); }
      if (!silent) renderer.render(scene, camera);
    },
    info: function () { return { state: state, x: +p.x.toFixed(2), y: +p.y.toFixed(2), time: +time.toFixed(2), got: got, grounded: p.grounded, model: !!player.userData.modelLoaded, enemies: enemies.map(function (en) { return +en.grp.position.x.toFixed(2); }), enemyY: enemies.map(function (en) { return +en.grp.position.y.toFixed(2); }), shadow: shadow.visible ? [+shadow.position.x.toFixed(2), +shadow.position.y.toFixed(2), +shadow.material.opacity.toFixed(2)] : null }; }
  };
})();
