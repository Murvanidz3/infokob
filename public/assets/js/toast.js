(function () {
  window.Toast = {
    show: function (message, type) {
      type = type || 'success';
      var c = document.getElementById('toast-container');
      if (!c) {
        c = document.createElement('div');
        c.id = 'toast-container';
        c.className = 'toast-container';
        document.body.appendChild(c);
      }
      var t = document.createElement('div');
      t.className = 'toast toast--' + type;
      t.setAttribute('role', 'status');
      t.innerHTML =
        '<span class="toast__msg">' +
        String(message).replace(/</g, '&lt;') +
        '</span><button type="button" class="toast__close" aria-label="Close">×</button>';
      var close = function () {
        t.remove();
      };
      t.querySelector('.toast__close').addEventListener('click', close);
      c.appendChild(t);
      setTimeout(close, 4000);
    },
  };
})();
