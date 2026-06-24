<?php
// api/chat/get_conversations_list.php
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
    $query = "SELECT 
                CASE 
                    WHEN m.sender_id = ? THEN m.receiver_id 
                    ELSE m.sender_id 
                END AS other_user_id,
                u.firstname,
                u.lastname,
                u.profile_image,
                MAX(m.created_at) AS last_message_time,
                (SELECT message FROM messages m2 
                 WHERE (m2.sender_id = ? AND m2.receiver_id = other_user_id) 
                    OR (m2.sender_id = other_user_id AND m2.receiver_id = ?) 
                 ORDER BY m2.created_at DESC LIMIT 1) AS last_message,
                (SELECT sender_id FROM messages m2 
                 WHERE (m2.sender_id = ? AND m2.receiver_id = other_user_id) 
                    OR (m2.sender_id = other_user_id AND m2.receiver_id = ?) 
                 ORDER BY m2.created_at DESC LIMIT 1) AS last_sender_id,
                (SELECT COUNT(*) FROM messages m3 
                 WHERE m3.sender_id = other_user_id 
                   AND m3.receiver_id = ? 
                   AND m3.is_read = 0) AS unread_count
              FROM messages m
              LEFT JOIN users u ON u.id = CASE 
                    WHEN m.sender_id = ? THEN m.receiver_id 
                    ELSE m.sender_id 
                END
              WHERE m.sender_id = ? OR m.receiver_id = ?
              GROUP BY other_user_id, u.firstname, u.lastname, u.profile_image
              ORDER BY last_message_time DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiiiiiiii", $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $conversations = $result->fetch_all(MYSQLI_ASSOC);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $conversations
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to get conversations: ' . $e->getMessage()
    ]);
}
?>