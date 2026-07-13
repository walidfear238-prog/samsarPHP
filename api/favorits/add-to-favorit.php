<?php
session_start();
header('Content-Type: application/json');
require '../../db/connect.php';
require_once __DIR__ . '/../../php/lang.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => t('api.err.user_not_logged_in')
    ]);
    exit;
}

// Check if property_id is set
if (!isset($_POST['property_id'])) {
    echo json_encode([
        "success" => false,
        "message" => t('api.property.err.id_required')
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$property_id = (int)$_POST['property_id'];

// Check if already favorited to avoid duplicates
$check_stmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND property_id = ?");
$check_stmt->bind_param('ii', $user_id, $property_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    // Already favorited 
    echo json_encode([
        "success" => false,
        "message" => t('api.favorites.already')
    ]);
    $check_stmt->close();
    exit;
}
$check_stmt->close();

// Add to favorites
$stmt = $conn->prepare("INSERT INTO favorites (user_id, property_id) VALUES (?, ?)");
$stmt->bind_param('ii', $user_id, $property_id);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => t('api.favorites.add_success')
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => t('api.err.database') . ": " . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>