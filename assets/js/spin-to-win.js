/**
 * Design Cart SpinToWin — front wheel (Three.js)
 * Visual 1:1 with spin-to-win.html + email claim
 *
 * @author Paweł Nosko | Design Cart
 */
(function () {
  'use strict';

  function boot(attempt) {
    if (typeof THREE === 'undefined') {
      if ((attempt || 0) < 40) {
        setTimeout(function () {
          boot((attempt || 0) + 1);
        }, 50);
      }
      return;
    }
    init();
  }

  function init() {
  var cfg = window.dcSpinToWin || {};

  var root = document.getElementById('dc-stw');
  var overlay = document.getElementById('dc-stw-overlay');
  var stage = document.getElementById('dc-stw-stage');
  var win = document.getElementById('dc-stw-win');
  var claimForm = document.getElementById('dc-stw-claim-form');
  var emailInput = document.getElementById('dc-stw-email');
  var msgEl = document.getElementById('dc-stw-msg');
  if (!root || !overlay || !stage || !win) {
    return;
  }

  root.hidden = false;

  var TAU = Math.PI * 2;
  var PAL = [
    { bg: '#e63946', fg: '#fff' },
    { bg: '#ff9f1c', fg: '#3a1d00' },
    { bg: '#ffd60a', fg: '#3a2f00' },
    { bg: '#2dc653', fg: '#fff' },
    { bg: '#1d8cf8', fg: '#fff' },
    { bg: '#a23bd6', fg: '#fff' }
  ];

  var prizes = [];
  var rot = 0;
  var mode = 'idle';
  var anim = null;
  var drag = null;
  var faceMesh = null;
  var pegs = null;
  var mx = 0;
  var my = 0;
  var tx = 0;
  var ty = 0;
  var claimed = false;

  var renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
  renderer.outputEncoding = THREE.sRGBEncoding;
  stage.appendChild(renderer.domElement);

  var scene = new THREE.Scene();
  var camera = new THREE.PerspectiveCamera(32, 1, 0.1, 60);
  camera.position.set(0, 0.3, 9.8);
  scene.add(new THREE.AmbientLight(0xffffff, 0.8));
  var key = new THREE.DirectionalLight(0xffffff, 0.7);
  key.position.set(3, 5, 8);
  scene.add(key);
  var warm = new THREE.PointLight(0xffd9a0, 0.6, 30);
  warm.position.set(-4, 2, 7);
  scene.add(warm);

  var gold = function () {
    return new THREE.MeshPhongMaterial({ color: 0xffc83d, specular: 0xffffff, shininess: 90 });
  };
  var dark = new THREE.MeshPhongMaterial({ color: 0x15121c, specular: 0x8888aa, shininess: 90 });
  var wheelRoot = new THREE.Group();
  scene.add(wheelRoot);

  var body = new THREE.Mesh(new THREE.CylinderGeometry(2.02, 2.02, 0.4, 64).rotateX(Math.PI / 2), dark);
  wheelRoot.add(body);
  var rim = new THREE.Mesh(new THREE.TorusGeometry(1.92, 0.12, 20, 96), dark);
  rim.position.z = 0.2;
  wheelRoot.add(rim);

  var bulbs = [];
  for (var k = 0; k < 24; k++) {
    var m = gold();
    var b = new THREE.Mesh(new THREE.SphereGeometry(0.07, 16, 12), m);
    var t = (k / 24) * TAU;
    b.position.set(Math.sin(t) * 1.92, Math.cos(t) * 1.92, 0.3);
    wheelRoot.add(b);
    bulbs.push(m);
  }

  var wheel = new THREE.Group();
  wheelRoot.add(wheel);
  var hub = new THREE.Mesh(new THREE.CylinderGeometry(0.36, 0.4, 0.16, 40).rotateX(Math.PI / 2), gold());
  hub.position.z = 0.3;
  wheelRoot.add(hub);
  var cap = new THREE.Mesh(new THREE.SphereGeometry(0.2, 24, 16), gold());
  cap.position.z = 0.38;
  wheelRoot.add(cap);

  var sc = document.createElement('canvas');
  sc.width = sc.height = 256;
  var sg = sc.getContext('2d');
  var gr = sg.createLinearGradient(0, 0, 256, 256);
  gr.addColorStop(0, 'rgba(255,255,255,.38)');
  gr.addColorStop(0.45, 'rgba(255,255,255,0)');
  gr.addColorStop(1, 'rgba(255,255,255,.08)');
  sg.fillStyle = gr;
  sg.fillRect(0, 0, 256, 256);
  var shine = new THREE.Mesh(
    new THREE.CircleGeometry(1.8, 64),
    new THREE.MeshBasicMaterial({ map: new THREE.CanvasTexture(sc), transparent: true, depthWrite: false })
  );
  shine.position.z = 0.22;
  wheelRoot.add(shine);

  var pointer = new THREE.Group();
  pointer.position.set(0, 2.55, 0.4);
  wheelRoot.add(pointer);
  var cone = new THREE.Mesh(new THREE.ConeGeometry(0.2, 0.95, 24).rotateZ(Math.PI), gold());
  cone.position.y = -0.475;
  pointer.add(cone);
  pointer.add(new THREE.Mesh(new THREE.SphereGeometry(0.17, 20, 14), gold()));

  function fmt(p) {
    return typeof p.amount === 'number' ? String(p.amount) : String(p.amount || '');
  }

  function colorOf(i, n) {
    var c = i % PAL.length;
    if (i === n - 1 && n > 1 && c === 0) c = 2;
    return PAL[c];
  }

  function buildFace() {
    var S = 1024;
    var R = S / 2;
    var c = document.createElement('canvas');
    c.width = c.height = S;
    var g = c.getContext('2d');
    var n = prizes.length;
    var a = TAU / n;
    g.translate(R, R);
    prizes.forEach(function (p, i) {
      var col = colorOf(i, n);
      var s = -Math.PI / 2 + (i - 0.5) * a;
      g.beginPath();
      g.moveTo(0, 0);
      g.arc(0, 0, R, s, s + a);
      g.closePath();
      g.fillStyle = col.bg;
      g.fill();
      g.lineWidth = 5;
      g.strokeStyle = 'rgba(255,255,255,.9)';
      g.stroke();
      g.save();
      g.rotate(-Math.PI / 2 + i * a);
      g.fillStyle = col.fg;
      g.textAlign = 'right';
      g.textBaseline = 'middle';
      g.shadowColor = 'rgba(0,0,0,.3)';
      g.shadowBlur = 6;
      var fa = Math.max(26, Math.min(76, 300 * a * 0.36));
      var fn = fa * 0.45;
      var maxW = R - 40 - Math.max(130, (fa + fn * 1.5) / a);
      var txt = fmt(p);
      var nm = String(p.name || '');
      var fit = function (t, sz, w) {
        while (sz > 14) {
          g.font = '800 ' + sz + "px 'Trebuchet MS',sans-serif";
          if (g.measureText(t).width <= w) break;
          sz -= 2;
        }
        return sz;
      };
      fa = fit(txt, fa, maxW);
      fn = fit(nm, fn, maxW);
      var top = -(fa + fn * 1.5) / 2;
      var x = R - 40;
      g.font = '800 ' + fa + "px 'Trebuchet MS',sans-serif";
      g.fillText(txt, x, top + fa / 2);
      g.font = '700 ' + fn + "px 'Trebuchet MS',sans-serif";
      g.fillText(nm, x, top + fa + fn * 0.75);
      g.restore();
    });
    g.lineWidth = 14;
    g.strokeStyle = '#ffc83d';
    g.beginPath();
    g.arc(0, 0, R - 7, 0, TAU);
    g.stroke();
    var tex = new THREE.CanvasTexture(c);
    tex.encoding = THREE.sRGBEncoding;
    tex.anisotropy = renderer.capabilities.getMaxAnisotropy();
    return tex;
  }

  function build() {
    if (faceMesh) {
      faceMesh.material.map.dispose();
      wheel.remove(faceMesh);
    }
    if (pegs) wheel.remove(pegs);
    if (!prizes.length) return;
    faceMesh = new THREE.Mesh(new THREE.CircleGeometry(1.8, 96), new THREE.MeshBasicMaterial({ map: buildFace() }));
    faceMesh.position.z = 0.21;
    wheel.add(faceMesh);
    pegs = new THREE.Group();
    var a = TAU / prizes.length;
    var pm = gold();
    var pg = new THREE.SphereGeometry(0.055, 12, 10);
    for (var i = 0; i < prizes.length; i++) {
      var ang = (i + 0.5) * a;
      var mesh = new THREE.Mesh(pg, pm);
      mesh.position.set(Math.sin(ang) * 1.72, Math.cos(ang) * 1.72, 0.25);
      pegs.add(mesh);
    }
    wheel.add(pegs);
  }

  function pickWinner() {
    var w = prizes.map(function (p) {
      return p.weight > 0 ? p.weight : 1;
    });
    var sum = w.reduce(function (x, y) {
      return x + y;
    }, 0);
    var r = Math.random() * sum;
    for (var i = 0; i < w.length; i++) {
      r -= w[i];
      if (r <= 0) return i;
    }
    return w.length - 1;
  }

  function startSpin(v) {
    if (!prizes.length || mode === 'spin') return;
    var n = prizes.length;
    var a = TAU / n;
    var dir = v < 0 ? -1 : 1;
    var speed = Math.min(Math.max(Math.abs(v), 6), 24);
    var target = pickWinner();
    var base = -target * a + (Math.random() - 0.5) * a * 0.6;
    var minD = Math.max(TAU * 3, speed * 1.1);
    var r0 = rot;
    var k = dir > 0 ? Math.ceil((r0 + minD - base) / TAU) : Math.floor((r0 - minD - base) / TAU);
    var rf = base + k * TAU;
    var D = Math.abs(rf - r0);
    var T = Math.min(Math.max((4 * D) / speed, 4.5), 8);
    anim = { r0: r0, rf: rf, t0: performance.now(), T: T * 1000, target: target };
    mode = 'spin';
    stage.classList.add('busy');
    stage.classList.remove('grabbing');
    claimed = false;
  }

  function finish() {
    var p = prizes[anim.target];
    mode = 'done';
    setTimeout(function () {
      document.getElementById('dc-stw-win-amount').textContent = fmt(p);
      document.getElementById('dc-stw-win-name').textContent = p.name || '';
      if (emailInput) emailInput.value = '';
      if (msgEl) {
        msgEl.hidden = true;
        msgEl.textContent = '';
        msgEl.className = 'dc-stw__msg';
      }
      win.hidden = false;
      if (emailInput) emailInput.focus();
    }, 450);
  }

  function center() {
    var r = renderer.domElement.getBoundingClientRect();
    var v = new THREE.Vector3(0, 0, 0).project(camera);
    return { x: r.left + ((v.x + 1) / 2) * r.width, y: r.top + ((1 - v.y) / 2) * r.height };
  }

  var ang = function (e, c) {
    return Math.atan2(e.clientY - c.y, e.clientX - c.x);
  };

  stage.addEventListener('pointerdown', function (e) {
    if (mode === 'spin' || (mode === 'done' && !win.hidden)) return;
    var c = center();
    drag = {
      c: c,
      last: ang(e, c),
      x0: e.clientX,
      y0: e.clientY,
      moved: 0,
      s: [{ t: performance.now(), r: rot }]
    };
    mode = 'drag';
    stage.classList.add('grabbing');
    stage.setPointerCapture(e.pointerId);
  });

  stage.addEventListener('pointermove', function (e) {
    var r = stage.getBoundingClientRect();
    mx = ((e.clientX - r.left) / r.width) * 2 - 1;
    my = ((e.clientY - r.top) / r.height) * 2 - 1;
    if (mode !== 'drag' || !drag) return;
    var d = ang(e, drag.c) - drag.last;
    if (d > Math.PI) d -= TAU;
    if (d < -Math.PI) d += TAU;
    drag.last = ang(e, drag.c);
    rot += d;
    drag.moved = Math.hypot(e.clientX - drag.x0, e.clientY - drag.y0);
    var now = performance.now();
    drag.s.push({ t: now, r: rot });
    while (drag.s.length > 2 && now - drag.s[0].t > 120) drag.s.shift();
  });

  function release() {
    if (mode !== 'drag' || !drag) return;
    var s = drag.s;
    var f = s[0];
    var l = s[s.length - 1];
    var dt = (l.t - f.t) / 1000;
    var v = dt > 0 ? (l.r - f.r) / dt : 0;
    if (drag.moved < 6) v = 10;
    drag = null;
    mode = 'idle';
    startSpin(v || 10);
  }

  stage.addEventListener('pointerup', release);
  stage.addEventListener('pointercancel', release);
  stage.addEventListener('pointerleave', function () {
    if (mode !== 'drag') {
      mx = 0;
      my = 0;
    }
  });

  function resize() {
    var w = stage.clientWidth;
    var h = stage.clientHeight;
    if (!w || !h) return;
    renderer.setSize(w, h);
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
  }
  new ResizeObserver(resize).observe(stage);

  var clock = performance.now();
  function loop(now) {
    requestAnimationFrame(loop);
    if (overlay.hidden) return;
    if (mode === 'spin' && anim) {
      var t = (now - anim.t0) / anim.T;
      if (t >= 1) {
        rot = anim.rf;
        mode = 'finish';
        finish();
      } else {
        rot = anim.r0 + (anim.rf - anim.r0) * (1 - Math.pow(1 - t, 4));
      }
    }
    if (mode === 'done') stage.classList.add('busy');
    wheel.rotation.z = -rot;
    if (prizes.length) {
      var s = (((rot / (TAU / prizes.length) + 0.5) % 1) + 1) % 1;
      var d = s < 0.5 ? s : s - 1;
      pointer.rotation.z = d < 0 && d > -0.25 ? 0.45 * (1 + d / 0.25) : 0;
    }
    var sec = (now - clock) / 1000;
    var ph = Math.floor(sec * (mode === 'spin' ? 14 : 3));
    bulbs.forEach(function (m, idx) {
      var on = (idx + ph) % 2 === 0;
      m.emissive.setHex(on ? 0xff9a00 : 0x1a0f00);
    });
    tx += (mx - tx) * 0.08;
    ty += (my - ty) * 0.08;
    wheelRoot.rotation.y = tx * 0.25;
    wheelRoot.rotation.x = ty * 0.14;
    renderer.render(scene, camera);
  }

  function markSeen() {
    var key = cfg.storageKey || 'dc_stw_seen';
    try {
      if (cfg.frequency === 'once') {
        localStorage.setItem(key, '1');
      } else if (cfg.frequency === 'every_session') {
        sessionStorage.setItem(key, '1');
      }
      // every_reload / button_only — nic nie zapisujemy
    } catch (e) {}
  }

  function alreadySeen() {
    var key = cfg.storageKey || 'dc_stw_seen';
    try {
      if (cfg.frequency === 'every_reload') return false;
      if (cfg.frequency === 'once') return localStorage.getItem(key) === '1';
      if (cfg.frequency === 'every_session') return sessionStorage.getItem(key) === '1';
    } catch (e) {}
    return false;
  }

  function openPopup() {
    overlay.hidden = false;
    win.hidden = true;
    if (mode === 'done') mode = 'idle';
    stage.classList.remove('busy');
    resize();
    markSeen();
  }

  function closePopup() {
    overlay.hidden = true;
  }

  document.getElementById('dc-stw-close').addEventListener('click', closePopup);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !overlay.hidden) closePopup();
  });

  if (claimForm) {
    claimForm.addEventListener('submit', function (e) {
      e.preventDefault();
      if (claimed || !anim || !prizes[anim.target]) return;
      var email = (emailInput && emailInput.value ? emailInput.value : '').trim();
      if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showMsg((cfg.i18n && cfg.i18n.claimError) || 'Invalid email', false);
        return;
      }

      var rodoInput = document.getElementById('dc-stw-rodo');
      if (rodoInput && !rodoInput.checked) {
        showMsg((cfg.i18n && cfg.i18n.rodoError) || 'RODO required', false);
        return;
      }

      var prize = prizes[anim.target];
      var btn = document.getElementById('dc-stw-claim');
      if (btn) btn.disabled = true;

      var body = new URLSearchParams();
      body.set('action', cfg.ajaxAction || 'dc_stw_claim');
      body.set('email', email);
      body.set('id_cart_rule', String(prize.id_cart_rule || prize.id || 0));
      body.set('token', cfg.token || '');
      body.set('ajax', '1');
      if (rodoInput) {
        body.set('rodo', rodoInput.checked ? '1' : '0');
      }

      fetch(cfg.claimUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: body.toString(),
        credentials: 'same-origin'
      })
        .then(function (r) {
          return r.json();
        })
        .then(function (json) {
          if (json && json.success) {
            claimed = true;
            showMsg((json.message || (cfg.i18n && cfg.i18n.claimSuccess)) || 'OK', true);
            if (emailInput) emailInput.disabled = true;
          } else {
            showMsg((json && json.message) || (cfg.i18n && cfg.i18n.claimError) || 'Error', false);
            if (btn) btn.disabled = false;
          }
        })
        .catch(function () {
          showMsg((cfg.i18n && cfg.i18n.claimError) || 'Error', false);
          if (btn) btn.disabled = false;
        });
    });
  }

  function showMsg(text, ok) {
    if (!msgEl) return;
    msgEl.hidden = false;
    msgEl.textContent = text;
    msgEl.className = 'dc-stw__msg ' + (ok ? 'is-ok' : 'is-err');
  }

  function bindTriggers() {
    var raw = (cfg.triggerClasses || '').split(',');
    raw.forEach(function (sel) {
      sel = sel.trim();
      if (!sel) return;
      try {
        document.querySelectorAll(sel).forEach(function (el) {
          el.addEventListener('click', function (e) {
            e.preventDefault();
            openPopup();
          });
        });
      } catch (err) {}
    });

    document.addEventListener('click', function (e) {
      var t = e.target.closest('[data-dc-stw-open]');
      if (!t) return;
      e.preventDefault();
      openPopup();
    });
  }

  prizes = Array.isArray(cfg.prizes) ? cfg.prizes.filter(function (p) {
    return p && p.amount !== undefined;
  }) : [];
  build();
  bindTriggers();
  resize();
  requestAnimationFrame(loop);

  if (cfg.frequency !== 'button_only' && !alreadySeen() && prizes.length) {
    setTimeout(openPopup, 800);
  }

  window.DcSpinToWin = {
    open: openPopup,
    close: closePopup,
    load: function (list) {
      prizes = (list || []).filter(function (p) {
        return p && p.amount !== undefined;
      });
      rot = 0;
      mode = 'idle';
      anim = null;
      win.hidden = true;
      stage.classList.remove('busy');
      build();
    }
  };
  } // init

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      boot(0);
    });
  } else {
    boot(0);
  }
})();
