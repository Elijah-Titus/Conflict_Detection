<?php
$pageTitle = 'Live Map';

// Leaflet must load BEFORE our map.js, so it goes in the head
$extraHead = '
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
';

$pageScripts = ['js/map.js'];

require dirname(__DIR__) . '/includes/user_header.php';
?>

<div id="map"></div>

<?php require dirname(__DIR__) . '/includes/user_footer.php'; ?>