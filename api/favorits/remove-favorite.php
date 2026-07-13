<?php
session_start();
header('Content-Type: application/json');

require '../../db/connect.php';
require_once __DIR__ . '/../../php/lang.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => t('api.err.user_not_logged_in')
    ]);
    exit;
}

if (!isset($_POST['property_id'])) {
    echo json_encode([
        "success" => false,
        "message" => t('api.property.err.id_required')
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
        "message" => t('api.favorites.remove_success')
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