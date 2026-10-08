<?php
require 'db.php';
header('Content-Type: application/json');

$stmt = $pdo->query(
    "SELECT id, incident_type, severity, description, lga, latitude, longitude, reported_at
     FROM incidents
     WHERE reported_at >= NOW() - INTERVAL 30 DAY
     ORDER BY reported_at DESC"
);

echo json_encode($stmt->fetchAll());