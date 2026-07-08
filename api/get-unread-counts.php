<?php
session_start();
header('Content-Type: application/json');
require "../db/connect.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

$msgStmt = $conn->prepare("
    SELECT COUNT(*) AS count
    FROM messages m
    JOIN conversations c ON c.id = m.conversation_id
    WHERE (c.user_id = ? OR c.agency_id = ?) AND m.sender_id != ? AND m.is_read = 0
");
$msgStmt->bind_param("iii", $user_id, $user_id, $user_id);
$msgStmt->execute();
$unread_messages = (int) $msgStmt->get_result()->fetch_assoc()['count'];
$msgStmt->close();

$notifStmt = $conn->prepare("SELECT COUNT(*) AS count FROM notifications WHERE user_id = ? AND is_read = 0");
$notifStmt->bind_param("i", $user_id);
$notifStmt->execute();
$unread_notifications = (int) $notifStmt->get_result()->fetch_assoc()['count'];
$notifStmt->close();

echo json_encode([
    'unread_messages' => $unread_messages,
    'unread_notifications' => $unread_notifications
]);
$conn->close();
