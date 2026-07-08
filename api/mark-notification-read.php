<?php
session_start();
header('Content-Type: application/json');
require "../db/connect.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? (int) $data['id'] : 0;

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->close();
} else {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

echo json_encode(['success' => true]);
$conn->close();
