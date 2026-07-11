<?php
require __DIR__ . '/_bootstrap.php';
// Thread token "{other_user_id}_{property_id}" — property_id 0 means NULL/no listing.

$token = isset($_GET['conversation_id']) ? (string) $_GET['conversation_id'] : '';
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;
if ($limit < 1) $limit = 50;
if ($limit > 200) $limit = 200;

if ($token === '') {
    json_out(false, 'Missing conversation_id', 400);
}

$parts = explode('_', $token, 2);
$other_user_id = isset($parts[0]) ? (int) $parts[0] : 0;
$property_id   = (isset($parts[1]) && (int) $parts[1] > 0) ? (int) $parts[1] : null;

if (!$other_user_id) {
    json_out(false, 'Invalid conversation_id', 400);
}

// No separate "ownership" check needed: the WHERE clause only ever
// matches rows where the logged-in user is the sender or the receiver,
// so a user can never see anyone else's messages by guessing a token.
$stmt = $conn->prepare("
    SELECT id, sender_id, receiver_id, property_id, message, is_read, created_at
    FROM messages
    WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
      AND property_id <=> ?
    ORDER BY created_at DESC, id DESC
    LIMIT ?
");
$stmt->bind_param(
    "iiiiii",
    $CHAT_USER_ID, $other_user_id,
    $other_user_id, $CHAT_USER_ID,
    $property_id,
    $limit
);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$rows = array_reverse($rows);

$messages = array_map(function ($row) {
    return [
        'id'          => (int) $row['id'],
        'sender_id'   => (int) $row['sender_id'],
        'receiver_id' => (int) $row['receiver_id'],
        'property_id' => $row['property_id'] !== null ? (int) $row['property_id'] : null,
        'message'     => $row['message'],
        'is_read'     => (bool) $row['is_read'],
        'created_at'  => $row['created_at'],
    ];
}, $rows);

json_out(true, $messages);
