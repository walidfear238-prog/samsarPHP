<?php
session_start();
require "../db/connect.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$property_id) {
    echo json_encode(['error' => 'Property ID required']);
    exit;
}

$stmt = $conn->prepare("
    SELECT 
        p.*,
        GROUP_CONCAT(pi.image_path) as images
    FROM properties p
    LEFT JOIN property_images pi ON p.id = pi.property_id
    WHERE p.id = ? AND p.user_id = ?
    GROUP BY p.id
");
$stmt->bind_param("ii", $property_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$property = $result->fetch_assoc();

if (!$property) {
    echo json_encode(['error' => 'Property not found']);
    exit;
}

header('Content-Type: application/json');
echo json_encode($property);
?>