<?php
require __DIR__ . '/includes/auth.php';
require dirname(__DIR__) . '/includes/db.php';

requireAdminAccount();

$pageTitle = 'Overview';

// ---------- 1. Report counts by status (all time) ----------
$counts = $pdo->query(
    "SELECT COUNT(*)                  AS total,
            SUM(status = 'reported')  AS pending,
            SUM(status = 'verified')  AS verified,
            SUM(status = 'resolved')  AS resolved
     FROM incidents"
)->fetch();

$farmers = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();

// ---------- 2. Early-warning score per area, last 7 days ----------
// Same rule as includes/risk.php: score = sum of severity values
$riskRows = $pdo->query(
    "SELECT lga, COUNT(*) AS reports, SUM(severity) AS score
     FROM incidents
     WHERE reported_at >= NOW() - INTERVAL 7 DAY
     GROUP BY lga
     ORDER BY score DESC
     LIMIT 8"
)->fetchAll();

// Thresholds must match includes/risk.php
function riskLevel(int $score): string
{
    if ($score >= 10) return 'critical';
    if ($score >= 6)  return 'high';
    if ($score >= 3)  return 'medium';
    return 'low';
}

// ---------- 3. Reports waiting for review (most severe first) ----------
$pending = $pdo->query(
    "SELECT i.incident_type, i.severity, i.lga, i.reported_at, u.full_name
     FROM incidents i
     LEFT JOIN users u ON u.id = i.user_id
     WHERE i.status = 'reported'
     ORDER BY i.severity DESC, i.reported_at DESC
     LIMIT 6"
)->fetchAll();

$severityLabels = [1 => 'Low', 2 => 'Medium', 3 => 'High'];

require __DIR__ . '/includes/admin_header.php';
?>

<div class="dash-container">

    <section class="dash-welcome">
        <div>
            <h2>Admin overview</h2>
            <p>Review incoming reports and watch which areas are heating up.</p>
        </div>
        <a href="reports.php?status=reported" class="btn-light">Review pending reports</a>
    </section>

    <section class="dash-stats stats-5">
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
                <span class="dash-label">Pending review</span>
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
        <div class="dash-stat stat-farmers">
            <span class="stat-icon">👥</span>
            <div>
                <span class="dash-number"><?= $farmers ?></span>
                <span class="dash-label">Farmers</span>
            </div>
        </div>
    </section>

    <div class="admin-grid">

        <section class="dash-card">
            <h3>Area risk (last 7 days)</h3>

            <?php if (!$riskRows): ?>
                <p class="dash-empty"><span class="empty-icon">🌿</span>No reports in the last 7 days.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="dash-table">
                        <thead>
                            <tr><th>Area</th><th>Reports</th><th>Score</th><th>Risk</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riskRows as $row): ?>
                                <?php $level = riskLevel((int)$row['score']); ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['lga']) ?></td>
                                    <td><?= (int)$row['reports'] ?></td>
                                    <td><?= (int)$row['score'] ?></td>
                                    <td><span class="badge risk-<?= $level ?>"><?= strtoupper($level) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <section class="dash-card">
            <h3>Waiting for review</h3>

            <?php if (!$pending): ?>
                <p class="dash-empty"><span class="empty-icon">🎉</span>Nothing is waiting. All reports have been reviewed.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="dash-table">
                        <thead>
                            <tr><th>Date</th><th>Farmer</th><th>Problem</th><th>Area</th><th>Severity</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars(date('d M, H:i', strtotime($p['reported_at']))) ?></td>
                                    <td><?= $p['full_name'] ? htmlspecialchars($p['full_name']) : '<span class="muted">Anonymous</span>' ?></td>
                                    <td><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $p['incident_type']))) ?></td>
                                    <td><?= htmlspecialchars($p['lga']) ?></td>
                                    <td>
                                        <span class="badge badge-sev-<?= (int)$p['severity'] ?>">
                                            <?= $severityLabels[$p['severity']] ?? '-' ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p><a href="reports.php?status=reported">See all pending reports →</a></p>
            <?php endif; ?>
        </section>

    </div>
</div>

<?php require dirname(__DIR__) . '/includes/user_footer.php'; ?>