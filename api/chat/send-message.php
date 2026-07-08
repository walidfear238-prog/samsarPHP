<?php
require __DIR__ . '/_bootstrap.php';


$raw = file_get_contents("php://input");
$data = json_decode($raw);

if ($data === null && trim($raw) !== '') {
    json_out(false, 'Malformed JSON body received: ' . substr($raw, 0, 200), 400);
}

$message_text = isset($data->message) ? trim((string) $data->message) : '';
if ($message_text === '') {
    json_out(false, 'Message cannot be empty', 400);
}

$other_user_id = null;
$property_id = null;

if (!empty($data->conversation_id)) {

    $parts = explode('_', (string) $data->conversation_id, 2);
    $other_user_id = isset($parts[0]) ? (int) $parts[0] : 0;
    $property_id = (isset($parts[1]) && (int) $parts[1] > 0) ? (int) $parts[1] : null;

    if (!$other_user_id) {
        json_out(false, 'Invalid conversation_id', 400);
    }

} elseif (!empty($data->property_id)) {

    $property_id = (int) $data->property_id;

    $pstmt = $conn->prepare("SELECT user_id FROM properties WHERE id = ?");
    $pstmt->bind_param("i", $property_id);
    $pstmt->execute();
    $prop = $pstmt->get_result()->fetch_assoc();
    $pstmt->close();

    if (!$prop) {
        json_out(false, 'Property not found', 404);
    }
    $other_user_id = (int) $prop['user_id'];

} elseif (!empty($data->receiver_id)) {
    
    $other_user_id = (int) $data->receiver_id;

} else {
    json_out(false, 'Missing conversation_id, property_id, or receiver_id', 400);
}

if ($other_user_id === $CHAT_USER_ID) {
    json_out(false, 'You cannot message yourself', 400);
}


$check = $conn->prepare("SELECT id FROM users WHERE id = ?");
$check->bind_param("i", $other_user_id);
$check->execute();
if ($check->get_result()->num_rows === 0) {
    json_out(false, 'Recipient not found', 404);
}
$check->close();


$insert = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, property_id, message, is_read) VALUES (?, ?, ?, ?, 0)");
$insert->bind_param("iiis", $CHAT_USER_ID, $other_user_id, $property_id, $message_text);
$insert->execute();
$message_id = $insert->insert_id;
$insert->close();


$preview = substr($message_text, 0, 255);
$notif_title = 'New message';
$notif = $conn->prepare("INSERT INTO notifications (user_id, type, title, message, is_read) VALUES (?, 'message', ?, ?, 0)");
$notif->bind_param("iss", $other_user_id, $notif_title, $preview);
$notif->execute();
$notif->close();


$get = $conn->prepare("SELECT id, sender_id, receiver_id, property_id, message, is_read, created_at FROM messages WHERE id = ?");
$get->bind_param("i", $message_id);
$get->execute();
$row = $get->get_result()->fetch_assoc();
$get->close();

$thread_token = $other_user_id . '_' . ((int) ($row['property_id'] ?? 0));

json_out(true, [
    'id' => (int) $row['id'],
    'conversation_id' => $thread_token,
    'sender_id' => (int) $row['sender_id'],
    'receiver_id' => (int) $row['receiver_id'],
    'property_id' => $row['property_id'] !== null ? (int) $row['property_id'] : null,
    'message' => $row['message'],
    'is_read' => (bool) $row['is_read'],
    'created_at' => $row['created_at'],
], 201);