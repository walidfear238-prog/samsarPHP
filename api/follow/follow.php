<?php
session_start();
header('Content-Type: application/json');
require '../../db/connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "User not logged in"
    ]);
    exit;
}

$me = $_SESSION['user_id'];
$profile = $_GET['id'];

$stmt = $conn->prepare("
INSERT INTO follows
(follower_id, following_id)
VALUES (?, ?)
");

$stmt->bind_param("ii", $me, $profile);
json_encode ($stmt->execute);

?>