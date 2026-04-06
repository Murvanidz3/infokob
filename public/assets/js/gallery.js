(function () {
  document.addEventListener('DOMContentLoaded', function () {
    var lb = document.getElementById('property-lightbox');
    var lbImg = document.getElementById('property-lightbox-img');
    if (!lb || !lbImg) return;

    function openLightbox(src) {
      if (!src) return;
      lbImg.src = src;
      lb.hidden = false;
      document.body.style.overflow = 'hidden';
    }

    function closeLb() {
      lb.hidden = true;
      document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-lightbox]').forEach(function (el) {
      el.addEventListener('click', function (e) {
        e.preventDefault();
        var src = el.getAttribute('data-lightbox') || el.getAttribute('data-full');
        openLightbox(src);
      });
    });

    var closeBtn = lb.querySelector('.lightbox__close');
    if (closeBtn) closeBtn.addEventListener('click', closeLb);
    lb.addEventListener('click', function (e) {
      if (e.target === lb) closeLb();
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !lb.hidden) closeLb();
    });
  });
})();
