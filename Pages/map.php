<?php
$pageTitle = 'Live Conflict Map';

// Leaflet must load BEFORE our map.js, so it goes in the head
$extraHead = '
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
';

$pageScripts = ['js/map.js'];
$pageStyles = ['map.css'];

require dirname(__DIR__) . '/includes/header.php';
?>

<main class="public-map-page">
    <div id="map"></div>
</main>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>