<?php
session_start();
header('Content-Type: application/json');

require '../../db/connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "User not logged in"
    ]);
    exit;
}

if (!isset($_POST['property_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Property ID is required"
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$property_id = (int)$_POST['property_id'];

$stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND property_id = ?");
$stmt->bind_param("ii", $user_id, $property_id);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Removed from favorites successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>