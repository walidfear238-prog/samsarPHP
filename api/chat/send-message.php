<?php
// api/chat/send_message.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../db/connect.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'User not authenticated'
    ]);
    exit();
}
$sender_id = $_SESSION['user_id'];

// Get the request data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (!isset($data->receiver_id) || !isset($data->message)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields: receiver_id and message'
    ]);
    exit();
}

// Validate empty message
if (empty(trim($data->message))) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Message cannot be empty'
    ]);
    exit();
}

// Prevent sending message to yourself
if ($sender_id == $data->receiver_id) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'You cannot send a message to yourself'
    ]);
    exit();
}

try {
    // Check if receiver exists
    $check_user = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $check_user->bind_param("i", $data->receiver_id);
    $check_user->execute();
    $check_result = $check_user->get_result();
    
    if ($check_result->num_rows == 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Receiver not found'
        ]);
        exit();
    }

    // Insert the message
    $query = "INSERT INTO messages (sender_id, receiver_id, message, parent_message_id, created_at) 
              VALUES (?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($query);
    $parent_message_id = isset($data->parent_message_id) ? $data->parent_message_id : null;
    $message_text = trim($data->message);
    $stmt->bind_param("iisi", $sender_id, $data->receiver_id, $message_text, $parent_message_id);
    $stmt->execute();
    
    $message_id = $conn->insert_id;

    // Get the sender info for response
    $get_sender = $conn->prepare("SELECT firstname, lastname FROM users WHERE id = ?");
    $get_sender->bind_param("i", $sender_id);
    $get_sender->execute();
    $sender_result = $get_sender->get_result();
    $sender = $sender_result->fetch_assoc();

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully',
        'data' => [
            'id' => $message_id,
            'sender_id' => $sender_id,
            'sender_name' => $sender['firstname'] . ' ' . $sender['lastname'],
            'receiver_id' => $data->receiver_id,
            'message' => $message_text,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send message: ' . $e->getMessage()
    ]);
}
?>