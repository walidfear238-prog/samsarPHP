<?php
require_once __DIR__ . '/../php/lang.php';
/**
 * FIXED VERSION — api/get-unread-counts.php
 *
 * ROOT CAUSE OF THE BUG:
 *   The original query joined `messages m JOIN conversations c ON c.id = m.conversation_id`,
 *   but the samsar database has NO `conversations` table and the `messages` table has NO
 *   `conversation_id` column. Every call to this endpoint threw a fatal SQL error, so the
 *   sidebar badges (bdg-msg, bdg-notif, bdg-notif-2) on every dashboard page silently
 *   stayed at "0" forever.
 *
 * FIX:
 *   Count unread messages addressed to me directly from the `messages` table (matching the
 *   schema actually shipped in samsar-2.sql). No join, no missing table.
 */
session_start();
header('Content-Type: application/json');
require "../db/connect.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => t('api.err.unauthorized')]);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Unread messages addressed to the current user (matches the real `messages` schema).
$msgStmt = $conn->prepare("
    SELECT COUNT(*) AS count
    FROM messages
    WHERE receiver_id = ? AND is_read = 0
");
$msgStmt->bind_param("i", $user_id);
$msgStmt->execute();
$unread_messages = (int) $msgStmt->get_result()->fetch_assoc()['count'];
$msgStmt->close();

// Unread notifications for the current user.
$notifStmt = $conn->prepare("SELECT COUNT(*) AS count FROM notifications WHERE user_id = ? AND is_read = 0");
$notifStmt->bind_param("i", $user_id);
$notifStmt->execute();
$unread_notifications = (int) $notifStmt->get_result()->fetch_assoc()['count'];
$notifStmt->close();

echo json_encode([
    'unread_messages'      => $unread_messages,
    'unread_notifications' => $unread_notifications
]);
$conn->close();
?>