<?php    
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require "../db/connect.php";

if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($property_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid property ID']);
    exit;
}

$stmt = $conn->prepare("
    SELECT 
        p.id,
        p.title,
        p.property_type,
        p.price,
        p.description,
        p.status,
        p.city,
        p.district,
        p.bedrooms,
        p.bathrooms,
        p.area,
        p.created_at,
        p.updated_at,
        u.id as user_id,
        u.firstname,
        u.lastname,
        u.email as agent_email,
        u.phone as agent_phone,
        u.profile_image as agent_avatar,
        u.agencyName,
        (SELECT JSON_ARRAYAGG(pi.image_path) 
         FROM property_images pi 
         WHERE pi.property_id = p.id) as images,
        (SELECT pi.image_path 
         FROM property_images pi 
         WHERE pi.property_id = p.id 
         ORDER BY pi.is_primary DESC, pi.id ASC 
         LIMIT 1) as main_image
    FROM properties p
    LEFT JOIN users u ON p.user_id = u.id
    WHERE p.id = ? 
    AND p.status IN ('available', 'rented', 'sold', 'pending', 'draft')
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare query: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $property_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Property not found']);
    exit;
}

$property = $result->fetch_assoc();

// Parse images
if ($property['images']) {
    $property['images'] = json_decode($property['images'], true);
    $property['images'] = array_filter($property['images']);
} else {
    $property['images'] = [];
}

// Ensure main_image exists
if (empty($property['main_image']) && !empty($property['images'])) {
    $property['main_image'] = $property['images'][0];
}

// Build agent name
$property['agentName'] = trim(($property['firstname'] ?? '') . ' ' . ($property['lastname'] ?? ''));
if (empty($property['agentName']) && !empty($property['agencyName'])) {
    $property['agentName'] = $property['agencyName'];
}
if (empty($property['agentName'])) {
    $property['agentName'] = 'Agency';
}

$stmt->close();
$conn->close();

echo json_encode($property);
?>