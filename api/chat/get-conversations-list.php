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
            c.id                  AS conversation_id,
            c.property_id,
            p.title               AS property_title,
            c.last_message,
            c.last_message_time,

            -- Determine the other party
            CASE WHEN c.user_id = ? THEN c.agency_id ELSE c.user_id END AS other_user_id,

            u.firstname           AS other_firstname,
            u.lastname            AS other_lastname,
            u.profile_image       AS other_avatar,
            u.role                AS other_role,

            -- Who sent the last message? (for 'You: …' prefix)
            (SELECT sender_id
             FROM messages m2
             WHERE m2.conversation_id = c.id
             ORDER BY m2.created_at DESC, m2.id DESC
             LIMIT 1) AS last_sender_id,

            -- Unread count: messages the other person sent that I haven't read
            (SELECT COUNT(*)
             FROM messages m3
             WHERE m3.conversation_id = c.id
               AND m3.sender_id != ?
               AND m3.is_read = 0) AS unread_count

        FROM conversations c
        LEFT JOIN properties p ON p.id = c.property_id
        LEFT JOIN users u
            ON u.id = CASE WHEN c.user_id = ? THEN c.agency_id ELSE c.user_id END
        WHERE c.user_id = ? OR c.agency_id = ?
        ORDER BY c.last_message_time DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiiii", $user_id, $user_id, $user_id, $user_id, $user_id);
    $stmt->execute();
    $conversations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    http_response_code(200);
    echo json_encode(['success' => true, 'data' => $conversations]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to get conversations: ' . $e->getMessage()]);
}
?>