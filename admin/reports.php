<?php
require __DIR__ . '/includes/auth.php';
require dirname(__DIR__) . '/includes/db.php';

requireAdminAccount();

$pageTitle = 'All Reports';

// ---------- Which tab is selected? Only accept known values ----------
$tabs   = ['all' => 'All', 'reported' => 'Pending', 'verified' => 'Verified', 'resolved' => 'Resolved'];
$filter = $_GET['status'] ?? 'all';
if (!array_key_exists($filter, $tabs)) {
    $filter = 'all';
}

// ---------- Load reports together with who sent them ----------
$sql = 'SELECT i.id, i.incident_type, i.severity, i.description, i.lga,
               i.latitude, i.longitude, i.status, i.reported_at,
               u.full_name, u.email, u.phone
        FROM incidents i
        LEFT JOIN users u ON u.id = i.user_id';
$params = [];

if ($filter !== 'all') {
    $sql .= ' WHERE i.status = ?';
    $params[] = $filter;
}

$sql .= ' ORDER BY i.reported_at DESC LIMIT 100';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll();

// ---------- One-time message from update_status.php ----------
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$severityLabels = [1 => 'Low', 2 => 'Medium', 3 => 'High'];
$statusLabels   = ['reported' => 'Pending', 'verified' => 'Verified', 'resolved' => 'Resolved'];

require __DIR__ . '/includes/admin_header.php';
?>

<div class="dash-container">

    <h2>All reports</h2>

    <?php if ($flash): ?>
        <div class="flash"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <nav class="filter-tabs">
        <?php foreach ($tabs as $key => $label): ?>
            <a href="reports.php?status=<?= $key ?>" class="<?= $filter === $key ? 'active' : '' ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <section class="dash-card">
        <?php if (!$reports): ?>
            <p class="dash-empty"><span class="empty-icon">📭</span>No reports in this view.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="dash-table reports-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reporter</th>
                            <th>Problem</th>
                            <th>Location</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($r['reported_at']))) ?><br>
                                    <small><?= htmlspecialchars(date('H:i', strtotime($r['reported_at']))) ?></small></td>

                                <td class="col-reporter">
                                    <?php if ($r['full_name']): ?>
                                        <strong><?= htmlspecialchars($r['full_name']) ?></strong><br>
                                        <a href="mailto:<?= htmlspecialchars($r['email']) ?>"><?= htmlspecialchars($r['email']) ?></a><br>
                                        <a href="tel:<?= htmlspecialchars($r['phone']) ?>"><?= htmlspecialchars($r['phone']) ?></a>
                                    <?php else: ?>
                                        <span class="muted">Anonymous (no account)</span>
                                    <?php endif; ?>
                                </td>

                                <td class="col-problem">
                                    <strong><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $r['incident_type']))) ?></strong><br>
                                    <?= $r['description'] !== '' ? htmlspecialchars($r['description']) : '<span class="muted">No description</span>' ?>
                                </td>

                                <td class="col-location">
                                    <?= htmlspecialchars($r['lga']) ?><br>
                                    <small><?= htmlspecialchars(number_format((float)$r['latitude'], 5)) ?>,
                                           <?= htmlspecialchars(number_format((float)$r['longitude'], 5)) ?></small><br>
                                    <a class="map-link" target="_blank" rel="noopener"
                                       href="https://www.google.com/maps?q=<?= htmlspecialchars($r['latitude']) ?>,<?= htmlspecialchars($r['longitude']) ?>">
                                        Open in Google Maps ↗
                                    </a>
                                </td>

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

                                <td>
                                    <?php if ($r['status'] === 'resolved'): ?>
                                        <span class="done-text">✔ Done</span>
                                    <?php else: ?>
                                        <form method="POST" action="update_status.php" class="inline-form">
                                            <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">
                                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                            <input type="hidden" name="return" value="<?= htmlspecialchars($filter) ?>">

                                            <?php if ($r['status'] === 'reported'): ?>
                                                <button type="submit" name="status" value="verified" class="btn-sm btn-verify">Verify</button>
                                            <?php endif; ?>
                                            <button type="submit" name="status" value="resolved" class="btn-sm btn-resolve">Resolve</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="muted">Showing the latest <?= count($reports) ?> reports (up to 100).</p>
        <?php endif; ?>
    </section>
</div>

<?php require dirname(__DIR__) . '/includes/user_footer.php'; ?>