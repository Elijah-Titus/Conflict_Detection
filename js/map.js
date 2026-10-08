// ---------- Settings (change these for your study area) ----------
var MAP_CENTER = [7.38, 8.57];   // latitude, longitude
var MAP_ZOOM   = 8;
var REFRESH_MS = 30000;          // refresh every 30 seconds

var SEVERITY_COLORS = { 1: 'green', 2: 'orange', 3: 'red' };

// ---------- 1. Create the map ----------
var mapElement = document.getElementById('map');
var map = L.map(mapElement).setView(MAP_CENTER, MAP_ZOOM);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// A layer group lets us clear and redraw all markers in one go
var markers = L.layerGroup().addTo(map);

// ---------- 2. Legend ----------
var legend = L.control({ position: 'bottomright' });

legend.onAdd = function () {
    var div = L.DomUtil.create('div', 'legend');
    div.innerHTML =
        '<b>Severity</b><br>' +
        '<span class="legend-dot" style="background:green"></span>Low<br>' +
        '<span class="legend-dot" style="background:orange"></span>Medium<br>' +
        '<span class="legend-dot" style="background:red"></span>High';
    return div;
};

legend.addTo(map);

// ---------- 3. Helper: make text safe to insert into HTML ----------
function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}

// ---------- 4. Fetch incidents and draw them ----------
function loadIncidents() {
    fetch(mapElement.dataset.incidentsUrl || '../includes/api_incidents.php')
        .then(function (res) { return res.json(); })
        .then(function (data) {
            markers.clearLayers();   // remove old markers first

            data.forEach(function (i) {
                var popup =
                    '<b>' + escapeHtml(i.incident_type.replace('_', ' ')) + '</b><br>' +
                    escapeHtml(i.lga) + '<br>' +
                    escapeHtml(i.description) + '<br>' +
                    '<small>' + escapeHtml(i.reported_at) + '</small>';

                L.circleMarker([i.latitude, i.longitude], {
                    radius: 8,
                    color: SEVERITY_COLORS[i.severity],
                    fillOpacity: 0.7
                })
                .bindPopup(popup)
                .addTo(markers);
            });
        })
        .catch(function (err) {
            console.error('Could not load incidents:', err);
        });
}

// ---------- 5. Start ----------
loadIncidents();                        // load once immediately
setInterval(loadIncidents, REFRESH_MS); // then keep refreshing