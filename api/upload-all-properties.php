<?php    
require_once __DIR__ . '/../php/lang.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require "../db/connect.php";

// No authentication check - this is for public viewing

if (!$conn) {
http_response_code(500);
echo json_encode(['error' => t('api.err.db_connection_failed')]);
exit;
}

// Get ALL properties not filtered by user_id
$stmt = $conn->prepare("
SELECT
p.id,
p.title,
p.property_type,
p.price,
p.description,
p.status,
p.city,
p.bedrooms,
p.bathrooms,
p.area,
(SELECT pi.image_path FROM property_images pi WHERE pi.property_id = p.id LIMIT 1) as img
FROM properties p
WHERE p.status IN ('available', 'rented', 'sold', 'pending', 'draft')
ORDER BY p.id DESC
");

if (!$stmt) {
http_response_code(500);
echo json_encode(['error' => t('api.err.query_prepare_failed')]);
exit;
}

$stmt->execute();
$result = $stmt->get_result();

$properties = [];
while ($row = $result->fetch_assoc()) {
$properties[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($properties);
?>