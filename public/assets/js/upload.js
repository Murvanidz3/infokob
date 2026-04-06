/* Drag-drop + Sortable hookup for listing create — extend when #listing-images is present */
(function () {
  document.addEventListener('DOMContentLoaded', function () {
    var zone = document.getElementById('listing-upload-zone');
    var input = document.getElementById('listing-images');
    if (!zone || !input) return;

    zone.addEventListener('click', function () {
      input.click();
    });
    ['dragenter', 'dragover'].forEach(function (ev) {
      zone.addEventListener(ev, function (e) {
        e.preventDefault();
        zone.classList.add('is-dragover');
      });
    });
    ['dragleave', 'drop'].forEach(function (ev) {
      zone.addEventListener(ev, function (e) {
        e.preventDefault();
        zone.classList.remove('is-dragover');
      });
    });
    zone.addEventListener('drop', function (e) {
      if (e.dataTransfer && e.dataTransfer.files) {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      }
    });

    var grid = document.getElementById('listing-upload-grid');
    if (grid && typeof Sortable !== 'undefined') {
      Sortable.create(grid, { animation: 150 });
    }
  });
})();
