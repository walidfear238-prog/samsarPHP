<?php
session_start();
header('Content-Type: application/json');
require "../db/connect.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'You must be logged in to send a message']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

$to_user_id = isset($data['to_user_id']) ? (int) $data['to_user_id'] : 0;
$property_id = isset($data['property_id']) ? (int) $data['property_id'] : 0;
$message = isset($data['message']) ? trim($data['message']) : '';

if (!$to_user_id || !$property_id || $message === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

if ($to_user_id === $user_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'You cannot message yourself']);
    exit;
}

$find = $conn->prepare("
    SELECT id FROM conversations
    WHERE property_id = ?
      AND ((user_id = ? AND agency_id = ?) OR (user_id = ? AND agency_id = ?))
");
$find->bind_param("iiiii", $property_id, $user_id, $to_user_id, $to_user_id, $user_id);
$find->execute();
$existing = $find->get_result()->fetch_assoc();
$find->close();

if ($existing) {
    $conversation_id = (int) $existing['id'];
    $update = $conn->prepare("UPDATE conversations SET last_message = ?, last_message_time = NOW(), updated_at = NOW() WHERE id = ?");
    $update->bind_param("si", $message, $conversation_id);
    $update->execute();
    $update->close();
} else {
    $create = $conn->prepare("
        INSERT INTO conversations (property_id, user_id, agency_id, last_message, last_message_time)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $create->bind_param("iiis", $property_id, $user_id, $to_user_id, $message);
    $create->execute();
    $conversation_id = $create->insert_id;
    $create->close();
}

$msg = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, message) VALUES (?, ?, ?)");
$msg->bind_param("iis", $conversation_id, $user_id, $message);
$msg->execute();
$msg->close();

$sender = $conn->prepare("SELECT firstname, lastname FROM users WHERE id = ?");
$sender->bind_param("i", $user_id);
$sender->execute();
$senderRow = $sender->get_result()->fetch_assoc();
$sender->close();
$senderName = trim(($senderRow['firstname'] ?? '') . ' ' . ($senderRow['lastname'] ?? ''));

$prop = $conn->prepare("SELECT title FROM properties WHERE id = ?");
$prop->bind_param("i", $property_id);
$prop->execute();
$propRow = $prop->get_result()->fetch_assoc();
$prop->close();
$propTitle = $propRow['title'] ?? 'your property';

$notif = $conn->prepare("INSERT INTO notifications (user_id, type, title, message) VALUES (?, 'message', ?, ?)");
$title = 'New inquiry';
$body = ($senderName !== '' ? $senderName : 'Someone') . ' sent you a message about ' . $propTitle;
$notif->bind_param("iss", $to_user_id, $title, $body);
$notif->execute();
$notif->close();

echo json_encode(['success' => true, 'conversation_id' => (int) $conversation_id]);
$conn->close();
