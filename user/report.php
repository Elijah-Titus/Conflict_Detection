<?php
$pageTitle   = 'Report Incident';
$pageScripts = ['js/report.js'];
$pageStyles  = ['user-report.css'];

require dirname(__DIR__) . '/includes/user_header.php';
?>

<section class="user-report-page">
    <div class="user-report-heading">
        <span class="user-report-eyebrow">Community safety</span>
        <h2>Report an Incident</h2>
        <p>Share what happened so local responders can understand and monitor the situation.</p>
    </div>

    <div class="user-report-card">
        <p class="user-report-note">
            Reporting as <strong><?= htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8') ?></strong>.
            This report will appear on your dashboard.
        </p>

        <form action="submit_report.php" method="POST">
            <div class="user-report-field">
                <label for="incident_type">Incident type</label>
                <select name="incident_type" id="incident_type" required>
                    <option value="crop_damage">Crop damage</option>
                    <option value="grazing_dispute">Grazing dispute</option>
                    <option value="water_dispute">Water dispute</option>
                    <option value="violence">Violence</option>
                    <option value="tension">Rising tension</option>
                </select>
            </div>

            <div class="user-report-field">
                <label for="severity">Severity</label>
                <select name="severity" id="severity" required>
                    <option value="1">Low</option>
                    <option value="2">Medium</option>
                    <option value="3">High</option>
                </select>
            </div>

            <div class="user-report-field user-report-field-wide">
                <label for="lga">LGA / Area</label>
                <input type="text" name="lga" id="lga" autocomplete="address-level2" required>
            </div>

            <div class="user-report-field user-report-field-wide">
                <label for="description">Description <span>(optional)</span></label>
                <textarea name="description" id="description" placeholder="Add any details that may help responders understand the incident."></textarea>
            </div>

            <input type="hidden" name="latitude" id="lat">
            <input type="hidden" name="longitude" id="lng">

            <div class="user-report-submit">
                <p id="gps-status" role="status" aria-live="polite">Getting your location...</p>
                <button type="submit" id="submit-btn" disabled>Submit incident report</button>
            </div>
        </form>
    </div>
</section>

<?php require dirname(__DIR__) . '/includes/user_footer.php'; ?>