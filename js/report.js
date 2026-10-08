// ---------- Get references to the page elements we need ----------
var latField  = document.getElementById('lat');
var lngField  = document.getElementById('lng');
var statusMsg = document.getElementById('gps-status');
var submitBtn = document.getElementById('submit-btn');

// ---------- Runs when the browser finds the location ----------
function onLocationFound(position) {
    latField.value = position.coords.latitude;
    lngField.value = position.coords.longitude;

    statusMsg.textContent = 'Location captured.';
    statusMsg.className = 'status-ok';
    submitBtn.disabled = false;   // allow submitting now
}

// ---------- Runs if location fails ----------
function onLocationError() {
    statusMsg.textContent =
        'Could not get your location. Please allow location access and reload the page.';
    statusMsg.className = 'status-error';
}

// ---------- Ask the browser for the user's position ----------
if ('geolocation' in navigator) {
    navigator.geolocation.getCurrentPosition(onLocationFound, onLocationError, {
        enableHighAccuracy: true,
        timeout: 15000
    });
} else {
    statusMsg.textContent = 'Your browser does not support location services.';
    statusMsg.className = 'status-error';
}