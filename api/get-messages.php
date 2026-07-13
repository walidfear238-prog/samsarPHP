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
$conversation_id = isset($_GET['conversation_id']) ? (int) $_GET['conversation_id'] : 0;

if (!$conversation_id) {
    http_response_code(400);
    echo json_encode(['error' => t('api.err.missing_conversation_id')]);
    exit;
}

$check = $conn->prepare("SELECT id FROM conversations WHERE id = ? AND (user_id = ? OR agency_id = ?)");
$check->bind_param("iii", $conversation_id, $user_id, $user_id);
$check->execute();
$owns = $check->get_result()->num_rows > 0;
$check->close();

if (!$owns) {
    http_response_code(403);
    echo json_encode(['error' => t('api.err.forbidden')]);
    exit;
}

$stmt = $conn->prepare("
    SELECT id, sender_id, message, is_read, created_at
    FROM messages
    WHERE conversation_id = ?
    ORDER BY created_at ASC, id ASC
");
$stmt->bind_param("i", $conversation_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'id' => (int) $row['id'],
        'sender_id' => (int) $row['sender_id'],
        'me' => (int) $row['sender_id'] === $user_id,
        'text' => $row['message'],
        'is_read' => (bool) $row['is_read'],
        'created_at' => $row['created_at']
    ];
}
$stmt->close();

$update = $conn->prepare("UPDATE messages SET is_read = 1 WHERE conversation_id = ? AND sender_id != ? AND is_read = 0");
$update->bind_param("ii", $conversation_id, $user_id);
$update->execute();
$update->close();

echo json_encode($messages);
$conn->close();
