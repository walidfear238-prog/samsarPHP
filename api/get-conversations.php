<?php
session_start();
header('Content-Type: application/json');
require "../db/connect.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT
        c.id,
        c.property_id,
        c.last_message,
        c.last_message_time,
        IF(c.user_id = ?, c.agency_id, c.user_id) AS other_user_id,
        u.firstname, u.lastname, u.agencyName, u.profile_image, u.role,
        p.title AS property_title,
        (SELECT COUNT(*) FROM messages m WHERE m.conversation_id = c.id AND m.sender_id != ? AND m.is_read = 0) AS unread_count
    FROM conversations c
    JOIN users u ON u.id = IF(c.user_id = ?, c.agency_id, c.user_id)
    LEFT JOIN properties p ON p.id = c.property_id
    WHERE c.user_id = ? OR c.agency_id = ?
    ORDER BY c.last_message_time IS NULL, c.last_message_time DESC, c.updated_at DESC
");
$stmt->bind_param("iiiii", $user_id, $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$conversations = [];
while ($row = $result->fetch_assoc()) {
    $name = ($row['role'] === 'agency' && $row['agencyName']) ? $row['agencyName'] : trim($row['firstname'] . ' ' . $row['lastname']);
    $conversations[] = [
        'id' => (int) $row['id'],
        'property_id' => $row['property_id'] !== null ? (int) $row['property_id'] : null,
        'property_title' => $row['property_title'],
        'other_user_id' => (int) $row['other_user_id'],
        'name' => $name,
        'avatar' => $row['profile_image'] ? $row['profile_image'] : 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80',
        'last_message' => $row['last_message'],
        'last_message_time' => $row['last_message_time'],
        'unread_count' => (int) $row['unread_count']
    ];
}

$stmt->close();
echo json_encode($conversations);
$conn->close();
