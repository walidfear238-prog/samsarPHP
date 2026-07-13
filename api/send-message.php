<?php
session_start();
header('Content-Type: application/json');
require "../db/connect.php";
require_once __DIR__ . "/../php/lang.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => t('api.err.unauthorized')]);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

$conversation_id = isset($data['conversation_id']) ? (int) $data['conversation_id'] : 0;
$message = isset($data['message']) ? trim($data['message']) : '';

if (!$conversation_id || $message === '') {
    http_response_code(400);
    echo json_encode(['error' => t('api.message.err.missing_fields')]);
    exit;
}

$check = $conn->prepare("SELECT user_id, agency_id FROM conversations WHERE id = ? AND (user_id = ? OR agency_id = ?)");
$check->bind_param("iii", $conversation_id, $user_id, $user_id);
$check->execute();
$convRow = $check->get_result()->fetch_assoc();
$check->close();

if (!$convRow) {
    http_response_code(403);
    echo json_encode(['error' => t('api.err.forbidden')]);
    exit;
}

$other_id = ((int) $convRow['user_id'] === $user_id) ? (int) $convRow['agency_id'] : (int) $convRow['user_id'];

$stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, message) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $conversation_id, $user_id, $message);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => t('api.message.err.send_failed')]);
    exit;
}

$message_id = $stmt->insert_id;
$stmt->close();

$update = $conn->prepare("UPDATE conversations SET last_message = ?, last_message_time = NOW(), updated_at = NOW() WHERE id = ?");
$update->bind_param("si", $message, $conversation_id);
$update->execute();
$update->close();

$sender = $conn->prepare("SELECT firstname, lastname FROM users WHERE id = ?");
$sender->bind_param("i", $user_id);
$sender->execute();
$senderRow = $sender->get_result()->fetch_assoc();
$sender->close();
$senderName = trim(($senderRow['firstname'] ?? '') . ' ' . ($senderRow['lastname'] ?? ''));
$preview = substr($message, 0, 80);

$notif = $conn->prepare("INSERT INTO notifications (user_id, type, title, message) VALUES (?, 'message', ?, ?)");
$title = t('api.notif.new_message');
$notifBody = ($senderName !== '' ? $senderName . ': ' : '') . $preview;
$notif->bind_param("iss", $other_id, $title, $notifBody);
$notif->execute();
$notif->close();

$row = $conn->prepare("SELECT id, message, created_at FROM messages WHERE id = ?");
$row->bind_param("i", $message_id);
$row->execute();
$sent = $row->get_result()->fetch_assoc();
$row->close();

echo json_encode([
    'id' => (int) $sent['id'],
    'text' => $sent['message'],
    'created_at' => $sent['created_at']
]);
$conn->close();
