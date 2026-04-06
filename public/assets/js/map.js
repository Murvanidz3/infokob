function initHomeMap() {
  var cfg = window.__HOME_MAP__;
  var el = document.getElementById('home-map-canvas');
  if (!cfg || !cfg.markers || !cfg.markers.length || !el || typeof google === 'undefined') {
    return;
  }
  var bounds = new google.maps.LatLngBounds();
  var map = new google.maps.Map(el, {
    zoom: 12,
    styles: [],
    mapTypeControl: false,
  });
  cfg.markers.forEach(function (m) {
    var pos = { lat: m.lat, lng: m.lng };
    bounds.extend(pos);
    var marker = new google.maps.Marker({
      position: pos,
      map: map,
      icon: {
        path: google.maps.SymbolPath.CIRCLE,
        scale: 8,
        fillColor: '#FF5A5F',
        fillOpacity: 1,
        strokeColor: '#fff',
        strokeWeight: 2,
      },
    });
    var esc = function (s) {
      return String(s || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/"/g, '&quot;');
    };
    var info = new google.maps.InfoWindow({
      content:
        '<div style="font-family:Inter,sans-serif;max-width:220px;padding:6px;font-size:13px;"><strong>' +
        esc(m.title) +
        '</strong><br>' +
        esc(m.price) +
        '<br><a href="' +
        esc(m.url) +
        '">→</a></div>',
    });
    marker.addListener('click', function () {
      info.open(map, marker);
    });
  });
  map.fitBounds(bounds);
  var listener = google.maps.event.addListener(map, 'idle', function () {
    if (map.getZoom() > 15) map.setZoom(15);
    google.maps.event.removeListener(listener);
  });
}
window.initHomeMap = initHomeMap;
