<?php
// api/chat/send-message.php
// Sends a message inside an existing conversation.
// Body: { conversation_id: int, message: string }

// FIX: Enable MySQLi exceptions so try-catch actually catches DB errors.
// Without this, $conn->prepare() returns false on error instead of throwing,
// causing a fatal "Call to a member function bind_param() on bool" crash
// that corrupts the JSON response.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit(); }

require_once '../../db/connect.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}
$sender_id = (int)$_SESSION['user_id'];

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->conversation_id) || !isset($data->message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields: conversation_id and message']);
    exit();
}

$conversation_id = (int)$data->conversation_id;
$message_text    = trim($data->message);

if (empty($message_text)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
    exit();
}

try {
    // Verify the sender belongs to this conversation
    $auth = $conn->prepare("
        SELECT id FROM conversations
        WHERE id = ? AND (user_id = ? OR agency_id = ?)
    ");
    $auth->bind_param("iii", $conversation_id, $sender_id, $sender_id);
    $auth->execute();
    if ($auth->get_result()->num_rows === 0) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access denied to this conversation']);
        exit();
    }

    // Insert the new message
    $insert = $conn->prepare("
        INSERT INTO messages (conversation_id, sender_id, message, is_read, created_at)
        VALUES (?, ?, ?, 0, NOW())
    ");
    $insert->bind_param("iis", $conversation_id, $sender_id, $message_text);
    $insert->execute();
    $message_id = $conn->insert_id;

    // FIX: Removed `updated_at = NOW()` — that column may not exist in the
    // conversations table and was causing prepare() to return false, breaking
    // every send request.  last_message + last_message_time is sufficient.
    $update = $conn->prepare("
        UPDATE conversations
        SET last_message = ?, last_message_time = NOW()
        WHERE id = ?
    ");
    $update->bind_param("si", $message_text, $conversation_id);
    $update->execute();

    // Fetch sender info for the response payload
    $senderQ = $conn->prepare("SELECT firstname, lastname, profile_image FROM users WHERE id = ?");
    $senderQ->bind_param("i", $sender_id);
    $senderQ->execute();
    $sender = $senderQ->get_result()->fetch_assoc();

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully',
        'data'    => [
            'id'               => $message_id,
            'conversation_id'  => $conversation_id,
            'sender_id'        => $sender_id,
            'sender_firstname' => $sender['firstname'] ?? '',
            'sender_lastname'  => $sender['lastname']  ?? '',
            'sender_avatar'    => $sender['profile_image'] ?? null,
            'message'          => $message_text,
            'is_read'          => 0,
            'created_at'       => date('Y-m-d H:i:s')
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send message: ' . $e->getMessage()]);
}
?>