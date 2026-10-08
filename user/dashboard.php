<?php
require dirname(__DIR__) . '/includes/auth.php';
require dirname(__DIR__) . '/includes/db.php';

requireLogin();

$pageTitle  = 'My Dashboard';
$pageStyles = ['dashboard.css'];

$userId = (int)$_SESSION['user_id'];

// ---------- 1. The farmer's own account details ----------
$stmt = $pdo->prepare('SELECT full_name, email, phone, created_at FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

// If the account no longer exists, end the session
if (!$user) {
    header('Location: ../pages/logout.php');
    exit;
}

// ---------- 2. Counts of this farmer's reports, by status ----------
$stmt = $pdo->prepare(
    "SELECT COUNT(*)                  AS total,
            SUM(status = 'reported')  AS pending,
            SUM(status = 'verified')  AS verified,
            SUM(status = 'resolved')  AS resolved
     FROM incidents
     WHERE user_id = ?"
);
$stmt->execute([$userId]);
$counts = $stmt->fetch();

// ---------- 3. The 10 most recent reports ----------
$stmt = $pdo->prepare(
    'SELECT incident_type, severity, lga, status, reported_at
     FROM incidents
     WHERE user_id = ?
     ORDER BY reported_at DESC
     LIMIT 10'
);
$stmt->execute([$userId]);
$reports = $stmt->fetchAll();

$severityLabels = [1 => 'Low', 2 => 'Medium', 3 => 'High'];
$statusLabels   = ['reported' => 'Pending', 'verified' => 'Verified', 'resolved' => 'Resolved'];

require dirname(__DIR__) . '/includes/user_header.php';
?>

<div class="dash-container">

    <section class="dash-welcome">
        <div>
            <h2>Welcome back, <?= htmlspecialchars($user['full_name']) ?> 👋</h2>
            <p>Track the incidents you have reported and see how they are being handled.</p>
        </div>
        <a href="report.php" class="btn-light">+ Report an Incident</a>
    </section>

    <section class="dash-stats">
        <div class="dash-stat stat-total">
            <span class="stat-icon">📋</span>
            <div>
                <span class="dash-number"><?= (int)$counts['total'] ?></span>
                <span class="dash-label">Total reports</span>
            </div>
        </div>
        <div class="dash-stat stat-pending">
            <span class="stat-icon">⏳</span>
            <div>
                <span class="dash-number"><?= (int)$counts['pending'] ?></span>
                <span class="dash-label">Pending</span>
            </div>
        </div>
        <div class="dash-stat stat-verified">
            <span class="stat-icon">✔️</span>
            <div>
                <span class="dash-number"><?= (int)$counts['verified'] ?></span>
                <span class="dash-label">Verified</span>
            </div>
        </div>
        <div class="dash-stat stat-resolved">
            <span class="stat-icon">🏁</span>
            <div>
                <span class="dash-number"><?= (int)$counts['resolved'] ?></span>
                <span class="dash-label">Resolved</span>
            </div>
        </div>
    </section>

    <section class="dash-card">
        <h3>My recent reports</h3>

        <?php if (!$reports): ?>
            <p class="dash-empty">
                <span class="empty-icon">🌱</span>
                You have not reported anything yet.<br>
                Use <strong>Report</strong> in the sidebar to submit your first case.
            </p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Problem</th>
                            <th>Area</th>
                            <th>Severity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars(date('d M Y, H:i', strtotime($r['reported_at']))) ?></td>
                                <td><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $r['incident_type']))) ?></td>
                                <td><?= htmlspecialchars($r['lga']) ?></td>
                                <td>
                                    <span class="badge badge-sev-<?= (int)$r['severity'] ?>">
                                        <?= $severityLabels[$r['severity']] ?? '-' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= htmlspecialchars($r['status']) ?>">
                                        <?= $statusLabels[$r['status']] ?? '-' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

</div>

<?php require dirname(__DIR__) . '/includes/user_footer.php'; ?>