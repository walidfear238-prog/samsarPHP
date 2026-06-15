<?php
session_start();
header('Content-Type: application/json');

require '../../db/connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get all favorited properties with their images
$query = "SELECT DISTINCT p.id, p.title, p.property_type, p.price, p.status, p.city, p.bedrooms, p.bathrooms, p.area 
          FROM favorites f
          JOIN properties p ON f.property_id = p.id
          WHERE f.user_id = ?
          ORDER BY f.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$properties = [];
while ($row = $result->fetch_assoc()) {
    // Get the first image (primary or any image) for this property
    $imgStmt = $conn->prepare("SELECT image_path FROM property_images WHERE property_id = ? ORDER BY is_primary DESC, sort_order ASC LIMIT 1");
    $imgStmt->bind_param("i", $row['id']);
    $imgStmt->execute();
    $imgResult = $imgStmt->get_result();
    $imgRow = $imgResult->fetch_assoc();
    
    $row['img'] = $imgRow ? $imgRow['image_path'] : null;
    $properties[] = $row;
    
    $imgStmt->close();
}

echo json_encode($properties);
$stmt->close();
$conn->close();
?>