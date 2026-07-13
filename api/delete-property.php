<?php
session_start();
header('Content-Type: application/json');

require "../db/connect.php";
require_once __DIR__ . "/../php/lang.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => t('api.err.unauthorized')]);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['property_id'])) {
    echo json_encode(['success' => false, 'message' => t('api.property.err.id_required')]);
    exit;
}

$property_id = $input['property_id'];
$user_id = $_SESSION['user_id'];

// First verify that this property belongs to the logged-in user
$check_stmt = $conn->prepare("SELECT id FROM properties WHERE id = ? AND user_id = ?");
$check_stmt->bind_param("ii", $property_id, $user_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => t('api.property.err.delete_permission')]);
    exit;
}

// Delete property (images will be deleted automatically if you have FOREIGN KEY with CASCADE)
$delete_stmt = $conn->prepare("DELETE FROM properties WHERE id = ? AND user_id = ?");
$delete_stmt->bind_param("ii", $property_id, $user_id);

if ($delete_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => t('api.property.delete_success')]);
} else {
    echo json_encode(['success' => false, 'message' => t('api.err.database') . ': ' . $conn->error]);
}

$check_stmt->close();
$delete_stmt->close();
$conn->close();
?>