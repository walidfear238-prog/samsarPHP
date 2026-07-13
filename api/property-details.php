<?php
require_once __DIR__ . '/../php/lang.php';
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');


define('PROPERTY_IMG_DIR', 'uploads/property_images/');


function img_url(string $path): string
{
    if ($path === '')
        return '';
    return (strpos($path, '/') === false) ? PROPERTY_IMG_DIR . $path : $path;
}

require "../db/connect.php";

//   Connection check 
if (!isset($conn) || !$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed. Check db/connect.php and make sure it creates $conn as a mysqli object.']);
    exit;
}

//  Property ID 
$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($property_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => t('api.err.invalid_property_id')]);
    exit;
}

//  Main property query 
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
        u.id            AS user_id,
        u.firstname,
        u.lastname,
        u.email         AS agent_email,
        u.phone         AS agent_phone,
        u.profile_image AS agent_avatar,
        u.agencyName,
        (
            SELECT GROUP_CONCAT(pi.image_path ORDER BY pi.is_primary DESC, pi.id ASC SEPARATOR '||')
            FROM property_images pi
            WHERE pi.property_id = p.id
        ) AS images_raw,
        (
            SELECT pi.image_path
            FROM property_images pi
            WHERE pi.property_id = p.id
            ORDER BY pi.is_primary DESC, pi.id ASC
            LIMIT 1
        ) AS main_image
    FROM properties p
    LEFT JOIN users u ON p.user_id = u.id
    WHERE p.id = ?
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Query prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $property_id);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Query execute failed: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => "Property #$property_id not found in database"]);
    exit;
}

$property = $result->fetch_assoc();
$stmt->close();

//  Similar properties 
$similar = [];
$sim_stmt = $conn->prepare("
    SELECT
        p.id,
        p.title,
        p.property_type,
        p.price,
        p.city,
        p.bedrooms,
        (
            SELECT pi.image_path
            FROM property_images pi
            WHERE pi.property_id = p.id
            ORDER BY pi.is_primary DESC, pi.id ASC
            LIMIT 1
        ) AS main_image
    FROM properties p
    WHERE p.id != ?
      AND (p.property_type = ? OR p.city = ?)
    ORDER BY p.created_at DESC
    LIMIT 3
");

if ($sim_stmt) {
    $sim_stmt->bind_param("iss", $property_id, $property['property_type'], $property['city']);
    if ($sim_stmt->execute()) {
        $sim_result = $sim_stmt->get_result();
        while ($row = $sim_result->fetch_assoc()) {
            // Fix image path for each similar property
            $row['main_image'] = img_url((string) ($row['main_image'] ?? ''));
            $similar[] = $row;
        }
    }
    $sim_stmt->close();
}

$conn->close();

//  Parse & fix image paths 

if (!empty($property['images_raw'])) {
    $raw = array_values(array_filter(explode('||', $property['images_raw'])));
    $property['images'] = array_map(fn($p) => img_url($p), $raw);
} else {
    $property['images'] = [];
}
unset($property['images_raw']);


$property['main_image'] = img_url((string) ($property['main_image'] ?? ''));


if ($property['main_image'] === '' && !empty($property['images'])) {
    $property['main_image'] = $property['images'][0];
}

$first = trim($property['firstname'] ?? '');
$last = trim($property['lastname'] ?? '');
$property['agentName'] = trim("$first $last");

if (empty($property['agentName']) && !empty($property['agencyName'])) {
    $property['agentName'] = $property['agencyName'];
}
if (empty($property['agentName'])) {
    $property['agentName'] = 'Agency';
}

$property['similar'] = $similar;

 
echo json_encode($property, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);