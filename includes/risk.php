<?php
require 'db.php';

// Score each LGA over the last 7 days.
// Severity is the weight: a high-severity report counts for more.
$stmt = $pdo->query(
    "SELECT lga,
            COUNT(*) AS reports,
            SUM(severity) AS score
     FROM incidents
     WHERE reported_at >= NOW() - INTERVAL 7 DAY
     GROUP BY lga
     ORDER BY score DESC"
);

foreach ($stmt->fetchAll() as $row) {
    if ($row['score'] >= 10)      $level = 'CRITICAL';
    elseif ($row['score'] >= 6)   $level = 'HIGH';
    elseif ($row['score'] >= 3)   $level = 'MEDIUM';
    else                          $level = 'LOW';

    echo "{$row['lga']}: {$row['reports']} reports, score {$row['score']} → $level<br>";

    if ($level === 'HIGH' || $level === 'CRITICAL') {
        // Send an alert (see below)
        mail('mediator@example.com',
             "Conflict warning: {$row['lga']}",
             "{$row['lga']} has reached $level risk ({$row['reports']} reports in 7 days).");
    }
}