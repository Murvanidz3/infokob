(function () {
  document.querySelectorAll('.stat-pill[data-count]').forEach(function (el) {
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
  });
})();
