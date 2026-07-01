<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
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

if (!isset($_GET['conversation_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing conversation_id parameter']);
    exit();
}
$conversation_id = (int)$_GET['conversation_id'];

$limit  = isset($_GET['limit'])  ? max(1, (int)$_GET['limit'])  : 50;
$offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;

try {
    // Verify the current user belongs to this conversation
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

    // Fetch messages with sender info, ordered oldest-first
    $stmt = $conn->prepare("
        SELECT
            m.id,
            m.conversation_id,
            m.sender_id,
            m.message,
            m.is_read,
            m.created_at,
            u.firstname       AS sender_firstname,
            u.lastname        AS sender_lastname,
            u.profile_image   AS sender_avatar
        FROM messages m
        LEFT JOIN users u ON u.id = m.sender_id
        WHERE m.conversation_id = ?
        ORDER BY m.created_at ASC, m.id ASC
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("iii", $conversation_id, $limit, $offset);
    $stmt->execute();
    $messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Mark messages sent by the OTHER party as read
    $mark = $conn->prepare("
        UPDATE messages
        SET is_read = 1
        WHERE conversation_id = ? AND sender_id != ? AND is_read = 0
    ");
    $mark->bind_param("ii", $conversation_id, $user_id);
    $mark->execute();

    http_response_code(200);
    echo json_encode(['success' => true, 'data' => $messages]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to get messages: ' . $e->getMessage()]);
}
?>