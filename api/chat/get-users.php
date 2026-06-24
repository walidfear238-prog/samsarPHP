<?php
// api/chat/get_users.php
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

try {
    // Get all users except the current user
    $query = "SELECT id, firstname, lastname, email, profile_image, role, agencyName 
              FROM users 
              WHERE id != ? 
              ORDER BY firstname ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);

    // For each user, check if there's an existing conversation
    foreach ($users as &$user) {
        // Get last message between current user and this user
        $last_msg_query = "SELECT message, sender_id, created_at 
                          FROM messages 
                          WHERE (sender_id = ? AND receiver_id = ?) 
                             OR (sender_id = ? AND receiver_id = ?) 
                          ORDER BY created_at DESC 
                          LIMIT 1";
        $last_stmt = $conn->prepare($last_msg_query);
        $last_stmt->bind_param("iiii", $user_id, $user['id'], $user['id'], $user_id);
        $last_stmt->execute();
        $last_result = $last_stmt->get_result();
        $last_msg = $last_result->fetch_assoc();
        
        $user['last_message'] = $last_msg ? $last_msg['message'] : null;
        $user['last_message_time'] = $last_msg ? $last_msg['created_at'] : null;
        $user['last_sender_id'] = $last_msg ? $last_msg['sender_id'] : null;
        
        // Get unread count
        $unread_query = "SELECT COUNT(*) as unread 
                        FROM messages 
                        WHERE sender_id = ? AND receiver_id = ? AND is_read = 0";
        $unread_stmt = $conn->prepare($unread_query);
        $unread_stmt->bind_param("ii", $user['id'], $user_id);
        $unread_stmt->execute();
        $unread_result = $unread_stmt->get_result();
        $unread = $unread_result->fetch_assoc();
        $user['unread_count'] = $unread ? $unread['unread'] : 0;
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $users
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to get users: ' . $e->getMessage()
    ]);
}
?>