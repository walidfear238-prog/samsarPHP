<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit(); }

require_once '../../db/connect.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}
$user_id = (int)$_SESSION['user_id'];

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->conversation_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing conversation_id']);
    exit();
}
$conversation_id = (int)$data->conversation_id;

try {
    // Verify the user belongs to this conversation
    $auth = $conn->prepare("
        SELECT id FROM conversations
        WHERE id = ? AND (user_id = ? OR agency_id = ?)
    ");
    $auth->bind_param("iii", $conversation_id, $user_id, $user_id);
    $auth->execute();
    if ($auth->get_result()->num_rows === 0) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit();
    }

    // Mark unread messages FROM the other person as read
    $stmt = $conn->prepare("
        UPDATE messages
        SET is_read = 1
        WHERE conversation_id = ? AND sender_id != ? AND is_read = 0
    ");
    $stmt->bind_param("ii", $conversation_id, $user_id);
    $stmt->execute();
    $affected = $stmt->affected_rows;

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => "{$affected} messages marked as read",
        'data'    => ['marked_count' => $affected]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to mark messages: ' . $e->getMessage()]);
}
?>