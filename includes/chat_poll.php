<?php
require_once __DIR__ . '/auth.php';
require __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$isAdminViewer = isset($_SESSION['admin_id']);

if ($isAdminViewer) {
    $userId = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
    if (!$userId) {
        http_response_code(400);
        echo json_encode(['error' => 'A valid user is required.']);
        exit;
    }

    $userCheck = $pdo->prepare('SELECT id FROM users WHERE id = ?');
    $userCheck->execute([$userId]);
    if (!$userCheck->fetchColumn()) {
        http_response_code(404);
        echo json_encode(['error' => 'User conversation not found.']);
        exit;
    }
} else {
    if (!isLoggedIn()) {
        http_response_code(403);
        echo json_encode(['error' => 'Authentication required.']);
        exit;
    }
    $userId = (int)$_SESSION['user_id'];
}

$afterId = filter_input(INPUT_GET, 'after_id', FILTER_VALIDATE_INT);
$afterId = $afterId !== false && $afterId !== null ? max(0, $afterId) : 0;

$stmt = $pdo->prepare(
    "SELECT m.id, m.sender_type, m.message_text, m.created_at,
            CASE WHEN m.sender_type = 'admin' THEN 'Admin' ELSE u.full_name END AS sender_name
     FROM chat_messages m
     JOIN users u ON u.id = m.user_id
     WHERE m.user_id = ? AND m.id > ?
     ORDER BY m.id ASC
     LIMIT 100"
);
$stmt->execute([$userId, $afterId]);

echo json_encode(['messages' => $stmt->fetchAll()], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
