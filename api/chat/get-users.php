<?php
require __DIR__ . '/_bootstrap.php';
// (Currently unused by the frontend, kept for a future "start new
// conversation" picker.) Returns other users plus whether any message
// history already exists with each — a single "existing_conversation_id"
// doesn't make sense anymore since the same user pair can have several
// separate threads (one per property, plus one general thread).

$query = "
    SELECT
        u.id,
        u.firstname,
        u.lastname,
        u.email,
        u.profile_image,
        u.role,
        u.agencyName,
        EXISTS(
            SELECT 1 FROM messages m
            WHERE (m.sender_id = ? AND m.receiver_id = u.id)
               OR (m.sender_id = u.id AND m.receiver_id = ?)
        ) AS has_conversation
    FROM users u
    WHERE u.id != ?
    ORDER BY u.firstname ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $CHAT_USER_ID, $CHAT_USER_ID, $CHAT_USER_ID);
$stmt->execute();
$users = array_map(function ($row) {
    $row['has_conversation'] = (bool) $row['has_conversation'];
    return $row;
}, $stmt->get_result()->fetch_all(MYSQLI_ASSOC));
$stmt->close();

json_out(true, $users);
