<?php
$pageTitle   = 'Report Incident';
$pageScripts = ['js/report.js'];
$pageStyles  = ['report.css'];

require dirname(__DIR__) . '/includes/header.php';
?>

<div class="form-container">
    <h2>Report an Incident</h2>

    <?php if (isLoggedIn()): ?>
    <p class="status-ok">Reporting as <?= htmlspecialchars($_SESSION['name']) ?>. This report will appear on your dashboard.</p>
<?php else: ?>
    <p>You are reporting anonymously. <a href="login.php">Log in</a> to track your reports.</p>
<?php endif; ?>

    <form action="submit_report.php" method="POST">
        <label for="incident_type">Type</label>
        <select name="incident_type" id="incident_type" required>
            <option value="crop_damage">Crop damage</option>
            <option value="grazing_dispute">Grazing dispute</option>
            <option value="water_dispute">Water dispute</option>
            <option value="violence">Violence</option>
            <option value="tension">Rising tension</option>
        </select>

        <label for="severity">Severity</label>
        <select name="severity" id="severity" required>
            <option value="1">Low</option>
            <option value="2">Medium</option>
            <option value="3">High</option>
        </select>

        <label for="lga">LGA / Area</label>
        <input type="text" name="lga" id="lga" required>

        <label for="description">Description</label>
        <textarea name="description" id="description"></textarea>

        <input type="hidden" name="latitude" id="lat">
        <input type="hidden" name="longitude" id="lng">

        <button type="submit" id="submit-btn" disabled>Submit</button>
        <p id="gps-status" role="status" aria-live="polite">Getting your location...</p>
    </form>
</div>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>