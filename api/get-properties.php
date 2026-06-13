<?php
session_start();
require "../db/connect.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT 
        p.id, 
        p.title, 
        p.property_type, 
        p.price, 
        p.status, 
        p.city, 
        p.bedrooms, 
        p.bathrooms, 
        p.area,
        (SELECT pi.image_path FROM property_images pi WHERE pi.property_id = p.id LIMIT 1) as img
    FROM properties p
    WHERE p.user_id = ?
    ORDER BY p.id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$properties = [];

while ($row = $result->fetch_assoc()) {
    $properties[] = $row;
}

header('Content-Type: application/json');
echo json_encode($properties);
?>