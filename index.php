<?php
require __DIR__ . '/includes/db.php';

$pageTitle = 'Conflict Warning System';
$pageStyles = ['home.css', 'map.css'];
$pageScripts = ['js/map.js'];
$extraHead = '
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
';
$base = '';   // this page is in the project root

// Quick statistics for the last 30 days
$total = $pdo->query(
    "SELECT COUNT(*) FROM incidents
     WHERE reported_at >= NOW() - INTERVAL 30 DAY"
)->fetchColumn();

$highSeverity = $pdo->query(
    "SELECT COUNT(*) FROM incidents
     WHERE severity = 3 AND reported_at >= NOW() - INTERVAL 30 DAY"
)->fetchColumn();

$areas = $pdo->query(
    "SELECT COUNT(DISTINCT lga) FROM incidents
     WHERE reported_at >= NOW() - INTERVAL 30 DAY"
)->fetchColumn();

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <h2>Early warning for farmer-herder conflict</h2>
        <p>
            A GIS-based system for reporting incidents and monitoring
            conflict risk in real time, so that communities and mediators
            can act before tensions escalate.
        </p>
        <div class="hero-buttons">
            <a href="Pages/report.php" class="btn btn-primary">Report an Incident</a>
            <a href="Pages/map.php" class="btn btn-secondary">View Live Map</a>
        </div>
    </div>
</section>

<section class="stats">
    <div class="stat-card">
        <span class="stat-number"><?= (int)$total ?></span>
        <span class="stat-label">Incidents (30 days)</span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?= (int)$highSeverity ?></span>
        <span class="stat-label">High severity</span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?= (int)$areas ?></span>
        <span class="stat-label">Areas affected</span>
    </div>
</section>

<section class="how-it-works">
    <h3>How it works</h3>
    <div class="steps">
        <div class="step">
            <strong>1. Report</strong>
            <p>Anyone in the field submits an incident with their GPS location.</p>
        </div>
        <div class="step">
            <strong>2. Monitor</strong>
            <p>Incidents appear on a live map and refresh automatically.</p>
        </div>
        <div class="step">
            <strong>3. Warn</strong>
            <p>Areas that cross a risk threshold trigger alerts to mediators.</p>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>