<?php
require dirname(__DIR__) . '/includes/auth.php';
requireLogin();   // check BEFORE saving anything

$pageTitle  = 'Submit Incident Report';
$pageStyles = ['report.css'];

$success = false;
$message = 'Please submit the report using the incident form.';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
} else {
    $type           = $_POST['incident_type'] ?? null;
    $severityValue  = $_POST['severity'] ?? null;
    $lga            = $_POST['lga'] ?? null;
    $description    = $_POST['description'] ?? '';
    $latitudeValue  = $_POST['latitude'] ?? null;
    $longitudeValue = $_POST['longitude'] ?? null;

    $allowedTypes = ['crop_damage', 'grazing_dispute', 'water_dispute', 'violence', 'tension'];
    $severity  = is_string($severityValue) ? filter_var($severityValue, FILTER_VALIDATE_INT) : false;
    $latitude  = is_string($latitudeValue) ? filter_var($latitudeValue, FILTER_VALIDATE_FLOAT) : false;
    $longitude = is_string($longitudeValue) ? filter_var($longitudeValue, FILTER_VALIDATE_FLOAT) : false;
    $lga         = is_string($lga) ? trim($lga) : '';
    $description = is_string($description) ? trim($description) : null;

    $valid = is_string($type)
        && in_array($type, $allowedTypes, true)
        && $severity !== false
        && $severity >= 1
        && $severity <= 3
        && $lga !== ''
        && $description !== null
        && $latitude !== false
        && $latitude >= -90
        && $latitude <= 90
        && $longitude !== false
        && $longitude >= -180
        && $longitude <= 180;

    if (!$valid) {
        http_response_code(422);
        $message = 'Some report details are missing or invalid. Please go back, check the form, and try again.';
    } else {
        require dirname(__DIR__) . '/includes/db.php';

        // The farmer's id always comes from the session, never from the form
        $userId = (int)$_SESSION['user_id'];

        $stmt = $pdo->prepare(
            'INSERT INTO incidents (incident_type, severity, description, lga, latitude, longitude, user_id)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$type, $severity, $description, $lga, $latitude, $longitude, $userId]);

        $success = true;
        $message = 'Your incident report has been submitted. Thank you for helping your community.';
    }
}

require dirname(__DIR__) . '/includes/user_header.php';
?>

<div class="submission-container">
    <section class="submission-card <?= $success ? 'submission-success' : 'submission-error' ?>" role="status">
        <h2><?= $success ? 'Report submitted' : 'Report not submitted' ?></h2>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        <a class="submission-link" href="<?= $success ? 'dashboard.php' : 'report.php' ?>">
            <?= $success ? 'View my dashboard' : 'Return to the report form' ?>
        </a>
    </section>
</div>

<?php require dirname(__DIR__) . '/includes/user_footer.php'; ?>