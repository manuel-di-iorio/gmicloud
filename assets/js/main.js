// Sidebar
function w3_open() {
  var nav = document.getElementById("navbar");
  var overlay = document.getElementById("overlay");
  if (nav) nav.classList.add("ds-sidebar--visible");
  if (overlay) overlay.style.display = "block";
}

function w3_close() {
  var nav = document.getElementById("navbar");
  var overlay = document.getElementById("overlay");
  if (nav) nav.classList.remove("ds-sidebar--visible");
  if (overlay) overlay.style.display = "none";
}

// Modals
function openModal(modalId, onOpen, ctx) {
  var el = document.getElementById(modalId);
  if (!el) return;
  el.classList.remove('hidden');
  el.removeAttribute('data-armed');
  var btn = el.querySelector('.ui-destructive');
  if (btn) {
    btn.innerHTML = btn.getAttribute('data-original-html') || btn.innerHTML;
    btn.classList.remove('is-armed', 'animate-confirm-pulse');
  }
  if (onOpen) onOpen(ctx);
}

function closeModal(modalId, onClose, ctx) {
  var el = document.getElementById(modalId);
  if (!el) return;
  el.classList.add('hidden');
  if (onClose) onClose(ctx);
}

// Two-click destructive confirmation (capture phase to intercept before onclick)
document.addEventListener('click', function(e) {
  var btn = e.target.closest('.ui-destructive');
  if (!btn) return;
  var overlay = btn.closest('.ds-modal-overlay');
  if (!overlay) return;
  if (overlay.getAttribute('data-armed') === '1') {
    overlay.removeAttribute('data-armed');
    btn.classList.remove('is-armed', 'animate-confirm-pulse');
    return;
  }
  e.stopPropagation();
  e.preventDefault();
  overlay.setAttribute('data-armed', '1');
  if (!btn.getAttribute('data-original-html')) {
    btn.setAttribute('data-original-html', btn.innerHTML);
  }
  btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Conferma?';
  btn.classList.add('is-armed', 'animate-confirm-pulse');
  setTimeout(function() {
    overlay.removeAttribute('data-armed');
    btn.innerHTML = btn.getAttribute('data-original-html') || btn.innerHTML;
    btn.classList.remove('is-armed', 'animate-confirm-pulse');
  }, 5000);
}, true);

// Accordions (legacy helper)
function toggleAccordion(el) {
  const elIcon = el.querySelector("i");
  if (!elIcon) return;
  direction = elIcon.className.includes("down") ? "up" : "down";
  elIcon.classList.remove("fa-arrow-circle-down");
  elIcon.classList.remove("fa-arrow-circle-up");
  elIcon.classList.add("fa-arrow-circle-" + direction);
  if (direction === "down") {
    el.nextElementSibling.style.display = "none";
  } else {
    el.nextElementSibling.style.display = "block";
  }
}

// FAQ accordion (flat, no animation library)
(function initFAQ() {
  document.querySelectorAll('.ds-faq__q').forEach(function(q) {
    q.addEventListener('click', function () {
      var item = this.parentElement;
      var isOpen = item.classList.contains('is-open');
      var list = item.parentElement;
      if (list) list.querySelectorAll('.ds-faq__item.is-open').forEach(function(open) { open.classList.remove('is-open'); });
      if (!isOpen) item.classList.add('is-open');
    });
  });
  // legacy class support
  document.querySelectorAll('.faq-question').forEach(function(q) {
    if (q.classList.contains('ds-faq__q')) return;
    q.addEventListener('click', function () {
      var item = this.parentElement;
      var isOpen = item.classList.contains('is-open');
      if (item.parentElement) item.parentElement.querySelectorAll('.faq-item.is-open').forEach(function(open) { open.classList.remove('is-open'); });
      if (!isOpen) item.classList.add('is-open');
    });
  });
})();

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
  anchor.addEventListener('click', function (e) {
    var hrefAttribute = this.getAttribute('href');
    if (hrefAttribute && hrefAttribute.length > 1) {
      var target = document.querySelector(hrefAttribute);
      if (target) {
        e.preventDefault();
        var offset = 80;
        var top = target.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top: top, behavior: 'smooth' });
      }
    }
  });
});

