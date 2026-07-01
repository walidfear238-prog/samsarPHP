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

try {
    $query = "
        SELECT
            u.id,
            u.firstname,
            u.lastname,
            u.email,
            u.profile_image,
            u.role,
            u.agencyName,
            (SELECT c.id
             FROM conversations c
             WHERE (c.user_id = ? AND c.agency_id = u.id)
                OR (c.agency_id = ? AND c.user_id = u.id)
             LIMIT 1) AS existing_conversation_id
        FROM users u
        WHERE u.id != ?
        ORDER BY u.firstname ASC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $user_id, $user_id, $user_id);
    $stmt->execute();
    $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    http_response_code(200);
    echo json_encode(['success' => true, 'data' => $users]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to get users: ' . $e->getMessage()]);
}
?>