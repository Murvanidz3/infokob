(function () {
  var form = document.getElementById('listing-filters');
  if (!form) return;

  var grid = document.getElementById('listing-results');
  var countEl = document.getElementById('results-count');
  var pag = document.getElementById('pagination-wrap');
  var empty = document.getElementById('listing-empty');

  function showEmpty(show) {
    if (!empty) return;
    empty.style.display = show ? 'block' : 'none';
  }

  async function load() {
    if (!grid) return;
    grid.classList.add('is-loading');
    var snapshot = grid.innerHTML;
    grid.innerHTML = new Array(6)
      .fill('<div class="skeleton" style="height:280px;border-radius:12px"></div>')
      .join('');
    var qs = new URLSearchParams(new FormData(form));
    qs.set('ajax', '1');
    var url = form.getAttribute('action').split('?')[0] + '?' + qs.toString();
    try {
      var r = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      var j = await r.json();
      if (j.ok) {
        grid.innerHTML = j.html;
        if (pag) {
          pag.innerHTML = j.pagination || '';
        }
        if (countEl && j.countLabel) {
          countEl.textContent = j.countLabel;
        }
        showEmpty(!j.html || j.html.trim() === '');
      } else {
        grid.innerHTML = snapshot;
      }
    } catch (e) {
      console.error(e);
      grid.innerHTML = snapshot;
    } finally {
      grid.classList.remove('is-loading');
    }
  }

  form.addEventListener('change', function () {
    load();
  });
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    load();
  });
})();
