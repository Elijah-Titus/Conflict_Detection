<?php
require __DIR__ . '/includes/auth.php';
require dirname(__DIR__) . '/includes/db.php';

requireAdminAccount();

// Only accept a POST with a valid CSRF token
if ($_SERVER['REQUEST_METHOD'] !== 'POST'
    || !verifyCsrf(isset($_POST['csrf']) && is_string($_POST['csrf']) ? $_POST['csrf'] : null)) {
    http_response_code(400);
    exit('Invalid request.');
}

$id     = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
$status = $_POST['status'] ?? '';
$return = $_POST['return'] ?? 'all';

// Admins may only set these two statuses
$allowedStatuses = ['verified', 'resolved'];

if ($id === false || !in_array($status, $allowedStatuses, true)) {
    http_response_code(400);
    exit('Invalid request.');
}

$stmt = $pdo->prepare('UPDATE incidents SET status = ? WHERE id = ?');
$stmt->execute([$status, $id]);

$_SESSION['flash'] = "Report #{$id} marked as {$status}.";

// Go back to the tab the admin was on
$back = in_array($return, ['reported', 'verified', 'resolved'], true) ? '?status=' . $return : '';
header('Location: reports.php' . $back);
exit;