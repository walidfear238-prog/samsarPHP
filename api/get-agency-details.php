<?php
require_once __DIR__ . '/../php/lang.php';
// Returns a single agency's real profile data plus the list of properties
// that belong to that agency only. Public endpoint - no auth required,
// used by 05-agency-profile.php.

header('Content-Type: application/json');

require "../db/connect.php";

if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => t('api.err.db_connection_failed')]);
    exit;
}

$agency_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($agency_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => t('api.err.invalid_agency_id')]);
    exit;
}

$cityLabels = [
    'marrakech'  => 'Marrakech',
    'casablanca' => 'Casablanca',
    'tangier'    => 'Tangier',
    'rabat'      => 'Rabat',
    'fès'        => 'Fès',
    'essaouira'  => 'Essaouira',
];

// Only ever return an account that is actually an agency
$stmt = $conn->prepare("
    SELECT id, firstname, lastname, agencyName, email, phone, city, profile_image, is_verified, created_at
    FROM users
    WHERE id = ? AND role = 'agency'
");
$stmt->bind_param("i", $agency_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => t('api.err.agency_not_found')]);
    exit;
}

$agency = $result->fetch_assoc();
$stmt->close();

$name = trim((string) $agency['agencyName']);
if ($name === '') {
    $name = trim($agency['firstname'] . ' ' . $agency['lastname']);
}

$cityRaw   = (string) ($agency['city'] ?? '');
$cityLabel = $cityLabels[$cityRaw] ?? ($cityRaw !== '' ? ucfirst($cityRaw) : '');

// Every property that belongs to this specific agency - nothing else
$properties = [];
$types      = [];
$districts  = [];

$p_stmt = $conn->prepare("
    SELECT
        p.id, p.title, p.property_type, p.price, p.status, p.city, p.district,
        p.bedrooms, p.bathrooms, p.area,
        (SELECT pi.image_path FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) AS img
    FROM properties p
    WHERE p.user_id = ?
    ORDER BY p.id DESC
");
$p_stmt->bind_param("i", $agency_id);
$p_stmt->execute();
$p_result = $p_stmt->get_result();

while ($row = $p_result->fetch_assoc()) {
    if (!empty($row['property_type']) && !in_array($row['property_type'], $types, true)) {
        $types[] = $row['property_type'];
    }
    if (!empty($row['district']) && !in_array($row['district'], $districts, true)) {
        $districts[] = $row['district'];
    }
    $properties[] = $row;
}
$p_stmt->close();

// Join year / time on platform - derived from the real account creation date
$joinYear        = null;
$yearsOnPlatform = 0;
if (!empty($agency['created_at'])) {
    $created         = new DateTime($agency['created_at']);
    $now             = new DateTime();
    $joinYear        = (int) $created->format('Y');
    $yearsOnPlatform = $now->diff($created)->y;
}

echo json_encode([
    'id'              => (int) $agency['id'],
    'name'            => $name,
    'city'            => $cityLabel,
    'districts'       => $districts,
    'specialties'     => $types,
    'email'           => $agency['email'],
    'phone'           => $agency['phone'],
    'logo'            => $agency['profile_image'] ?: '',
    'is_verified'     => (int) $agency['is_verified'] === 1,
    'joinYear'        => $joinYear,
    'yearsOnPlatform' => $yearsOnPlatform,
    'listingsCount'   => count($properties),
    'properties'      => $properties,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$conn->close();
