<?php
require __DIR__ . "/../../db/connect.php";

header('Content-Type: application/json');


$user_id = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;

if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM following WHERE follower_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$count = (int) $stmt->get_result()->fetch_assoc()['count'];

echo json_encode([
    'success' => true,
    'user_id' => $user_id,
    'count'   => $count
]);