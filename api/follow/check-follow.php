<?php
session_start();
require __DIR__ . "/../../db/connect.php";
require_once __DIR__ . "/../../php/lang.php";

header('Content-Type: application/json');


if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => t('api.err.unauthorized')]);
    exit;
}


$follower_id  = (int) $_SESSION['user_id'];
$following_id = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;

if ($following_id <= 0) {
    echo json_encode(['success' => false, 'message' => t('api.err.invalid_user_id')]);
    exit;
}


$stmt = $conn->prepare("SELECT id FROM following WHERE follower_id = ? AND following_id = ?");
$stmt->bind_param("ii", $follower_id, $following_id);
$stmt->execute();

echo json_encode([
    'success'      => true,
    'is_following' => $stmt->get_result()->num_rows > 0
]);