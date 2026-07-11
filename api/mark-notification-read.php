<?php
/**
 * FIXED VERSION — api/mark-notification-read.php
 *
 * CHANGE vs original:
 *   When a `message`-type notification is marked as read AND it carries a
 *   `link` value (e.g. "25_41" = otherUserId_propertyId), the underlying
 *   messages in that conversation are also marked as read. This keeps the
 *   sidebar badge in sync with reality: clicking a "New message"
 *   notification no longer leaves the actual message showing as unread.
 */
session_start();
header('Content-Type: application/json');
require "../db/connect.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? (int) $data['id'] : 0;

if ($id > 0) {
    // Single-notification mark-as-read. Also fetch type+link so we can
    // cascade into the messages table for message-type notifications.
    $fetch = $conn->prepare("SELECT type, link FROM notifications WHERE id = ? AND user_id = ?");
    $fetch->bind_param("ii", $id, $user_id);
    $fetch->execute();
    $row = $fetch->get_result()->fetch_assoc();
    $fetch->close();

    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->close();

    // Cascade: if this was a message notification with a link, mark the
    // corresponding messages as read too.
    if ($row && $row['type'] === 'message' && !empty($row['link'])) {
        samsar_mark_conversation_read($conn, $user_id, $row['link']);
    }
} else {
    // "Mark all as read" — mark every notification, then cascade for
    // every distinct message-type link the user has.
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    $links = $conn->prepare("SELECT DISTINCT link FROM notifications WHERE user_id = ? AND type = 'message' AND link IS NOT NULL");
    $links->bind_param("i", $user_id);
    $links->execute();
    $res = $links->get_result();
    while ($r = $res->fetch_assoc()) {
        samsar_mark_conversation_read($conn, $user_id, $r['link']);
    }
    $links->close();
}

echo json_encode(['success' => true]);
$conn->close();


/**
 * Helper — mark all unread messages in a conversation as read.
 * $link is the "otherUserId_propertyId" token used by api/chat/*.
 */
function samsar_mark_conversation_read($conn, $me, $link)
{
    $parts = explode('_', (string) $link, 2);
    $other_user_id = isset($parts[0]) ? (int) $parts[0] : 0;
    $property_id = (isset($parts[1]) && (int) $parts[1] > 0) ? (int) $parts[1] : null;
    if (!$other_user_id)
        return;

    $stmt = $conn->prepare("
        UPDATE messages
        SET is_read = 1, read_at = NOW()
        WHERE sender_id = ? AND receiver_id = ? AND property_id <=> ? AND is_read = 0
    ");
    $stmt->bind_param("iii", $other_user_id, $me, $property_id);
    $stmt->execute();
    $stmt->close();
}