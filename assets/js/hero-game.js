/* Aether Run — endless steampunk cloud-runner for the landing hero.
 *
 * 3D side-view presentation with a tilted camera (so the cloud road, the drone
 * and the background clouds all read as real 3D), procedural endless level
 * generation and a difficulty ramp that never truly stops.
 *
 * Everything is generated at runtime: no external models or textures.
 * One-button controls: Space / ArrowUp / W / click / tap = jump.
 */
(function () {
  var canvas = document.getElementById('hero-game');
  var hero = document.querySelector('.ds-hero');
  if (!canvas || !hero || !window.THREE) return;

  // ---------------------------------------------------------------- DOM ----
  var hintEl = document.getElementById('hero-hint');
  var scoreEl = document.getElementById('hero-score');
  var timeEl = document.getElementById('hero-time');
  var distanceEl = document.getElementById('hero-distance');
  var gemsEl = document.getElementById('hero-gems');
  var bestEl = document.getElementById('hero-best');
  var tierLabelEl = document.getElementById('hero-tier-label');
  var speedEl = document.getElementById('hero-speed');
  var tierFillEl = document.getElementById('hero-tier-fill');
  var progEl = document.getElementById('hero-progress-fill');
  var eventEl = document.getElementById('hero-event');
  var recapEl = document.getElementById('hero-recap');
  var restartBtn = document.getElementById('hero-restart');
  var leaderboardBody = document.getElementById('hero-leaderboard-body');
  var scoreForm = document.getElementById('hero-score-form');
  var playerInput = document.getElementById('hero-player-name');
  var saveScoreBtn = document.getElementById('hero-score-save');
  var scoreStatusEl = document.getElementById('hero-score-status');
  var soundBtn = document.getElementById('hero-sound');

  var heroConfig = window.HERO_GAME_CONFIG || {};
  var scoresUrl = heroConfig.scoresUrl || '/hero-scores.php';
  var I18N = heroConfig.i18n || {};
  function tr(key, params) {
    var s = I18N[key] != null ? I18N[key] : key;
    if (params) for (var k in params) s = s.split('{' + k + '}').join(params[k]);
    return s;
  }

  var savedPlayerName = '';
  try { savedPlayerName = localStorage.getItem('hero-game-player') || ''; } catch (e) { savedPlayerName = ''; }
  var submittingScore = false;
  var reduceMotion = window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // ------------------------------------------------------------- RENDER ----
  var renderer;
  try {
    renderer = new THREE.WebGLRenderer({
      canvas: canvas, alpha: true, antialias: true, powerPreference: 'high-performance'
    });
  } catch (e) { return; }
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.35));
  renderer.outputEncoding = THREE.sRGBEncoding;
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 1.08;

  var scene = new THREE.Scene();
  var camera = new THREE.PerspectiveCamera(40, 1, 0.1, 260);
  var viewZ = 12;

  scene.add(new THREE.HemisphereLight(0x9dbfe8, 0x24180f, 0.82));
  var sunLight = new THREE.DirectionalLight(0xffd7a0, 1.55);
  sunLight.position.set(-7, 11, -9);
  scene.add(sunLight);
  var fillLight = new THREE.DirectionalLight(0x8fc9ff, 0.55);
  fillLight.position.set(7, 4, 10);
  scene.add(fillLight);

  // --------------------------------------------------------------- AUDIO ---
  var FX = {
    ctx: null, master: null, engineOsc: null, engineGain: null,
    ensure: function () {
      if (!this.ctx) {
        var AC = window.AudioContext || window.webkitAudioContext;
        if (!AC) return;
        this.ctx = new AC();
        this.master = this.ctx.createGain();
        this.master.gain.value = 0.16;
        this.master.connect(this.ctx.destination);
      }
      if (this.ctx.state === 'suspended') this.ctx.resume();
    },
    tone: function (freq, dur, type, slideTo, delay, amp) {
      if (!this.ctx) return;
      var t0 = this.ctx.currentTime + (delay || 0);
      var o = this.ctx.createOscillator(), g = this.ctx.createGain();
      o.type = type || 'sine';
      o.frequency.setValueAtTime(freq, t0);
      if (slideTo) o.frequency.exponentialRampToValueAtTime(slideTo, t0 + dur);
      g.gain.setValueAtTime(0.0001, t0);
      g.gain.exponentialRampToValueAtTime(amp || 0.55, t0 + 0.014);
      g.gain.exponentialRampToValueAtTime(0.0001, t0 + dur);
      o.connect(g); g.connect(this.master);
      o.start(t0); o.stop(t0 + dur + 0.05);
    },
    jump: function () {
      this.tone(260, 0.16, 'square', 760);
      this.tone(900, 0.22, 'sine', 1500, 0.01, 0.12);
    },
    gem: function (n) { this.tone(720 + (n % 12) * 55, 0.13, 'triangle', 1680); },
    die: function () {
      this.tone(210, 0.45, 'sawtooth', 48, 0, 0.6);
      this.tone(120, 0.6, 'square', 32, 0.03, 0.22);
    },
    go: function () {
      this.tone(392, 0.1, 'square', 784);
      this.tone(784, 0.16, 'triangle', 1175, 0.09, 0.35);
    },
    tier: function () {
      var seq = [523, 659, 880];
      for (var i = 0; i < seq.length; i++) this.tone(seq[i], 0.15, 'triangle', null, i * 0.07, 0.3);
    },
    win: function () {
      var seq = [523, 659, 784, 1047];
      for (var i = 0; i < seq.length; i++) this.tone(seq[i], 0.18, 'triangle', null, i * 0.09, 0.36);
    },
    engine: function (on, speed) {
      if (!this.ctx || !this.master) return;
      if (!this.engineOsc) {
        this.engineOsc = this.ctx.createOscillator();
        this.engineGain = this.ctx.createGain();
        this.engineOsc.type = 'sawtooth';
        this.engineOsc.frequency.value = 48;
        this.engineGain.gain.value = 0;
        var lp = this.ctx.createBiquadFilter();
        lp.type = 'lowpass';
        lp.frequency.value = 180;
        this.engineOsc.connect(lp); lp.connect(this.engineGain); this.engineGain.connect(this.master);
        this.engineOsc.start();
      }
      if (on) {
        this.engineOsc.frequency.setTargetAtTime(42 + Math.min(speed, 10) * 2.4, this.ctx.currentTime, 0.25);
        this.engineGain.gain.setTargetAtTime(0.045, this.ctx.currentTime, 0.3);
      } else {
        this.engineGain.gain.setTargetAtTime(0, this.ctx.currentTime, 0.2);
      }
    }
  };

  // Tiny looping soundtrack (synth only). Off until the sound button is used.
  var Music = {
    on: false, bus: null, next: 0, step: 0,
    pattern: [0, 2, 1, 3, 4, 3, 2, 1],
    notes: [220.00, 261.63, 293.66, 329.63, 392.00],
    bass: 110.00, stepLen: 0.3, vol: 0.72,
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
      if (this.on) this.next = Math.max(this.next, FX.ctx ? FX.ctx.currentTime + 0.05 : 0);
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
        this.note(f, at, 0.26, 'triangle', 0.16);
        if (i === 0 || i === 4) this.note(this.bass, at, 0.55, 'sine', 0.2);
        if (i === 6) this.note(f * 2, at, 0.16, 'sine', 0.07);
        this.step++;
        this.next += this.stepLen;
      }
    }
  };
  function syncSoundButton() {
    if (!soundBtn) return;
    soundBtn.textContent = Music.on ? '\u266A' : '\u266A\u0338';
    soundBtn.setAttribute('aria-pressed', Music.on ? 'true' : 'false');
    soundBtn.title = Music.on ? tr('hero_game_music_on') : tr('hero_game_music_off');
  }
  if (soundBtn) {
    syncSoundButton();
    soundBtn.addEventListener('click', function (e) {
      e.preventDefault(); e.stopPropagation();
      Music.toggle();
    });
  }
  if (restartBtn) {
    restartBtn.addEventListener('click', function (e) {
      e.preventDefault(); e.stopPropagation();
      start();
    });
  }
  if (scoreForm) {
    scoreForm.addEventListener('submit', function (e) { e.preventDefault(); submitScore(); });
  }

  // ------------------------------------------------------------- TEXTURES --
  function glowTexture(inner, outer) {
    var c = document.createElement('canvas');
    c.width = c.height = 64;
    var g = c.getContext('2d');
    var grad = g.createRadialGradient(32, 32, 1, 32, 32, 32);
    grad.addColorStop(0, inner);
    grad.addColorStop(1, outer);
    g.fillStyle = grad;
    g.fillRect(0, 0, 64, 64);
    var tex = new THREE.CanvasTexture(c);
    tex.minFilter = THREE.LinearFilter;
    tex.generateMipmaps = false;
    return tex;
  }
  var softGlowTex = glowTexture('rgba(255,255,255,0.95)', 'rgba(255,255,255,0)');
  var shadowTex = glowTexture('rgba(0,0,0,0.9)', 'rgba(0,0,0,0)');

  // -------------------------------------------------------------- SHADERS --
  var NOISE_GLSL = [
    'float hash21(vec2 p){',
    '  p = fract(p * vec2(123.34, 456.21));',
    '  p += dot(p, p + 45.32);',
    '  return fract(p.x * p.y);',
    '}',
    'float noise2(vec2 p){',
    '  vec2 i = floor(p); vec2 f = fract(p);',
    '  f = f * f * (3.0 - 2.0 * f);',
    '  float a = hash21(i);',
    '  float b = hash21(i + vec2(1.0, 0.0));',
    '  float c = hash21(i + vec2(0.0, 1.0));',
    '  float d = hash21(i + vec2(1.0, 1.0));',
    '  return mix(mix(a, b, f.x), mix(c, d, f.x), f.y);',
    '}',
    'float fbm2(vec2 p){',
    '  float v = 0.0; float a = 0.5;',
    '  for (int i = 0; i < 5; i++){',
    '    v += a * noise2(p);',
    '    p = p * 2.03 + vec2(11.7, 7.3);',
    '    a *= 0.5;',
    '  }',
    '  return v;',
    '}',
    'float hash31(vec3 p){',
    '  p = fract(p * 0.3183099 + vec3(0.1, 0.2, 0.3));',
    '  p *= 17.0;',
    '  return fract(p.x * p.y * p.z * (p.x + p.y + p.z));',
    '}',
    'float noise3(vec3 x){',
    '  vec3 i = floor(x); vec3 f = fract(x);',
    '  f = f * f * (3.0 - 2.0 * f);',
    '  float n000 = hash31(i + vec3(0.0, 0.0, 0.0));',
    '  float n100 = hash31(i + vec3(1.0, 0.0, 0.0));',
    '  float n010 = hash31(i + vec3(0.0, 1.0, 0.0));',
    '  float n110 = hash31(i + vec3(1.0, 1.0, 0.0));',
    '  float n001 = hash31(i + vec3(0.0, 0.0, 1.0));',
    '  float n101 = hash31(i + vec3(1.0, 0.0, 1.0));',
    '  float n011 = hash31(i + vec3(0.0, 1.0, 1.0));',
    '  float n111 = hash31(i + vec3(1.0, 1.0, 1.0));',
    '  return mix(mix(mix(n000, n100, f.x), mix(n010, n110, f.x), f.y),',
    '             mix(mix(n001, n101, f.x), mix(n011, n111, f.x), f.y), f.z);',
    '}',
    'float fbm3(vec3 p){',
    '  float v = 0.0; float a = 0.5;',
    '  for (int i = 0; i < 4; i++){',
    '    v += a * noise3(p);',
    '    p = p * 2.02 + vec3(7.1, 3.7, 9.3);',
    '    a *= 0.5;',
    '  }',
    '  return v;',
    '}'
  ].join('\n');

  var U_TIME = { value: 0 };
  var SUN_DIR = new THREE.Vector3(-0.42, 0.5, -0.76).normalize();

  // Sky dome: gradient + warm sun bloom + a faint high-altitude cloud band.
  var skyMaterial = new THREE.ShaderMaterial({
    uniforms: {
      uTime: U_TIME,
      uSunDir: { value: SUN_DIR },
      uTop: { value: new THREE.Color(0x050b18) },
      uMid: { value: new THREE.Color(0x172944) },
      uHorizon: { value: new THREE.Color(0x6b4a55) },
      uSunColor: { value: new THREE.Color(0xffb35e) }
    },
    vertexShader: [
      'varying vec3 vDir;',
      'void main(){',
      '  vDir = normalize(position);',
      '  gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);',
      '}'
    ].join('\n'),
    fragmentShader: NOISE_GLSL + '\n' + [
      'uniform float uTime;',
      'uniform vec3 uSunDir, uTop, uMid, uHorizon, uSunColor;',
      'varying vec3 vDir;',
      'void main(){',
      '  float h = clamp(vDir.y * 0.5 + 0.5, 0.0, 1.0);',
      '  vec3 col = mix(uHorizon, uMid, smoothstep(0.27, 0.62, h));',
      '  col = mix(col, uTop, smoothstep(0.56, 1.0, h));',
      '  float sd = max(dot(normalize(vDir), normalize(uSunDir)), 0.0);',
      '  col += uSunColor * pow(sd, 240.0) * 1.9;',
      '  col += uSunColor * pow(sd, 7.0) * 0.16;',
      '  float band = fbm2(vec2(atan(vDir.z, vDir.x) * 2.4, vDir.y * 7.5 + uTime * 0.003));',
      '  col += vec3(0.07, 0.12, 0.18) * smoothstep(0.58, 0.95, band) * smoothstep(0.03, 0.55, h);',
      '  gl_FragColor = vec4(col, 1.0);',
      '}'
    ].join('\n'),
    side: THREE.BackSide,
    depthWrite: false
  });
  var sky = new THREE.Mesh(new THREE.SphereGeometry(150, 32, 18), skyMaterial);
  sky.renderOrder = -1000;
  scene.add(sky);

  // Far cloud sea below the road: huge shader plane, fbm volume + sun tint.
  var deckMaterial = new THREE.ShaderMaterial({
    uniforms: { uTime: U_TIME },
    vertexShader: [
      'varying vec2 vUv; varying vec3 vWorld;',
      'void main(){',
      '  vUv = uv;',
      '  vWorld = (modelMatrix * vec4(position, 1.0)).xyz;',
      '  gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);',
      '}'
    ].join('\n'),
    fragmentShader: NOISE_GLSL + '\n' + [
      'uniform float uTime;',
      'varying vec2 vUv; varying vec3 vWorld;',
      'void main(){',
      '  vec2 uv = vWorld.xz * 0.052;',
      '  uv.x += uTime * 0.004;',
      '  float n = fbm2(uv);',
      '  float n2 = fbm2(uv * 2.25 + vec2(3.1, 7.7) - uTime * 0.002);',
      '  float cloud = smoothstep(0.40, 0.78, n * 0.62 + n2 * 0.55);',
      '  vec3 col = mix(vec3(0.11, 0.16, 0.29), vec3(0.96, 0.90, 0.83), cloud);',
      '  col = mix(col, vec3(1.0, 0.73, 0.42), pow(cloud, 3.0) * 0.26);',
      '  float edge = smoothstep(0.0, 0.12, vUv.x) * smoothstep(1.0, 0.88, vUv.x);',
      '  edge *= smoothstep(0.0, 0.12, vUv.y) * smoothstep(1.0, 0.88, vUv.y);',
      '  gl_FragColor = vec4(col, cloud * 0.96 * edge);',
      '}'
    ].join('\n'),
    transparent: true,
    depthWrite: false,
    side: THREE.DoubleSide
  });
  var deck = new THREE.Mesh(new THREE.PlaneGeometry(520, 520), deckMaterial);
  deck.rotation.x = -Math.PI / 2;
  deck.position.set(0, -11, -26);
  deck.renderOrder = -60;
  scene.add(deck);

  // Volumetric-looking billboard clouds (shared shader; pass a few big banks).
  var billboardVert = [
    'varying vec2 vUv; varying vec3 vWorld;',
    'void main(){',
    '  vUv = uv;',
    '  vWorld = (modelMatrix * vec4(position, 1.0)).xyz;',
    '  gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);',
    '}'
  ].join('\n');

  function makeCloudMaterial(opacity, speed, tint) {
    return new THREE.ShaderMaterial({
      uniforms: {
        uTime: U_TIME,
        uOpacity: { value: opacity },
        uSpeed: { value: speed },
        uTint: { value: new THREE.Color(tint) }
      },
      vertexShader: billboardVert,
      fragmentShader: NOISE_GLSL + '\n' + [
        'uniform float uTime, uOpacity, uSpeed;',
        'uniform vec3 uTint;',
        'varying vec2 vUv; varying vec3 vWorld;',
        'void main(){',
        '  vec2 q = vUv * 2.0 - 1.0;',
        '  float r = length(q * vec2(1.0, 1.35));',
        '  vec2 np = q * 3.0 + vWorld.xy * 0.01 + vec2(uTime * uSpeed, 0.0);',
        '  float n = fbm2(np);',
        '  float n2 = fbm2(np * 2.1 + vec2(7.0, 3.0));',
        '  float shape = smoothstep(1.02, 0.16, r + (n - 0.5) * 0.62);',
        '  float alpha = shape * (0.42 + 0.58 * smoothstep(0.30, 0.82, n2)) * uOpacity;',
        '  float top = smoothstep(-0.15, 0.95, q.y + (n - 0.5) * 0.5);',
        '  vec3 col = mix(vec3(0.36, 0.48, 0.68), vec3(1.0, 0.94, 0.85), top);',
        '  col = mix(col, uTint, 0.32);',
        '  col += vec3(1.0, 0.72, 0.42) * pow(max(q.y * 0.5 + 0.5, 0.0), 6.0) * 0.20;',
        '  gl_FragColor = vec4(col, alpha);',
        '}'
      ].join('\n'),
      transparent: true,
      depthWrite: false,
      side: THREE.DoubleSide
    });
  }

  var bankFar = new THREE.Mesh(new THREE.PlaneGeometry(190, 58), makeCloudMaterial(0.75, 0.004, 0x8c6a63));
  bankFar.position.set(0, 6, -54);
  bankFar.renderOrder = -55;
  scene.add(bankFar);
  var bankMid = new THREE.Mesh(new THREE.PlaneGeometry(130, 36), makeCloudMaterial(0.9, 0.008, 0x9d7358));
  bankMid.position.set(0, 3.5, -36);
  bankMid.renderOrder = -54;
  scene.add(bankMid);

  var puffGeometry = new THREE.PlaneGeometry(1, 1);
  var puffMaterial = makeCloudMaterial(1.0, 0.012, 0x9d7b6a);

  // Cloud road shader: lit fbm volume with soft, misty edges.
  var platformVert = [
    'varying vec3 vPos; varying vec3 vNormal; varying vec3 vWorld;',
    'void main(){',
    '  vPos = position;',
    '  vNormal = normalize(mat3(modelMatrix) * normal);',
    '  vWorld = (modelMatrix * vec4(position, 1.0)).xyz;',
    '  gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);',
    '}'
  ].join('\n');
  function makePlatformMaterial(w, h, d) {
    return new THREE.ShaderMaterial({
      uniforms: {
        uTime: U_TIME,
        uSunDir: { value: SUN_DIR },
        uSize: { value: new THREE.Vector3(w, h, d) }
      },
      vertexShader: platformVert,
      fragmentShader: NOISE_GLSL + '\n' + [
        'uniform float uTime;',
        'uniform vec3 uSize, uSunDir;',
        'varying vec3 vPos; varying vec3 vNormal; varying vec3 vWorld;',
        'void main(){',
        '  float n = fbm3(vWorld * 0.50 + vec3(uTime * 0.020, 0.0, uTime * 0.010));',
        '  float n2 = fbm3(vWorld * 1.30 + vec3(-uTime * 0.028, 0.0, uTime * 0.012));',
        '  float yn = clamp(vPos.y / uSize.y + 0.5, 0.0, 1.0);',
        '  vec2 hx = abs(vPos.xz) / vec2(uSize.x * 0.5, uSize.z * 0.5);',
        '  float side = smoothstep(0.58, 1.0, max(hx.x, hx.y));',
        '  float alpha = (1.0 - side * 0.70) * (0.82 + 0.18 * yn);',
        '  alpha *= 0.92 + 0.08 * n;',
        '  float sun = max(dot(normalize(vNormal), normalize(uSunDir)), 0.0);',
        '  vec3 cool = vec3(0.34, 0.45, 0.65);',
        '  vec3 warm = vec3(0.99, 0.94, 0.87);',
        '  vec3 base = mix(cool, warm, clamp(yn * 0.70 + n * 0.45, 0.0, 1.0));',
        '  vec3 col = base * (0.55 + 0.55 * sun);',
        '  col += vec3(1.0, 0.72, 0.40) * pow(sun, 2.0) * 0.30;',
        '  col += vec3(0.40, 0.72, 1.0) * smoothstep(0.55, 0.90, n2) * 0.10;',
        '  col += vec3(1.0, 0.82, 0.52) * pow(max(vNormal.y, 0.0), 5.0) * 0.10;',
        '  gl_FragColor = vec4(col, clamp(alpha, 0.0, 1.0));',
        '}'
      ].join('\n'),
      transparent: true,
      depthWrite: true
    });
  }

  var mistMaterial = new THREE.ShaderMaterial({
    uniforms: { uTime: U_TIME },
    vertexShader: [
      'varying vec2 vUv;',
      'void main(){ vUv = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0); }'
    ].join('\n'),
    fragmentShader: NOISE_GLSL + '\n' + [
      'uniform float uTime;',
      'varying vec2 vUv;',
      'void main(){',
      '  vec2 p = vUv * 6.0; p.x += uTime * 0.03;',
      '  float n = fbm2(p);',
      '  float n2 = fbm2(p * 1.8 - uTime * 0.02);',
      '  float a = smoothstep(0.44, 0.86, n * 0.55 + n2 * 0.55);',
      '  a *= smoothstep(0.0, 0.18, vUv.x) * smoothstep(1.0, 0.82, vUv.x);',
      '  a *= smoothstep(0.0, 0.18, vUv.y) * smoothstep(1.0, 0.82, vUv.y);',
      '  gl_FragColor = vec4(vec3(1.0, 0.89, 0.74), a * 0.42);',
      '}'
    ].join('\n'),
    transparent: true,
    depthWrite: false,
    blending: THREE.AdditiveBlending
  });
  var mistGeometry = new THREE.PlaneGeometry(1, 1);

  // ------------------------------------------------------------ MATERIALS --
  var brassMat = new THREE.MeshPhongMaterial({ color: 0xb7813f, specular: 0xffe6b0, shininess: 92, emissive: 0x1b0d03 });
  var copperMat = new THREE.MeshPhongMaterial({ color: 0xc06634, specular: 0xffc084, shininess: 70, emissive: 0x200a02 });
  var darkBrassMat = new THREE.MeshPhongMaterial({ color: 0x6e4a22, specular: 0xd9a55d, shininess: 50, emissive: 0x0e0601 });
  var ironMat = new THREE.MeshPhongMaterial({ color: 0x323946, specular: 0x93a4bb, shininess: 34, emissive: 0x07090d });
  var glassMat = new THREE.MeshPhongMaterial({
    color: 0xa9efff, specular: 0xffffff, shininess: 160, emissive: 0x0b4660,
    transparent: true, opacity: 0.58, side: THREE.DoubleSide
  });
  var amberGlowMat = new THREE.MeshBasicMaterial({ color: 0xffc46a });
  var redGlowMat = new THREE.MeshBasicMaterial({ color: 0xff4538 });
  var cyanGlowMat = new THREE.MeshBasicMaterial({ color: 0x8ff6ff });
  var gearMat = new THREE.MeshPhongMaterial({ color: 0xd9a441, specular: 0xfff0bd, shininess: 130, emissive: 0x6b3707 });
  var trimMat = new THREE.MeshPhongMaterial({ color: 0x8a5a2a, specular: 0xffd489, shininess: 70, emissive: 0x2a1202 });

  var redGlowSprite = new THREE.SpriteMaterial({
    map: softGlowTex, color: 0xff5a3c, transparent: true, opacity: 0.6,
    depthWrite: false, blending: THREE.AdditiveBlending
  });
  var amberGlowSprite = new THREE.SpriteMaterial({
    map: softGlowTex, color: 0xffc46a, transparent: true, opacity: 0.58,
    depthWrite: false, blending: THREE.AdditiveBlending
  });
  var cyanGlowSprite = new THREE.SpriteMaterial({
    map: softGlowTex, color: 0x8ff6ff, transparent: true, opacity: 0.5,
    depthWrite: false, blending: THREE.AdditiveBlending
  });
  var whiteGlowSprite = new THREE.SpriteMaterial({
    map: softGlowTex, color: 0xfff4e0, transparent: true, opacity: 0.42,
    depthWrite: false, blending: THREE.AdditiveBlending
  });

  // ------------------------------------------------------------- PLAYER ----
  function buildDrone() {
    var root = new THREE.Group();
    var body = new THREE.Group();
    root.add(body);

    // Main brass hull.
    var hull = new THREE.Mesh(new THREE.SphereGeometry(0.43, 26, 20), brassMat);
    hull.scale.set(1.28, 0.82, 0.94);
    body.add(hull);

    // Hammered copper belly.
    var belly = new THREE.Mesh(new THREE.SphereGeometry(0.34, 22, 16), copperMat);
    belly.scale.set(1.08, 0.62, 0.78);
    belly.position.set(-0.06, -0.18, 0);
    body.add(belly);

    // Brass trim bands around the hull.
    var band = new THREE.Mesh(new THREE.TorusGeometry(0.37, 0.026, 8, 30), darkBrassMat);
    band.rotation.y = Math.PI / 2;
    band.position.x = -0.10;
    body.add(band);
    var band2 = band.clone();
    band2.position.x = 0.28;
    body.add(band2);

    // Nose cone + copper muzzle ring.
    var nose = new THREE.Mesh(new THREE.ConeGeometry(0.26, 0.52, 22), copperMat);
    nose.rotation.z = -Math.PI / 2;
    nose.position.set(0.62, 0.01, 0);
    body.add(nose);
    var muzzle = new THREE.Mesh(new THREE.TorusGeometry(0.18, 0.035, 8, 22), brassMat);
    muzzle.rotation.y = Math.PI / 2;
    muzzle.position.set(0.83, 0.01, 0);
    body.add(muzzle);

    // Canopy: glass bubble with a brass rim.
    var canopy = new THREE.Mesh(
      new THREE.SphereGeometry(0.29, 24, 16, 0, Math.PI * 2, 0, Math.PI * 0.54),
      glassMat
    );
    canopy.scale.set(1.18, 0.92, 0.92);
    canopy.position.set(0.20, 0.20, 0);
    body.add(canopy);
    var canopyRim = new THREE.Mesh(new THREE.TorusGeometry(0.32, 0.025, 7, 24), brassMat);
    canopyRim.rotation.x = Math.PI / 2;
    canopyRim.position.set(0.20, 0.19, 0);
    body.add(canopyRim);

    // Pilot optic inside the canopy.
    var optic = new THREE.Mesh(new THREE.SphereGeometry(0.075, 12, 10), cyanGlowMat);
    optic.scale.set(1, 1, 0.6);
    optic.position.set(0.34, 0.26, 0.055);
    body.add(optic);

    // Furnace cage + glowing core under the belly.
    var furnace = new THREE.Mesh(new THREE.CylinderGeometry(0.15, 0.13, 0.24, 14), ironMat);
    furnace.position.set(-0.08, -0.36, 0);
    body.add(furnace);
    var core = new THREE.Mesh(new THREE.SphereGeometry(0.11, 14, 10), amberGlowMat);
    core.position.set(-0.08, -0.39, 0);
    body.add(core);
    var coreGlow = new THREE.Sprite(new THREE.SpriteMaterial({
      map: softGlowTex, color: 0xffa63d, transparent: true, opacity: 0.75,
      depthWrite: false, blending: THREE.AdditiveBlending
    }));
    coreGlow.scale.set(1.15, 1.15, 1);
    coreGlow.position.copy(core.position);
    body.add(coreGlow);

    // Side propeller nacelles with protective rings.
    var props = [];
    [-1, 1].forEach(function (side) {
      var pod = new THREE.Mesh(new THREE.CylinderGeometry(0.095, 0.11, 0.34, 14), copperMat);
      pod.rotation.x = Math.PI / 2;
      pod.position.set(-0.08, 0.12, side * 0.50);
      body.add(pod);
      var arm = new THREE.Mesh(new THREE.BoxGeometry(0.5, 0.055, 0.07), darkBrassMat);
      arm.position.set(-0.08, 0.12, side * 0.33);
      body.add(arm);
      var guard = new THREE.Mesh(new THREE.TorusGeometry(0.25, 0.022, 6, 22), brassMat);
      guard.position.set(-0.08, 0.12, side * 0.50);
      body.add(guard);
      var prop = new THREE.Group();
      prop.position.set(-0.08, 0.12, side * 0.50);
      for (var b = 0; b < 3; b++) {
        var blade = new THREE.Mesh(new THREE.BoxGeometry(0.055, 0.34, 0.018), darkBrassMat);
        blade.position.y = 0.11;
        blade.rotation.z = b * Math.PI * 2 / 3;
        prop.add(blade);
      }
      var hub = new THREE.Mesh(new THREE.SphereGeometry(0.055, 10, 8), brassMat);
      prop.add(hub);
      body.add(prop);
      props.push(prop);
    });

    // Tail fins + rudder.
    var fin = new THREE.Mesh(new THREE.BoxGeometry(0.36, 0.22, 0.045), copperMat);
    fin.position.set(-0.58, 0.20, 0);
    fin.rotation.z = 0.22;
    body.add(fin);
    var rudder = new THREE.Mesh(new THREE.BoxGeometry(0.22, 0.34, 0.035), brassMat);
    rudder.position.set(-0.66, 0.02, 0);
    rudder.rotation.z = 0.18;
    body.add(rudder);

    // Chimney + steam pipe.
    var chimney = new THREE.Mesh(new THREE.CylinderGeometry(0.05, 0.065, 0.22, 10), ironMat);
    chimney.position.set(-0.34, 0.32, 0.16);
    chimney.rotation.z = -0.25;
    body.add(chimney);
    var chimneyLip = new THREE.Mesh(new THREE.TorusGeometry(0.055, 0.018, 6, 14), brassMat);
    chimneyLip.rotation.x = Math.PI / 2;
    chimneyLip.position.set(-0.30, 0.43, 0.16);
    body.add(chimneyLip);

    // Antenna with a small beacon.
    var antenna = new THREE.Mesh(new THREE.CylinderGeometry(0.008, 0.014, 0.42, 6), darkBrassMat);
    antenna.position.set(-0.20, 0.48, -0.08);
    antenna.rotation.z = -0.16;
    body.add(antenna);
    var beacon = new THREE.Mesh(new THREE.SphereGeometry(0.045, 10, 8), cyanGlowMat);
    beacon.position.set(-0.235, 0.69, -0.08);
    body.add(beacon);
    var beaconGlow = new THREE.Sprite(cyanGlowSprite);
    beaconGlow.scale.set(0.45, 0.45, 1);
    beaconGlow.position.copy(beacon.position);
    body.add(beaconGlow);

    // Little portholes on the visible side.
    for (var i = 0; i < 3; i++) {
      var port = new THREE.Mesh(new THREE.CylinderGeometry(0.042, 0.042, 0.035, 12), amberGlowMat);
      port.rotation.x = Math.PI / 2;
      port.position.set(-0.28 + i * 0.20, -0.02, 0.425);
      body.add(port);
      var portRing = new THREE.Mesh(new THREE.TorusGeometry(0.055, 0.012, 6, 14), darkBrassMat);
      portRing.position.copy(port.position);
      body.add(portRing);
    }

    // Side gear ornament.
    var cog = new THREE.Mesh(new THREE.TorusGeometry(0.11, 0.024, 6, 10), trimMat);
    cog.position.set(-0.34, -0.08, 0.42);
    body.add(cog);

    // Engine light.
    var light = new THREE.PointLight(0xff9a3c, 1.0, 4.5, 2);
    light.position.set(-0.08, -0.30, 0);
    body.add(light);

    root.userData = {
      body: body, props: props, core: core, coreGlow: coreGlow,
      beacon: beacon, light: light, baseY: 0
    };
    return root;
  }

  var player = buildDrone();
  var playerBody = player.userData.body;
  var playerHalo = new THREE.Sprite(new THREE.SpriteMaterial({
    map: softGlowTex, color: 0xffb45f, transparent: true, opacity: 0.34,
    depthWrite: false, blending: THREE.AdditiveBlending
  }));
  playerHalo.scale.set(2.4, 2.4, 1);
  player.add(playerHalo);
  playerBody.rotation.y = -0.16;
  scene.add(player);

  var shadow = new THREE.Mesh(
    new THREE.PlaneGeometry(1.3, 1.3),
    new THREE.MeshBasicMaterial({ map: shadowTex, transparent: true, opacity: 0.42, depthWrite: false })
  );
  shadow.rotation.x = -Math.PI / 2;
  shadow.renderOrder = 5;
  scene.add(shadow);

  // -------------------------------------------------------------- ENEMIES --
  var enemyGeo = {
    sphere: new THREE.SphereGeometry(0.38, 18, 14),
    band: new THREE.TorusGeometry(0.40, 0.055, 8, 22),
    ring: new THREE.TorusGeometry(0.50, 0.028, 6, 10),
    eyePort: new THREE.CylinderGeometry(0.14, 0.17, 0.13, 14),
    eye: new THREE.SphereGeometry(0.105, 12, 10),
    zeppelin: new THREE.SphereGeometry(0.60, 20, 16),
    zeppBand: new THREE.TorusGeometry(0.30, 0.024, 6, 22),
    gondola: new THREE.BoxGeometry(0.44, 0.19, 0.22),
    blade: new THREE.BoxGeometry(0.035, 0.28, 0.012),
    lantern: new THREE.SphereGeometry(0.075, 10, 8),
    hub: new THREE.SphereGeometry(0.055, 10, 8)
  };

  function buildSprocket() {
    var g = new THREE.Group();
    var core = new THREE.Mesh(enemyGeo.sphere, ironMat);
    core.scale.set(1.0, 0.94, 0.94);
    g.add(core);

    var band = new THREE.Mesh(enemyGeo.band, brassMat);
    band.rotation.x = Math.PI / 2;
    g.add(band);

    var ring = new THREE.Mesh(enemyGeo.ring, copperMat);
    ring.rotation.x = Math.PI / 2;
    ring.userData.spin = true;
    g.add(ring);

    var eyePort = new THREE.Mesh(enemyGeo.eyePort, darkBrassMat);
    eyePort.rotation.z = Math.PI / 2;
    eyePort.position.x = -0.38;
    g.add(eyePort);
    var eye = new THREE.Mesh(enemyGeo.eye, redGlowMat);
    eye.scale.x = 0.62;
    eye.position.x = -0.45;
    g.add(eye);

    var glow = new THREE.Sprite(redGlowSprite);
    glow.scale.set(1.3, 1.3, 1);
    glow.position.x = -0.45;
    g.add(glow);

    var cog = new THREE.Mesh(new THREE.TorusGeometry(0.17, 0.032, 6, 8), trimMat);
    cog.position.set(0.05, 0.33, 0);
    cog.userData.spin = true;
    g.add(cog);

    g.userData.ring = ring;
    return g;
  }

  function buildZeppelin() {
    var g = new THREE.Group();
    var body = new THREE.Mesh(enemyGeo.zeppelin, copperMat);
    body.scale.set(1.65, 0.54, 0.54);
    g.add(body);

    for (var i = 0; i < 3; i++) {
      var b = new THREE.Mesh(enemyGeo.zeppBand, brassMat);
      b.rotation.y = Math.PI / 2;
      b.position.x = -0.55 + i * 0.55;
      g.add(b);
    }

    var gondola = new THREE.Mesh(enemyGeo.gondola, darkBrassMat);
    gondola.position.set(-0.02, -0.38, 0);
    g.add(gondola);
    var skid = new THREE.Mesh(new THREE.BoxGeometry(0.34, 0.035, 0.035), brassMat);
    skid.position.set(-0.02, -0.49, 0);
    g.add(skid);

    var lantern = new THREE.Mesh(enemyGeo.lantern, redGlowMat);
    lantern.position.set(-0.94, 0.02, 0);
    g.add(lantern);
    var lglow = new THREE.Sprite(redGlowSprite);
    lglow.scale.set(1.5, 1.5, 1);
    lglow.position.copy(lantern.position);
    g.add(lglow);

    var prop = new THREE.Group();
    prop.position.set(0.97, 0.02, 0);
    for (var j = 0; j < 3; j++) {
      var blade = new THREE.Mesh(enemyGeo.blade, darkBrassMat);
      blade.position.y = 0.12;
      blade.rotation.z = j * Math.PI * 2 / 3;
      prop.add(blade);
    }
    var hub = new THREE.Mesh(enemyGeo.hub, brassMat);
    prop.add(hub);
    g.add(prop);
    g.userData.prop = prop;
    return g;
  }

  // --------------------------------------------------------------- GEARS ---
  function makeGearGeometry() {
    var teeth = 11, outer = 0.27, inner = 0.20;
    var shape = new THREE.Shape();
    var steps = teeth * 2;
    for (var i = 0; i <= steps; i++) {
      var a = (i / steps) * Math.PI * 2;
      var r = (i % 2 === 0) ? outer : inner;
      var x = Math.cos(a) * r, y = Math.sin(a) * r;
      if (i === 0) shape.moveTo(x, y); else shape.lineTo(x, y);
    }
    var hole = new THREE.Path();
    hole.absarc(0, 0, 0.075, 0, Math.PI * 2, true);
    shape.holes.push(hole);
    var geo = new THREE.ExtrudeGeometry(shape, {
      depth: 0.085, bevelEnabled: true, bevelSegments: 2,
      steps: 1, bevelSize: 0.014, bevelThickness: 0.014
    });
    geo.center();
    return geo;
  }
  var gearGeo = makeGearGeometry();

  // ------------------------------------------------------------- GAME DATA --
  var START_X = -3;
  var START_TOP = 0;
  var BASE_SPEED = 5.0;
  var MAX_SPEED = 9.6;
  var GRAV = -30;
  var JUMP_V = 12.0;
  var COYOTE = 0.11;
  var TIER_TIME = 20;
  var PLATFORM_H = 1.35;
  var PLATFORM_D = 3.1;

  var state = 'attract'; // attract | playing | dying | finished
  var p = { x: START_X, y: START_TOP, vy: 0, grounded: true, coyote: 0, buffer: 0 };
  var runT = 0, got = 0, distance = 0, speed = BASE_SPEED, tier = 1, difficulty = 0;
  var lastTier = 1;
  var platforms = [];
  var enemies = [];
  var gears = [];
  var particles = [];
  var sparkGeo = new THREE.BoxGeometry(0.09, 0.09, 0.12);
  var shake = 0;
  var hudClock = 0;
  var eventTimer = null;
  var startPlatform = null;

  var gen = { nextX: 14, lastY: 0, count: 0 };

  // Best run (local). Accept the legacy { time, points } shape.
  var best = null;
  try { best = JSON.parse(localStorage.getItem('hero-game-best') || 'null'); } catch (e) { best = null; }
  if (best && typeof best === 'object' && typeof best.points === 'number' && typeof best.score !== 'number') {
    best = { score: best.points, distance: 0, time: best.time || 0, gears: 0, tier: 1 };
  }
  if (!best || typeof best !== 'object' || typeof best.score !== 'number') best = null;

  function fmtInt(n) {
    try { return Number(n).toLocaleString(); } catch (e) { return String(n); }
  }
  function fmtTime(t) { return t.toFixed(2) + 's'; }
  function scoreValue() {
    return Math.max(50, Math.round(100 + Math.max(0, distance) * 10 + got * 150));
  }

  // --------------------------------------------------------- WORLD BUILD ---
  function addStartPlatform() {
    var w = 30;
    var mesh = new THREE.Mesh(
      new THREE.BoxGeometry(w, PLATFORM_H, PLATFORM_D),
      makePlatformMaterial(w, PLATFORM_H, PLATFORM_D)
    );
    mesh.position.set(-1, START_TOP - PLATFORM_H / 2, 0);
    scene.add(mesh);

    var tip = new THREE.Mesh(new THREE.BoxGeometry(w * 0.96, 0.07, 0.11), trimMat);
    tip.position.set(-1, START_TOP + 0.03, PLATFORM_D / 2 - 0.05);
    scene.add(tip);

    var mist = new THREE.Mesh(mistGeometry, mistMaterial);
    mist.scale.set(w, PLATFORM_D, 1);
    mist.rotation.x = -Math.PI / 2;
    mist.position.set(-1, START_TOP + 0.07, 0);
    scene.add(mist);

    var pl = {
      x: -1, y: START_TOP, hw: w / 2, w: w,
      mesh: mesh, tip: tip, mist: mist, puffs: []
    };
    // Soft cloud fringe at the edges of the start pad.
    addPlatformPuffs(pl, true);
    platforms.push(pl);
    startPlatform = pl;
    return pl;
  }

  function addPlatformPuffs(pl, generous) {
    var count = generous ? 5 : 3;
    for (var i = 0; i < count; i++) {
      var mesh = new THREE.Mesh(puffGeometry, puffMaterial);
      var side = (i % 2 === 0) ? 1 : -1;
      var s = 1.8 + Math.random() * 1.9;
      mesh.scale.set(s * 1.5, s * 0.68, 1);
      mesh.position.set(
        pl.x + (Math.random() - 0.5) * pl.w * 0.95,
        pl.y + 0.22 + Math.random() * 0.35,
        side * (0.9 + Math.random() * 0.8)
      );
      mesh.rotation.z = (Math.random() - 0.5) * 0.25;
      mesh.renderOrder = 2;
      scene.add(mesh);
      pl.puffs.push(mesh);
    }
  }

  function spawnPlatform() {
    var d = difficulty;
    var isRest = gen.count > 3 && gen.count % 7 === 0;
    var early = gen.count < 4;

    var w = (7.1 - 4.45 * d + Math.random() * (1.3 - 0.4 * d));
    var gap = 0.9 + 1.45 * d + Math.random() * (0.55 + 0.75 * d);
    var yStep = (Math.random() * 2 - 1) * (0.35 + 1.25 * d);

    if (early) {
      w = 7.6 - gen.count * 0.5;
      gap = 0.85 + Math.random() * 0.25;
      yStep = 0;
    }
    if (isRest) {
      w += 2.0;
      gap *= 0.7;
      yStep = 0;
    }
    w = Math.max(2.5, w);
    gap = Math.min(3.4, Math.max(0.75, gap));

    var y = gen.lastY + yStep;
    if (y > 5.6) y = 5.6;
    if (y < -2.4) y = -2.4;
    if (early) y = 0;

    var x = gen.nextX + gap + w / 2;
    var mesh = new THREE.Mesh(
      new THREE.BoxGeometry(w, PLATFORM_H, PLATFORM_D),
      makePlatformMaterial(w, PLATFORM_H, PLATFORM_D)
    );
    mesh.position.set(x, y - PLATFORM_H / 2, 0);
    scene.add(mesh);

    var tip = new THREE.Mesh(new THREE.BoxGeometry(w * 0.96, 0.07, 0.11), trimMat);
    tip.position.set(x, y + 0.03, PLATFORM_D / 2 - 0.05);
    scene.add(tip);

    var mist = new THREE.Mesh(mistGeometry, mistMaterial);
    mist.scale.set(w, PLATFORM_D, 1);
    mist.rotation.x = -Math.PI / 2;
    mist.position.set(x, y + 0.07, 0);
    scene.add(mist);

    var pl = { x: x, y: y, hw: w / 2, w: w, mesh: mesh, tip: tip, mist: mist, puffs: [] };
    addPlatformPuffs(pl, isRest);
    platforms.push(pl);

    // Floating gears: arcs that reward a well-timed hop.
    if (Math.random() < (isRest ? 0.95 : 0.78)) {
      var n = 1 + (Math.random() < 0.55 ? 1 : 0) + (d > 0.45 && Math.random() < 0.35 ? 1 : 0);
      for (var gi = 0; gi < n; gi++) {
        var frac = n === 1 ? 0.5 : gi / (n - 1);
        var gx = x - w * 0.30 + frac * w * 0.60;
        var gy = y + 0.95 + Math.sin(frac * Math.PI) * 0.55;
        spawnGear(gx, gy);
      }
    }

    // Enemies become denser as the run progresses, but never in the first
    // few seconds: the opening must stay friendly.
    if (!early && gen.count > 6 && runT > 5 && w >= 4.2 && Math.random() < Math.min(0.78, 0.10 + d * 0.62)) {
      var kind = Math.random() < 0.30 + d * 0.12 ? 'zeppelin' : 'sprocket';
      spawnEnemy(x, y, w, kind, d);
      if (d > 0.62 && Math.random() < 0.18 && w >= 5.6) {
        spawnEnemy(x, y, w, Math.random() < 0.5 ? 'sprocket' : 'zeppelin', d);
      }
    }

    gen.nextX = x + w / 2;
    gen.lastY = y;
    gen.count++;
  }

  function spawnGear(x, y) {
    var grp = new THREE.Group();
    var mesh = new THREE.Mesh(gearGeo, gearMat);
    grp.add(mesh);
    var glow = new THREE.Sprite(amberGlowSprite);
    glow.scale.set(1.25, 1.25, 1);
    grp.add(glow);
    grp.position.set(x, y, 0.05);
    grp.userData = { baseY: y, phase: Math.random() * Math.PI * 2, taken: false };
    scene.add(grp);
    gears.push(grp);
  }

  function spawnEnemy(x, y, w, kind, d) {
    var grp = kind === 'zeppelin' ? buildZeppelin() : buildSprocket();
    var amp = Math.min(0.95, w * 0.26) * (0.6 + Math.random() * 0.4);
    var baseY = y + (kind === 'zeppelin' ? 2.15 : 1.08);
    grp.position.set(x, baseY, 0.05);
    scene.add(grp);
    enemies.push({
      grp: grp,
      kind: kind,
      x0: x,
      y0: baseY,
      amp: amp,
      speed: (kind === 'zeppelin' ? 0.55 : 1.05) + Math.random() * 0.45 + d * 0.35,
      phase: Math.random() * Math.PI * 2,
      rx: kind === 'zeppelin' ? 0.85 : 0.45,
      ry: kind === 'zeppelin' ? 0.46 : 0.46
    });
  }

  function disposePlatform(pl) {
    scene.remove(pl.mesh);
    pl.mesh.geometry.dispose();
    pl.mesh.material.dispose();
    scene.remove(pl.tip);
    pl.tip.geometry.dispose();
    scene.remove(pl.mist);
    pl.puffs.forEach(function (puff) { scene.remove(puff); });
  }

  function clearWorld() {
    platforms.forEach(disposePlatform);
    platforms = [];
    enemies.forEach(function (en) { scene.remove(en.grp); });
    enemies = [];
    gears.forEach(function (g) { scene.remove(g); });
    gears = [];
    particles.forEach(function (pt) {
      scene.remove(pt.mesh);
      if (pt.mesh.material) pt.mesh.material.dispose();
    });
    particles = [];
    gen.nextX = 14;
    gen.lastY = START_TOP;
    gen.count = 0;
    addStartPlatform();
    for (var i = 0; i < 9; i++) spawnPlatform();
  }

  function refreshDifficulty() {
    // Continuous curve still drives level generation (gaps, enemies, height).
    difficulty = 1 - Math.exp(-runT / 68);
    // Speed, instead, steps up only when a new tier is reached.
    var nextTier = 1 + Math.floor(runT / TIER_TIME);
    if (nextTier !== lastTier) {
      lastTier = nextTier;
      tier = nextTier;
      speed = Math.min(MAX_SPEED, BASE_SPEED + (tier - 1) * 0.55);
      showEvent(tr('hero_game_tier').toUpperCase() + ' ' + tier + '  //  BOOST', 'tier');
      FX.tier();
    }
  }

  // ------------------------------------------------------------- PARTICLES --
  function addParticle(mesh, vx, vy, vz, life, grav, grow, fade) {
    mesh.userData.p = {
      vx: vx, vy: vy, vz: vz, life: life, maxLife: life,
      grav: grav == null ? -6 : grav, grow: grow || 0, fade: fade == null ? 1 : fade,
      baseX: mesh.scale.x, baseY: mesh.scale.y
    };
    scene.add(mesh);
    particles.push({ mesh: mesh });
    if (particles.length > 140) {
      var old = particles.shift();
      scene.remove(old.mesh);
      if (old.mesh.material) old.mesh.material.dispose();
    }
  }

  function burst(x, y, z, color, count, big) {
    for (var i = 0; i < count; i++) {
      var mat = new THREE.MeshBasicMaterial({
        color: color, transparent: true, opacity: 1
      });
      var s = new THREE.Mesh(sparkGeo, mat);
      var sc = 0.6 + Math.random() * (big ? 1.4 : 0.8);
      s.scale.set(sc, sc, sc);
      s.position.set(x, y, z);
      addParticle(
        s,
        (Math.random() * 2 - 1) * (big ? 5.2 : 3.1),
        Math.random() * (big ? 5.5 : 2.8) + 1.0,
        (Math.random() - 0.5) * 2.4,
        0.5 + Math.random() * 0.55,
        -9,
        0,
        1
      );
    }
  }

  function steamPuff(x, y, z, scale, color, life, vx, vy) {
    var mat = new THREE.SpriteMaterial({
      map: softGlowTex, color: color || 0xffe2b8, transparent: true,
      opacity: 0.5, depthWrite: false, blending: THREE.AdditiveBlending
    });
    var s = new THREE.Sprite(mat);
    s.scale.set(scale, scale, 1);
    s.position.set(x, y, z);
    addParticle(s, vx == null ? -0.35 : vx, vy == null ? 0.55 : vy, 0.08, life || 0.7, 0.5, 0.9, 0.6);
  }

  function shockwave(x, y, z, color) {
    var mat = new THREE.SpriteMaterial({
      map: softGlowTex, color: color || 0x8ff6ff, transparent: true,
      opacity: 0.7, depthWrite: false, blending: THREE.AdditiveBlending
    });
    var s = new THREE.Sprite(mat);
    s.scale.set(1.2, 1.2, 1);
    s.position.set(x, y, z);
    addParticle(s, 0, 0, 0, 0.55, 0, 9, 0.7);
  }

  // ---------------------------------------------------------------- HUD -----
  function refreshHud(force) {
    if (!force) {
      hudClock += 1 / 60;
      if (hudClock < 0.08) return;
    }
    hudClock = 0;
    if (scoreEl) scoreEl.textContent = fmtInt(scoreValue());
    if (timeEl) timeEl.textContent = runT.toFixed(1) + 's';
    if (distanceEl) distanceEl.textContent = fmtInt(Math.max(0, Math.floor(distance)));
    if (gemsEl) gemsEl.textContent = String(got);
    if (tierLabelEl) tierLabelEl.textContent = tr('hero_game_tier').toUpperCase() + ' ' + tier;
    if (speedEl) speedEl.textContent = speed.toFixed(1) + ' m/s';
    var fill = Math.min(100, (runT % TIER_TIME) / TIER_TIME * 100);
    if (tierFillEl) tierFillEl.style.width = fill.toFixed(1) + '%';
    if (progEl) progEl.style.width = fill.toFixed(1) + '%';
    if (bestEl) {
      bestEl.textContent = best
        ? tr('hero_game_best_pts', { points: fmtInt(best.score) }) +
          (best.distance ? '  ·  ' + fmtInt(best.distance) + ' m' : '')
        : '';
    }
  }

  function showEvent(text, tone) {
    if (!eventEl) return;
    eventEl.textContent = text;
    eventEl.className = 'ds-hero__event is-show' + (tone ? ' is-' + tone : '');
    if (eventTimer) clearTimeout(eventTimer);
    eventTimer = setTimeout(function () {
      if (eventEl) eventEl.classList.remove('is-show');
    }, 1700);
  }

  function setHint(t) { if (hintEl) hintEl.textContent = t; }

  function bumpGears() {
    if (!gemsEl) return;
    var parent = gemsEl.parentNode;
    if (!parent) return;
    parent.classList.remove('is-bump');
    // Force a reflow so the animation can restart quickly.
    void parent.offsetWidth;
    parent.classList.add('is-bump');
  }

  // -------------------------------------------------------- LEADERBOARD ----
  function b64Encode(value) {
    try { return btoa(unescape(encodeURIComponent(value))); } catch (e) { return ''; }
  }
  function setScoreStatus(text, isError) {
    if (!scoreStatusEl) return;
    scoreStatusEl.textContent = text || '';
    scoreStatusEl.classList.toggle('is-error', !!isError);
  }
  function renderLeaderboard(rows) {
    if (!leaderboardBody) return;
    leaderboardBody.innerHTML = '';
    if (!rows || !rows.length) {
      var empty = document.createElement('tr');
      var emptyCell = document.createElement('td');
      emptyCell.colSpan = 4;
      emptyCell.className = 'is-empty';
      emptyCell.textContent = tr('hero_game_no_scores');
      empty.appendChild(emptyCell);
      leaderboardBody.appendChild(empty);
      return;
    }
    rows.forEach(function (row, i) {
      var name = String(row.player || '');
      var trEl = document.createElement('tr');
      if (savedPlayerName && name === savedPlayerName) trEl.className = 'is-you';

      var pos = document.createElement('td');
      pos.textContent = i + 1;
      var player = document.createElement('td');
      player.textContent = name;
      player.title = name;
      var pts = document.createElement('td');
      pts.textContent = fmtInt(row.score);
      var dist = document.createElement('td');
      if (row.distance != null && isFinite(row.distance)) {
        dist.textContent = fmtInt(Math.round(row.distance)) + ' m';
      } else {
        dist.textContent = row.time != null ? Number(row.time).toFixed(2) + 's' : '';
      }

      trEl.appendChild(pos);
      trEl.appendChild(player);
      trEl.appendChild(pts);
      trEl.appendChild(dist);
      leaderboardBody.appendChild(trEl);
    });
  }
  function fetchLeaderboard() {
    if (!leaderboardBody || typeof fetch !== 'function') return;
    fetch(scoresUrl + '?limit=5', {
      headers: { 'Accept': 'application/json' },
      credentials: 'same-origin'
    }).then(function (res) { return res.json(); })
      .then(function (data) {
        if (data && data.scores) renderLeaderboard(data.scores);
      }).catch(function () {});
  }
  function submitScore() {
    if (submittingScore || state !== 'finished') return;
    var name = playerInput ? playerInput.value.trim() : '';
    if (!name) {
      setScoreStatus(tr('hero_game_enter_name'), true);
      if (playerInput) playerInput.focus();
      return;
    }
    var encoded = b64Encode(name);
    if (!encoded) {
      setScoreStatus(tr('hero_game_invalid_name'), true);
      return;
    }

    submittingScore = true;
    if (saveScoreBtn) saveScoreBtn.disabled = true;
    setScoreStatus(tr('hero_game_saving'));

    fetch(scoresUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify({
        player: encoded,
        time: +runT.toFixed(2),
        distance: Math.round(distance * 10) / 10,
        gems: got,
        score: scoreValue()
      })
    }).then(function (res) {
      return res.json().then(function (data) { return { ok: res.ok, data: data }; });
    }).then(function (res) {
      submittingScore = false;
      if (saveScoreBtn) saveScoreBtn.disabled = false;
      var data = res.data || {};
      if (!res.ok || data.status !== 200) {
        setScoreStatus(data.message || tr('hero_game_save_error'), true);
        return;
      }
      savedPlayerName = name;
      try { localStorage.setItem('hero-game-player', name); } catch (e) {}
      if (playerInput) playerInput.value = name;
      if (data.scores) renderLeaderboard(data.scores);
      setScoreStatus(data.improved
        ? tr('hero_game_saved', { rank: data.rank, best: data.best })
        : tr('hero_game_kept', { rank: data.rank, best: data.best }));
    }).catch(function () {
      submittingScore = false;
      if (saveScoreBtn) saveScoreBtn.disabled = false;
      setScoreStatus(tr('hero_game_save_error'), true);
    });
  }
  fetchLeaderboard();

  // ----------------------------------------------------------- RUN STATE ----
  function reset() {
    p.x = START_X; p.y = START_TOP; p.vy = 0; p.grounded = true;
    p.coyote = 0; p.buffer = 0;
    runT = 0; got = 0; distance = 0; speed = BASE_SPEED; tier = 1; lastTier = 1;
    difficulty = 0;
    player.visible = true;
    player.scale.set(1, 1, 1);
    playerBody.rotation.set(0, -0.16, 0);
    playerBody.position.set(0, 0, 0);
    gears.forEach(function (g) { g.userData.taken = false; g.visible = true; });
    refreshHud(true);
  }

  function closeRecap() {
    if (recapEl) {
      recapEl.classList.remove('is-open');
      recapEl.setAttribute('aria-hidden', 'true');
      if (document.activeElement && recapEl.contains(document.activeElement)) document.activeElement.blur();
    }
    if (scoreForm) scoreForm.setAttribute('inert', '');
    hero.classList.remove('is-finished');
  }

  function toLanding() {
    state = 'attract';
    closeRecap();
    clearWorld();
    reset();
    hero.classList.remove('is-playing');
    setHint(tr('hero_game_hint_play'));
    FX.engine(false);
  }

  function start() {
    FX.ensure();
    reset();
    clearWorld();
    state = 'playing';
    hero.classList.add('is-playing');
    hero.classList.remove('is-finished');
    closeRecap();
    submittingScore = false;
    if (saveScoreBtn) saveScoreBtn.disabled = false;
    setScoreStatus('');
    if (playerInput) playerInput.value = savedPlayerName;
    setHint(tr('hero_game_hint_jump'));
    FX.go();
  }

  function die() {
    if (state !== 'playing') return;
    state = 'dying';
    FX.die();
    FX.engine(false);
    player.visible = false;
    shake = reduceMotion ? 0 : 0.55;
    burst(p.x, p.y + 0.55, 0, 0xffb15c, 24, true);
    burst(p.x, p.y + 0.45, 0, 0xffe6b0, 10, false);
    shockwave(p.x, p.y + 0.5, 0.1, 0xffa552);
    for (var i = 0; i < 10; i++) {
      steamPuff(p.x + (Math.random() - 0.5), p.y + 0.4 + Math.random() * 0.5, 0.1,
        0.5 + Math.random() * 0.6, 0xffdcb0, 0.9 + Math.random() * 0.4,
        (Math.random() - 0.5) * 1.6, 0.4 + Math.random() * 1.2);
    }
    setHint('');
    setTimeout(function () {
      if (state === 'dying') finish();
    }, 850);
  }

  function finish() {
    state = 'finished';
    hero.classList.remove('is-playing');
    hero.classList.add('is-finished');
    var pts = scoreValue();
    var isBest = best == null || pts > best.score;
    if (isBest) {
      best = {
        score: pts,
        distance: Math.floor(distance),
        time: +runT.toFixed(2),
        gears: got,
        tier: tier
      };
      try { localStorage.setItem('hero-game-best', JSON.stringify(best)); } catch (e) {}
    }
    FX.win();
    refreshHud(true);

    setTimeout(function () {
      if (state !== 'finished') return;
      var rt = document.getElementById('recap-time');
      var rg = document.getElementById('recap-gems');
      var rp = document.getElementById('recap-points');
      var rb = document.getElementById('recap-best');
      var rd = document.getElementById('recap-distance');
      var rti = document.getElementById('recap-tier');
      if (rt) rt.textContent = fmtTime(runT);
      if (rg) rg.textContent = String(got);
      if (rp) rp.textContent = fmtInt(pts);
      if (rd) rd.textContent = fmtInt(Math.floor(distance)) + ' m';
      if (rti) rti.textContent = String(tier);
      if (rb) rb.textContent = isBest
        ? tr('hero_game_new_best')
        : tr('hero_game_best_pts', { points: fmtInt(best.score) });
      if (recapEl) {
        recapEl.classList.add('is-open');
        recapEl.setAttribute('aria-hidden', 'false');
      }
      if (scoreForm) scoreForm.removeAttribute('inert');
      if (playerInput) playerInput.value = savedPlayerName;
      if (savedPlayerName) submitScore();
      else if (playerInput && !reduceMotion) playerInput.focus();
    }, 350);
  }

  // -------------------------------------------------------------- INPUT ----
  function doJump() {
    if (state !== 'playing') return;
    if (p.grounded || p.coyote > 0) {
      p.vy = JUMP_V;
      p.grounded = false;
      p.coyote = 0;
      FX.jump();
      for (var i = 0; i < 4; i++) {
        steamPuff(p.x - 0.3 + (Math.random() - 0.5) * 0.4, p.y + 0.25, 0.05,
          0.35 + Math.random() * 0.35, 0xffe7c4, 0.45 + Math.random() * 0.2,
          -0.6 - Math.random() * 0.6, -0.1 - Math.random() * 0.3);
      }
    } else {
      p.buffer = 0.12;
    }
  }
  function cutJump() {
    if (state !== 'playing') return;
    if (!p.grounded && p.vy > 3.0) p.vy *= 0.52;
  }

  window.addEventListener('keydown', function (e) {
    if (e.target && /^(INPUT|TEXTAREA|SELECT|BUTTON)$/.test(e.target.tagName)) return;
    if (e.code === 'Enter') {
      if (state === 'finished') { e.preventDefault(); start(); }
      else if (state === 'attract') { e.preventDefault(); start(); }
      return;
    }
    if (e.code !== 'Space' && e.code !== 'ArrowUp' && e.code !== 'KeyW') return;
    e.preventDefault();
    if (e.repeat) return;
    if (state === 'attract') { start(); return; }
    if (state === 'finished') { start(); return; }
    if (state === 'playing') doJump();
  });
  window.addEventListener('keyup', function (e) {
    if (e.code === 'Space' || e.code === 'ArrowUp' || e.code === 'KeyW') cutJump();
  });

  function heroPress(e) {
    if (e.target.closest && e.target.closest('a,button,input,label,form,select,textarea,.ds-hero__recap-card')) return;
    FX.ensure();
    if (state === 'playing') doJump();
    else if (state === 'finished') start();
    else start();
  }
  hero.addEventListener('mousedown', heroPress);
  hero.addEventListener('touchstart', heroPress, { passive: true });
  hero.addEventListener('mouseup', cutJump);
  hero.addEventListener('touchend', cutJump);

  // ------------------------------------------------------------ GAMEPLAY ----
  function step(dt, t) {
    U_TIME.value = t;

    if (state === 'playing') {
      runT += dt;
      refreshDifficulty();
      p.x += speed * dt;
      distance = Math.max(0, p.x - START_X);

      // Vertical motion / landing.
      if (!p.grounded) {
        var prevY = p.y;
        p.vy += GRAV * dt;
        p.y += p.vy * dt;
        if (p.vy < 0) {
          for (var i = 0; i < platforms.length; i++) {
            var pl = platforms[i];
            if (Math.abs(p.x - pl.x) <= pl.hw + 0.24) {
              if (prevY >= pl.y - 0.02 && p.y <= pl.y + 0.14) {
                p.y = pl.y; p.vy = 0; p.grounded = true;
                steamPuff(p.x, p.y + 0.12, 0.1, 0.85, 0xf4f9ff, 0.45, 0, 0.3);
                burst(p.x, p.y + 0.1, 0, 0xd7ecff, 5, false);
                if (p.buffer > 0) { p.buffer = 0; doJump(); }
                break;
              }
            }
          }
        }
        if (p.y < -7.5) { die(); return; }
      } else {
        var supported = false;
        for (var j = 0; j < platforms.length; j++) {
          var q = platforms[j];
          if (Math.abs(p.x - q.x) <= q.hw + 0.22 && Math.abs(p.y - q.y) < 0.18) {
            supported = true; break;
          }
        }
        if (!supported) { p.grounded = false; p.coyote = COYOTE; }
      }
      p.coyote = Math.max(0, p.coyote - dt);
      p.buffer = Math.max(0, p.buffer - dt);

      // Gear collection.
      for (var gi = 0; gi < gears.length; gi++) {
        var g = gears[gi];
        if (g.userData.taken) continue;
        var dx = g.position.x - p.x;
        var dy = g.position.y - (p.y + 0.5);
        if (dx * dx + dy * dy < 0.62) {
          g.userData.taken = true;
          g.visible = false;
          got++;
          FX.gem(got);
          bumpGears();
          burst(g.position.x, g.position.y, 0.1, 0xffc46a, 10, false);
          steamPuff(g.position.x, g.position.y, 0.1, 0.55, 0xffe1a0, 0.45, 0, 0.9);
          refreshHud(true);
        }
      }

      // Enemy collisions.
      var pcx = p.x, pcy = p.y + 0.5;
      for (var ei = 0; ei < enemies.length; ei++) {
        var en = enemies[ei];
        var ex = en.grp.position.x - pcx;
        var ey = en.grp.position.y - pcy;
        if (Math.abs(ex) < en.rx + 0.34 && Math.abs(ey) < en.ry + 0.36) {
          die();
          return;
        }
      }

      // Endless generation + culling.
      while (gen.nextX < p.x + 78) spawnPlatform();
      while (platforms.length && platforms[0].x + platforms[0].hw < p.x - 32) {
        disposePlatform(platforms.shift());
      }
      enemies = enemies.filter(function (en) {
        if (en.x0 + en.amp < p.x - 34) { scene.remove(en.grp); return false; }
        return true;
      });
      gears = gears.filter(function (g) {
        if (g.position.x < p.x - 34) { scene.remove(g); return false; }
        return true;
      });

      refreshHud(false);
    } else if (state === 'attract' || state === 'finished') {
      p.x = START_X;
      p.y = START_TOP;
      // Idle hover on the starting pad.
      if (state === 'attract') {
        p.y = START_TOP + Math.sin(t * 1.8) * 0.04;
      }
    }

    // Drone visual pose.
    var visiblePlayer = state !== 'dying' && state !== 'finished';
    player.visible = visiblePlayer;
    if (visiblePlayer) {
      var bob = Math.sin(t * 3.2) * (state === 'playing' ? 0.018 : 0.045);
      playerBody.position.y = bob;
      var noseTilt = state === 'playing' ? Math.max(-0.22, Math.min(0.24, p.vy * 0.018)) : 0;
      playerBody.rotation.z = noseTilt;
      playerBody.rotation.x = state === 'playing'
        ? Math.sin(t * 2.1) * 0.035 - (speed / MAX_SPEED) * 0.05
        : Math.sin(t * 1.1) * 0.03;
      playerBody.rotation.y = -0.16 + Math.sin(t * 0.7) * 0.035;
      player.position.set(p.x, p.y + 0.48 + bob, 0);
      player.scale.set(
        1 + (state === 'playing' ? Math.max(0, p.vy) * 0.004 : 0),
        1 - (state === 'playing' ? Math.max(0, p.vy) * 0.003 : 0),
        1
      );

      // Propellers + furnace pulse.
      var spin = (state === 'playing' ? 22 + speed * 2.2 : 12) * dt;
      player.userData.props.forEach(function (prop) { prop.rotation.z += spin; });
      var pulse = 0.7 + 0.3 * Math.sin(t * 7.5);
      player.userData.core.scale.setScalar(0.85 + pulse * 0.28);
      player.userData.coreGlow.scale.set(1.0 + pulse * 0.45, 1.0 + pulse * 0.45, 1);
      player.userData.light.intensity = 0.7 + pulse * 0.5;
      playerHalo.material.opacity = 0.20 + pulse * 0.14;

      // Steam trail.
      if (state === 'playing' && !reduceMotion && Math.random() < dt * 11) {
        steamPuff(p.x - 0.62, p.y + 0.62, 0.15, 0.24 + Math.random() * 0.22,
          0xffe4bd, 0.55 + Math.random() * 0.3, -0.8 - Math.random() * 0.6, 0.4 + Math.random() * 0.5);
      }

      // Shadow on the platform below.
      var shTop = -20, shX = p.x;
      for (var si = 0; si < platforms.length; si++) {
        var sp = platforms[si];
        if (Math.abs(p.x - sp.x) < sp.hw + 0.3) {
          if (sp.y <= p.y + 0.15 && sp.y > shTop) {
            shTop = sp.y;
            shX = Math.max(sp.x - sp.hw, Math.min(sp.x + sp.hw, p.x));
          }
        }
      }
      var shD = p.y - shTop;
      if (shTop > -18 && shD < 7) {
        shadow.visible = true;
        shadow.position.set(shX, shTop + 0.055, 0);
        shadow.material.opacity = Math.max(0.06, 0.36 - shD * 0.05);
        var shS = Math.max(0.55, 1.2 - shD * 0.06);
        shadow.scale.set(shS, shS, 1);
      } else {
        shadow.visible = false;
      }
    } else {
      shadow.visible = false;
    }

    // Gears idle animation.
    gears.forEach(function (g) {
      if (g.userData.taken) return;
      g.rotation.y += dt * 2.2;
      g.rotation.z += dt * 0.8;
      g.position.y = g.userData.baseY + Math.sin(t * 2.8 + g.userData.phase) * 0.09;
    });

    // Enemies patrol / face the player.
    enemies.forEach(function (en) {
      en.grp.position.x = en.x0 + Math.sin(t * en.speed + en.phase) * en.amp;
      en.grp.position.y = en.y0 + Math.sin(t * en.speed * 1.7 + en.phase) * 0.09;
      if (en.kind === 'zeppelin') {
        en.grp.userData.prop.rotation.x += dt * 16;
        en.grp.rotation.y = Math.sin(t * 0.6 + en.phase) * 0.06;
      } else {
        en.grp.rotation.y += dt * 0.9;
        en.grp.rotation.z = Math.sin(t * 1.8 + en.phase) * 0.10;
        var ring = en.grp.userData.ring;
        if (ring) ring.rotation.z += dt * 2.5;
      }
    });

    // Particles.
    for (var pi = particles.length - 1; pi >= 0; pi--) {
      var pt = particles[pi], pm = pt.mesh, pp = pm.userData.p;
      pp.life -= dt;
      if (pp.life <= 0) {
        scene.remove(pm);
        if (pm.material) pm.material.dispose();
        particles.splice(pi, 1);
        continue;
      }
      pp.vy += pp.grav * dt;
      pm.position.x += pp.vx * dt;
      pm.position.y += pp.vy * dt;
      pm.position.z += pp.vz * dt;
      var k = pp.life / pp.maxLife;
      if (pp.grow) {
        var gs = 1 + (1 - k) * pp.grow;
        pm.scale.set(pp.baseX * gs, pp.baseY * gs, 1);
      }
      if (pm.material) {
        pm.material.opacity = Math.max(0, k * pp.fade);
      }
    }

    // Camera: tilted side view, slightly above and in front of the drone.
    var targetX = (state === 'attract') ? START_X + 1.8 + Math.sin(t * 0.25) * 0.7 : p.x + 2.9;
    var targetY = (state === 'attract') ? 3.0 : Math.max(-1.2, p.y) + 2.85;
    camera.position.x += (targetX - camera.position.x) * Math.min(1, dt * 4.5);
    camera.position.y += (targetY - camera.position.y) * Math.min(1, dt * 3.6);
    camera.position.z += (viewZ - camera.position.z) * Math.min(1, dt * 3);
    var sx = 0, sy = 0;
    if (shake > 0.001) {
      sx = (Math.random() - 0.5) * shake;
      sy = (Math.random() - 0.5) * shake;
      shake *= Math.pow(0.02, dt);
    }
    var lookX = (state === 'attract') ? camera.position.x - 0.9 : p.x + 1.35;
    var lookY = (state === 'attract') ? camera.position.y - 2.55 : Math.max(-1.2, p.y) + 0.35;
    camera.lookAt(lookX + sx, lookY + sy, 0);

    // Background layers follow the run but their world-space noise scrolls,
    // so the cloud sea moves at a believable parallax speed.
    sky.position.copy(camera.position);
    deck.position.x = p.x + 20;
    deck.position.z = -26;
    bankMid.position.x = p.x + 12;
    bankFar.position.x = p.x + 24;

    // Engine soundtrack & music.
    if (state === 'playing') FX.engine(true, speed);
    Music.tick();
  }

  // --------------------------------------------------------------- RESIZE ---
  function resize() {
    var w = canvas.clientWidth || 1, h = canvas.clientHeight || 1;
    renderer.setSize(w, h, false);
    camera.aspect = w / h;
    var a = camera.aspect;
    viewZ = a > 2.1 ? 11.4 : a > 1.5 ? 12.4 : a > 1.0 ? 14.2 : 17.0;
    camera.updateProjectionMatrix();
  }
  window.addEventListener('resize', resize);

  // ---------------------------------------------------------------- INIT ----
  reset();
  clearWorld();
  refreshHud(true);
  resize();
  camera.position.set(START_X + 2.2, 3.0, viewZ);
  camera.lookAt(START_X + 1.0, 0.35, 0);

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

  // Minimal hook for automated testing.
  window.heroGame = {
    start: start,
    jump: doJump,
    tick: function (n, silent) {
      for (var i = 0; i < (n || 1); i++) { elapsed += 1 / 60; step(1 / 60, elapsed); }
      if (!silent) renderer.render(scene, camera);
    },
    info: function () {
      return {
        state: state,
        x: +p.x.toFixed(2),
        y: +p.y.toFixed(2),
        distance: +distance.toFixed(1),
        score: scoreValue(),
        time: +runT.toFixed(2),
        gems: got,
        tier: tier,
        speed: +speed.toFixed(2),
        grounded: p.grounded,
        platforms: platforms.length,
        enemies: enemies.length,
        gears: gears.filter(function (g) { return !g.userData.taken; }).length,
        edge: (function () {
          var best = null;
          for (var i = 0; i < platforms.length; i++) {
            var pl = platforms[i];
            if (pl.x + pl.hw > p.x + 0.2 && (!best || pl.x < best.x)) best = pl;
          }
          return best ? [+(best.x - best.hw).toFixed(2), +(best.x + best.hw).toFixed(2), +best.y.toFixed(2)] : null;
        })(),
        threat: (function () {
          var best = null, bd = 99;
          for (var i = 0; i < enemies.length; i++) {
            var en = enemies[i], dx = en.grp.position.x - p.x;
            if (dx > -0.5 && dx < bd) {
              bd = dx;
              best = [+dx.toFixed(2), +(en.grp.position.y - p.y).toFixed(2), en.kind];
            }
          }
          return best;
        })()
      };
    }
  };
})();
