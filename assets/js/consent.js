(function () {
  var NAME = 'cookie_consent';
  var DAYS = 182;
  function get() {
    var m = document.cookie.match(new RegExp('(?:^|; )' + NAME + '=([^;]*)'));
    if (!m) return null;
    try { return JSON.parse(decodeURIComponent(m[1])); } catch (e) { return null; }
  }
  function set(v) {
    var d = new Date();
    d.setTime(d.getTime() + DAYS * 864e5);
    var val = encodeURIComponent(JSON.stringify(v));
    var secure = location.protocol === 'https:' ? ';Secure' : '';
    document.cookie = NAME + '=' + val + ';expires=' + d.toUTCString() + ';path=/' + secure + ';SameSite=Lax';
  }
  function save(analytics, reload) {
    set({ necessary: true, analytics: !!analytics, t: new Date().toISOString() });
    try { localStorage.removeItem('cookieConsentAccepted'); } catch (e) {}
    if (reload !== false) location.reload();
  }
  function dismiss(banner) {
    set({ necessary: true, analytics: false, t: new Date().toISOString() });
    try { localStorage.removeItem('cookieConsentAccepted'); } catch (e) {}
    show(banner, false);
  }
  function show(el, on) { if (!el) return; el.classList.toggle('hidden', !on); el.classList.toggle('flex', on); }
  document.addEventListener('DOMContentLoaded', function () {
    var banner = document.getElementById('cookie-banner');
    var modal = document.getElementById('cookie-preferences-modal');
    var box = document.getElementById('cookie-pref-analytics');
    if (!banner) return;
    show(banner, !get());
    var cur = get();
    if (box) box.checked = !!(cur && cur.analytics);
    var acceptBtn = document.getElementById('accept-cookie-banner');
    var rejectBtn = document.getElementById('reject-cookie-banner');
    var customBtn = document.getElementById('customize-cookie-banner');
    var closeBannerBtn = document.getElementById('close-cookie-banner');
    var closeBtn = document.getElementById('close-cookie-preferences');
    var saveBtn = document.getElementById('save-cookie-preferences');
    if (acceptBtn) acceptBtn.onclick = function () { save(true); };
    if (rejectBtn) rejectBtn.onclick = function () { save(false); };
    if (closeBannerBtn) closeBannerBtn.onclick = function () { dismiss(banner); };
    if (customBtn) customBtn.onclick = function () { show(modal, true); };
    if (closeBtn) closeBtn.onclick = function () { show(modal, false); };
    if (saveBtn) saveBtn.onclick = function () { save(box && box.checked); };
    window.openCookiePreferences = function () { var c = get(); if (box) box.checked = !!(c && c.analytics); show(modal, true); };
  });
})();
