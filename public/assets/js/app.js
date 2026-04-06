(function () {
  function onScroll() {
    var h = document.querySelector('.site-header');
    if (!h) return;
    if (window.scrollY > 12) {
      h.classList.add('is-scrolled');
    } else {
      h.classList.remove('is-scrolled');
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    var compactSearch = document.querySelector('.header-mini-search');
    if (compactSearch) {
      compactSearch.addEventListener('click', function () {
        var modal = document.getElementById('search-modal');
        if (modal) {
          modal.removeAttribute('aria-hidden');
          modal.hidden = false;
        }
      });
    }

    document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = btn.getAttribute('data-close-modal');
        var modal = id ? document.getElementById(id) : btn.closest('.search-modal');
        if (modal) {
          modal.setAttribute('aria-hidden', 'true');
          modal.hidden = true;
        }
      });
    }

    document.querySelectorAll('form[data-loading]').forEach(function (form) {
      form.addEventListener('submit', function () {
        var btn = form.querySelector('[type="submit"]');
        if (btn && !btn.disabled) {
          btn.classList.add('btn--loading');
          btn.setAttribute('disabled', 'disabled');
        }
      });
    });
  });
})();
