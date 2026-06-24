<?php
// api/chat/mark_messages_read.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT");

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
$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->sender_id)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing sender_id'
    ]);
    exit();
}

try {
    $query = "UPDATE messages 
              SET is_read = 1, read_at = NOW() 
              WHERE sender_id = ? AND receiver_id = ? AND is_read = 0";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $data->sender_id, $user_id);
    $stmt->execute();
    $affected = $stmt->affected_rows;

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => "$affected messages marked as read",
        'data' => [
            'marked_count' => $affected
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to mark messages: ' . $e->getMessage()
    ]);
}
?>