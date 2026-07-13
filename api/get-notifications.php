<?php
require_once __DIR__ . '/../php/lang.php';
session_start();
header('Content-Type: application/json');
require "../db/connect.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => t('api.err.unauthorized')]);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 20;
if ($limit <= 0 || $limit > 100) $limit = 20;

$stmt = $conn->prepare("SELECT id, type, title, message, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
$stmt->bind_param("ii", $user_id, $limit);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = [
        'id' => (int) $row['id'],
        'type' => $row['type'],
        'title' => $row['title'],
        'text' => $row['message'],
        'read' => (bool) $row['is_read'],
        'created_at' => $row['created_at']
    ];
}
$stmt->close();

echo json_encode($notifications);
$conn->close();
