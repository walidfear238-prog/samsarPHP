<?php
// api/chat/get_conversation.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

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

// Get the other user ID from query parameter
if (!isset($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing user_id parameter'
    ]);
    exit();
}
$other_user_id = $_GET['user_id'];

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

try {
    // Get messages between the two users
    $query = "SELECT m.*, 
                     u1.firstname as sender_firstname, 
                     u1.lastname as sender_lastname,
                     u2.firstname as receiver_firstname, 
                     u2.lastname as receiver_lastname
              FROM messages m
              LEFT JOIN users u1 ON m.sender_id = u1.id
              LEFT JOIN users u2 ON m.receiver_id = u2.id
              WHERE (m.sender_id = ? AND m.receiver_id = ?)
                 OR (m.sender_id = ? AND m.receiver_id = ?)
              ORDER BY m.created_at ASC
              LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiiiii", $user_id, $other_user_id, $other_user_id, $user_id, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = $result->fetch_all(MYSQLI_ASSOC);

    // Mark messages as read
    $update = "UPDATE messages 
               SET is_read = 1, read_at = NOW() 
               WHERE sender_id = ? AND receiver_id = ? AND is_read = 0";
    $update_stmt = $conn->prepare($update);
    $update_stmt->bind_param("ii", $other_user_id, $user_id);
    $update_stmt->execute();

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $messages
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to get messages: ' . $e->getMessage()
    ]);
}
?>