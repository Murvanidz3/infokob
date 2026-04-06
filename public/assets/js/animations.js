(function () {
  function animateCount(el) {
    var target = parseInt(el.getAttribute('data-count'), 10) || 0;
    var span = el.querySelector('.js-count');
    if (!span) return;
    var duration = 900;
    var start = 0;
    var t0 = performance.now();
    function tick(now) {
      var p = Math.min(1, (now - t0) / duration);
      var v = Math.round(start + (target - start) * (0.5 - Math.cos(p * Math.PI) / 2));
      span.textContent = String(v);
      if (p < 1) {
        requestAnimationFrame(tick);
      }
    }
    requestAnimationFrame(tick);
  }

  function initReveal() {
    var els = document.querySelectorAll('.reveal, .reveal-stagger > *');
    if (!els.length || !('IntersectionObserver' in window)) {
      els.forEach(function (el) {
        el.classList.add('is-visible');
      });
      return;
    }
    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            io.unobserve(entry.target);
          }
        });
      },
      { rootMargin: '0px 0px -40px 0px', threshold: 0.08 }
    );
    els.forEach(function (el) {
      io.observe(el);
    });
  }

  function initStats() {
    var pills = document.querySelectorAll('.stat-pill[data-count]');
    if (!pills.length || !('IntersectionObserver' in window)) {
      pills.forEach(function (el) {
        animateCount(el);
      });
      return;
    }
    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            animateCount(entry.target);
            io.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.2 }
    );
    pills.forEach(function (el) {
      io.observe(el);
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initReveal();
    initStats();
  });
})();
