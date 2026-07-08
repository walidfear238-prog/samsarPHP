<?php
require __DIR__ . '/_bootstrap.php';

$raw  = file_get_contents("php://input");
$data = json_decode($raw);

if (empty($data->conversation_id)) {
    json_out(false, 'Missing conversation_id', 400);
}

$parts = explode('_', (string) $data->conversation_id, 2);
$other_user_id = isset($parts[0]) ? (int) $parts[0] : 0;
$property_id   = (isset($parts[1]) && (int) $parts[1] > 0) ? (int) $parts[1] : null;

if (!$other_user_id) {
    json_out(false, 'Invalid conversation_id', 400);
}

$stmt = $conn->prepare("
    UPDATE messages
    SET is_read = 1, read_at = NOW()
    WHERE sender_id = ? AND receiver_id = ? AND property_id <=> ? AND is_read = 0
");
$stmt->bind_param("iii", $other_user_id, $CHAT_USER_ID, $property_id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

json_out(true, ['marked_count' => $affected], 200, [
    'message' => "{$affected} messages marked as read",
]);
