/* White Friday landing page (template/white-friday.php) — ported from the campaign concepts */
(function () {
  var DATA = window.WF_DATA || {};
  var T = DATA.i18n || {};
  var START = new Date(DATA.start);
  var END = new Date(DATA.end);

  var pad = function (n) { return String(n).padStart(2, '0'); };

  /* ---------- Toast ---------- */
  var toastTimer;
  function toast(msg) {
    var t = document.querySelector('[data-toast]');
    if (!t) return;
    t.textContent = msg;
    t.classList.add('is-on');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { t.classList.remove('is-on'); }, 2200);
  }

  /* ---------- Countdowns ---------- */
  // <div data-countdown> … [data-d] [data-h] [data-m] [data-s]; [data-countdown-label] gets the phase text
  function tick() {
    var now = new Date();
    var live = now >= START && now <= END;
    var target = now < START ? START : END;
    var diff = Math.max(0, target - now);
    var d = Math.floor(diff / 864e5), h = Math.floor(diff / 36e5) % 24, m = Math.floor(diff / 6e4) % 60, s = Math.floor(diff / 1e3) % 60;

    document.querySelectorAll('[data-countdown]').forEach(function (el) {
      var set = function (k, v) { var n = el.querySelector('[data-' + k + ']'); if (n) n.textContent = pad(v); };
      set('d', d); set('h', h); set('m', m); set('s', s);
    });
    document.querySelectorAll('[data-countdown-label]').forEach(function (el) {
      el.textContent = now > END ? T.ended : live ? T.endsIn : T.startsIn;
    });
  }
  tick();
  setInterval(tick, 1000);

  /* ---------- Gift box (hero + popup share one saved prize) ---------- */
  var PRIZES = DATA.prizes || [];
  var KEY = 'sondos-wf-prize-ship';
  var gifts = document.querySelectorAll('[data-wf-gift]');

  function pick() {
    var total = PRIZES.reduce(function (s, p) { return s + p.w; }, 0), r = Math.random() * total;
    for (var i = 0; i < PRIZES.length; i++) { r -= PRIZES[i].w; if (r <= 0) return PRIZES[i]; }
    return PRIZES[0];
  }
  function show(stage, p, burst) {
    stage.querySelector('[data-gift-box]').classList.add('is-open');
    stage.querySelector('[data-prize-title]').textContent = p.title;
    stage.querySelector('[data-prize-code]').textContent = p.code;
    stage.querySelector('[data-prize-copy]').setAttribute('data-copy', p.code);
    stage.querySelector('[data-gift-prize]').classList.add('is-on');
    if (burst) confetti(stage.querySelector('[data-gift-confetti]'));
  }
  function confetti(box) {
    var colors = ['hsl(43 50% 57%)', 'hsl(275 30% 32%)', 'hsl(43 62% 75%)', '#fff'];
    for (var i = 0; i < 60; i++) {
      var c = document.createElement('i');
      c.style.background = colors[i % colors.length];
      c.style.setProperty('--x', (Math.random() * 500 - 250) + 'px');
      c.style.setProperty('--y', (Math.random() * -320 + 40) + 'px');
      c.style.setProperty('--r', (Math.random() * 720) + 'deg');
      c.style.animationDelay = Math.random() * .2 + 's';
      box.appendChild(c);
    }
    setTimeout(function () { box.innerHTML = ''; }, 1800);
  }

  // one try per visitor (on the live site the coupon should be issued server-side)
  var saved = null;
  try { saved = JSON.parse(localStorage.getItem(KEY)); } catch (e) {}
  if (saved) gifts.forEach(function (g) { show(g, saved, false); });

  gifts.forEach(function (stage) {
    stage.querySelector('[data-gift-box]').addEventListener('click', function () {
      if (this.classList.contains('is-open')) return;
      var p = pick();
      try { localStorage.setItem(KEY, JSON.stringify(p)); } catch (e) {}
      gifts.forEach(function (g) { show(g, p, g === stage); });
    });
  });

  /* ---------- Popups (discount box + reminder) ---------- */
  var lastFocus = null;
  function openPopup(id) {
    var popup = document.getElementById(id);
    if (!popup) return;
    lastFocus = document.activeElement;
    popup.hidden = false;
    document.body.classList.add('wf-popup-open');
    var first = popup.querySelector('[data-gift-box], input:not([type="hidden"]):not(.wf-hp):not([type="radio"])');
    if (first) first.focus();
  }
  function closePopup() {
    var popup = document.querySelector('.wf-popup:not([hidden])');
    if (!popup) return;
    popup.hidden = true;
    document.body.classList.remove('wf-popup-open');
    if (lastFocus) lastFocus.focus();
  }
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closePopup(); });

  /* ---------- Nights (concept 3) ---------- */
  var NIGHTS = DATA.nights || [];
  var REMIND_KEY = 'sondos-wf-reminders';
  function reminded() {
    try { return JSON.parse(localStorage.getItem(REMIND_KEY)) || []; } catch (e) { return []; }
  }
  var grid = document.querySelector('[data-nights-grid]');
  var esc = function (v) {
    return String(v == null ? '' : v).replace(/[&<>"']/g, function (c) { return '&#' + c.charCodeAt(0) + ';'; });
  };
  // each night has its own open / close time (Dashboard → White Friday → Offers schedule)
  function nightState(n, now) {
    return now < new Date(n.start) ? 'locked' : now < new Date(n.end) ? 'live' : 'done';
  }
  function nightById(id) {
    for (var i = 0; i < NIGHTS.length; i++) if (NIGHTS[i].id === id) return NIGHTS[i];
    return null;
  }
  function renderNights() {
    if (!grid) return;
    var now = new Date();
    var done = reminded();
    grid.innerHTML = NIGHTS.map(function (n) {
      var on = done.indexOf(n.id) > -1;
      var st = nightState(n, now);
      var label = st === 'live' ? T.live : st === 'done' ? T.done : T.locked;
      return '<article class="night' + (n.image ? ' night--img' : '') + '" data-state="' + st + '">' +
        (n.image ? '<div class="night__img"><img src="' + esc(n.image) + '" alt="" loading="lazy"></div>' : '') +
        '<span class="night__day">' + esc(n.day) + '</span><span class="night__date">' + esc(n.date) + '</span>' +
        '<h3>' + esc(n.title) + '</h3>' +
        (n.price ? '<div class="night__price">' + esc(n.price) + (n.regular ? ' <del>' + esc(n.regular) + '</del>' : '') + '</div>' : '') +
        (n.pct ? '<div class="night__pct">' + T.discountUpTo + ' <b>' + n.pct + '%</b></div>' : '') +
        '<div class="night__foot"><span class="night__state">' + label + '</span>' +
        (st === 'locked' ? '<button type="button" class="night__bell' + (on ? ' is-on' : '') + '" data-bell="' + n.id + '">' + (on ? T.reminded : T.remind) + '</button>' : st === 'live' ? '<a href="' + esc(n.link || '#deals') + '" class="btn btn-gold btn-sm btn-block" style="margin-top:10px">' + T.enterNight + '</a>' : '') +
        '</div></article>';
    }).join('');
  }
  renderNights();
  setInterval(renderNights, 60000);

  /* ---------- Clicks: copy code, reminders, popup ---------- */
  document.addEventListener('click', function (e) {
    var copy = e.target.closest('[data-copy]');
    if (copy) {
      var code = copy.getAttribute('data-copy');
      if (code) {
        (navigator.clipboard ? navigator.clipboard.writeText(code) : Promise.reject()).then(
          function () { toast(T.copied + ' ' + code); },
          function () { toast(T.code + ' ' + code); }
        );
      }
    }

    var bell = e.target.closest('[data-bell]');
    if (bell) {
      var night = +bell.getAttribute('data-bell');
      if (bell.classList.contains('is-on')) {
        toast(T.remindAlready);
      } else {
        var rp = document.getElementById('wf-reminder-popup');
        rp.querySelector('[name="night"]').value = night;
        var nt = nightById(night);
        rp.querySelector('[data-reminder-day]').textContent = nt.day + ' · ' + nt.date;
        rp.querySelector('[data-reminder-title]').textContent = nt.title + (nt.pct ? ' · ' + T.discountUpTo + ' ' + nt.pct + '%' : '');
        openPopup('wf-reminder-popup');
      }
    }

    var opener = e.target.closest('[data-popup-open]');
    if (opener) openPopup(opener.getAttribute('aria-controls'));
    if (e.target.closest('[data-popup-close]')) closePopup();
  });

  /* ---------- Signups: reminder popup + early access form (saved in Dashboard → White Friday) ---------- */
  var CONTACT_KEY = 'sondos-wf-contact';

  // WhatsApp / email switch in the reminder popup
  var reminderForm = document.querySelector('[data-wf-signup="reminder"]');
  function setMethod(method) {
    var input = reminderForm.querySelector('[name="contact"]');
    var email = method === 'email';
    reminderForm.querySelectorAll('.tab').forEach(function (t) {
      t.setAttribute('aria-selected', String(t.querySelector('input').value === method));
    });
    reminderForm.querySelector('[data-contact-label]').textContent = email ? T.emailHint : T.phoneHint;
    input.type = email ? 'email' : 'tel';
    input.inputMode = email ? 'email' : 'tel';
    input.placeholder = email ? 'name@example.com' : '01xxxxxxxxx';
    input.style.textAlign = email ? 'left' : 'right';
    var saved = {};
    try { saved = JSON.parse(localStorage.getItem(CONTACT_KEY)) || {}; } catch (e) {}
    input.value = saved[method] || '';
  }
  if (reminderForm) {
    reminderForm.addEventListener('change', function (e) {
      if (e.target.name === 'method') setMethod(e.target.value);
    });
    setMethod('whatsapp');
  }

  document.querySelectorAll('[data-wf-signup]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!form.reportValidity()) return;

      var type = form.getAttribute('data-wf-signup');
      var body = new FormData(form);
      body.append('action', 'elsondos_wf_signup');
      body.append('type', type);
      body.append('lang', DATA.lang || '');

      var btn = form.querySelector('[type="submit"]');
      btn.disabled = true;

      fetch(DATA.ajax, { method: 'POST', body: body, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!res || !res.success) throw new Error();

          if (type === 'reminder') {
            var night = +body.get('night');
            var list = reminded();
            if (list.indexOf(night) < 0) list.push(night);
            try {
              localStorage.setItem(REMIND_KEY, JSON.stringify(list));
              var saved = JSON.parse(localStorage.getItem(CONTACT_KEY) || '{}');
              saved[body.get('method')] = body.get('contact');
              localStorage.setItem(CONTACT_KEY, JSON.stringify(saved));
            } catch (err) {}
            renderNights();
            closePopup();
            toast(res.data.status === 'exists' ? T.remindAlready : T.remindOn);
          } else {
            form.reset();
            toast(T.registered);
          }
        })
        .catch(function () { toast(T.error); })
        .then(function () { btn.disabled = false; });
    });
  });

  /* ---------- Add to cart (WooCommerce ajax) ---------- */
  if (window.jQuery) {
    jQuery(document.body).on('added_to_cart', function (e, fragments, hash, $button) {
      var add = $button && $button[0];
      if (!add || !add.classList.contains('p-card__add')) return;
      var label = add.textContent;
      add.classList.add('is-added');
      add.textContent = T.added;
      setTimeout(function () { add.classList.remove('is-added'); add.textContent = label; }, 1600);
      toast(T.addedToast);
    });
  }

  /* ---------- Reveal on scroll ---------- */
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); } });
    }, { threshold: 0.12 });
    document.querySelectorAll('.wf-page .reveal').forEach(function (el) { io.observe(el); });
  } else {
    document.querySelectorAll('.wf-page .reveal').forEach(function (el) { el.classList.add('is-in'); });
  }
})();