// Tabs — style driven by .is-active in CSS only
document.addEventListener('click', function (e) {
  var btn = e.target.closest('.ds-tabs__btn, .ui-tabs__btn');
  if (!btn) return;
  var tabsEl = btn.closest('.ds-tabs, .ui-tabs');
  if (!tabsEl) return;
  var tabId = btn.getAttribute('data-tab');
  if (!tabId) return;

  tabsEl.querySelectorAll('.ds-tabs__btn, .ui-tabs__btn').forEach(function (b) {
    b.classList.remove('is-active');
    b.setAttribute('aria-selected', 'false');
  });
  btn.classList.add('is-active');
  btn.setAttribute('aria-selected', 'true');

  tabsEl.querySelectorAll('.ds-tabs__panel, .ui-tabs__panel').forEach(function (p) {
    p.classList.remove('is-active');
    p.classList.add('hidden');
    p.hidden = true;
  });
  var panel = tabsEl.querySelector('#panel-' + tabId);
  if (panel) {
    panel.classList.add('is-active');
    panel.classList.remove('hidden');
    panel.hidden = false;
    panel.dispatchEvent(new CustomEvent('tabshown', { bubbles: true }));

    var url = panel.getAttribute('data-url');
    if (url && panel.getAttribute('data-loaded') === 'false') {
      panel.setAttribute('data-loaded', 'loading');
      fetch(url)
        .then(function (r) { return r.text(); })
        .then(function (html) {
          panel.innerHTML = html;
          panel.setAttribute('data-loaded', 'true');
          panel.querySelectorAll('script').forEach(function (s) {
            var ns = document.createElement('script');
            if (s.src) { ns.src = s.src; } else { ns.textContent = s.textContent; }
            s.parentNode.replaceChild(ns, s);
          });
          panel.dispatchEvent(new CustomEvent('tabloaded', { bubbles: true }));
          if (window.tippy) tippy(panel.querySelectorAll('[data-tippy-content]'));
        })
        .catch(function () {
          panel.setAttribute('data-loaded', 'false');
        });
    }
  }
});

// ActionsMenu dropdown toggle
document.addEventListener('click', function(e) {
  var trigger = e.target.closest('.ds-actions__trigger, .ui-actions-menu__trigger');
  if (trigger) {
    e.stopPropagation();
    var menu = trigger.closest('.ds-actions, .ui-actions-menu');
    var dropdown = menu.querySelector('.ds-actions__dropdown, .ui-actions-menu__dropdown');
    var isOpen = !dropdown.classList.contains('hidden');

    document.querySelectorAll('.ds-actions__dropdown, .ui-actions-menu__dropdown').forEach(function(d) {
      d.classList.add('hidden');
      var t = d.closest('.ds-actions, .ui-actions-menu').querySelector('.ds-actions__trigger, .ui-actions-menu__trigger');
      if (t) t.setAttribute('aria-expanded', 'false');
    });

    if (!isOpen) {
      dropdown.classList.remove('hidden');
      trigger.setAttribute('aria-expanded', 'true');
    }
    return;
  }

  document.querySelectorAll('.ds-actions__dropdown, .ui-actions-menu__dropdown').forEach(function(d) {
    d.classList.add('hidden');
    var t = d.closest('.ds-actions, .ui-actions-menu').querySelector('.ds-actions__trigger, .ui-actions-menu__trigger');
    if (t) t.setAttribute('aria-expanded', 'false');
  });
});

function fetchWithSpinner(opts) {
  var btn = opts.button;
  var spinner = opts.spinner;
  var fetchOpts = opts.fetch;
  var delay = opts.delay || 2000;
  var onSuccess = opts.onSuccess || function() {};
  var onError = opts.onError || function() {};

  btn.disabled = true;
  var showSpinnerTimer = setTimeout(function() {
    spinner.style.display = "inline-block";
  }, delay);

  fetch(fetchOpts.url, fetchOpts.options || {}).then(function(response) {
    return response.json();
  }).then(function(data) {
    clearTimeout(showSpinnerTimer);
    spinner.style.display = "none";
    btn.disabled = false;
    onSuccess(data);
  }).catch(function(err) {
    clearTimeout(showSpinnerTimer);
    spinner.style.display = "none";
    btn.disabled = false;
    onError(err);
  });
}
