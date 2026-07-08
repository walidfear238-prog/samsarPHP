<?php
require __DIR__ . '/_bootstrap.php';


$pairsStmt = $conn->prepare("
    SELECT
        CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END AS other_id,
        property_id,
        MAX(created_at) AS last_time
    FROM messages
    WHERE sender_id = ? OR receiver_id = ?
    GROUP BY other_id, property_id
    ORDER BY last_time DESC
");
$pairsStmt->bind_param("iii", $CHAT_USER_ID, $CHAT_USER_ID, $CHAT_USER_ID);
$pairsStmt->execute();
$pairs = $pairsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$pairsStmt->close();

$conversations = [];

foreach ($pairs as $pair) {
    $otherId    = (int) $pair['other_id'];
    $propertyId = $pair['property_id'] !== null ? (int) $pair['property_id'] : null;

    // Last message in this thread
    $lm = $conn->prepare("
        SELECT sender_id, message, created_at
        FROM messages
        WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
          AND property_id <=> ?
        ORDER BY created_at DESC, id DESC
        LIMIT 1
    ");
    $lm->bind_param("iiiii", $CHAT_USER_ID, $otherId, $otherId, $CHAT_USER_ID, $propertyId);
    $lm->execute();
    $lastMsg = $lm->get_result()->fetch_assoc();
    $lm->close();

    // Unread count (messages the other person sent to me, still unread)
    $uc = $conn->prepare("
        SELECT COUNT(*) AS n FROM messages
        WHERE sender_id = ? AND receiver_id = ? AND property_id <=> ? AND is_read = 0
    ");
    $uc->bind_param("iii", $otherId, $CHAT_USER_ID, $propertyId);
    $uc->execute();
    $unread = (int) ($uc->get_result()->fetch_assoc()['n'] ?? 0);
    $uc->close();

    // Other user's info
    $u = $conn->prepare("SELECT firstname, lastname, profile_image, role FROM users WHERE id = ?");
    $u->bind_param("i", $otherId);
    $u->execute();
    $user = $u->get_result()->fetch_assoc();
    $u->close();

    if (!$user) continue; // other user was deleted — skip this thread

    // Property title, if this thread is tied to a listing
    $propertyTitle = null;
    if ($propertyId) {
        $p = $conn->prepare("SELECT title FROM properties WHERE id = ?");
        $p->bind_param("i", $propertyId);
        $p->execute();
        $propRow = $p->get_result()->fetch_assoc();
        $p->close();
        $propertyTitle = $propRow['title'] ?? null;
    }

    $conversations[] = [
        'conversation_id'   => $otherId . '_' . ($propertyId ?: 0),
        'property_id'       => $propertyId,
        'property_title'    => $propertyTitle,
        'last_message'      => $lastMsg['message'] ?? null,
        'last_message_time' => $lastMsg['created_at'] ?? null,
        'last_sender_id'    => isset($lastMsg['sender_id']) ? (int) $lastMsg['sender_id'] : null,
        'other_user_id'     => $otherId,
        'other_firstname'   => $user['firstname'],
        'other_lastname'    => $user['lastname'],
        'other_avatar'      => $user['profile_image'],
        'other_role'        => $user['role'],
        'unread_count'      => $unread,
    ];
}

json_out(true, $conversations);