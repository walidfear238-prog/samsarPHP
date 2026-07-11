<?php
// Returns all real agencies (users with role = 'agency') from the database.
// Public endpoint - no auth required, used by 04-agencies.php.

header('Content-Type: application/json');

require "../db/connect.php";

if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Maps the `city` enum values stored in the DB to the display labels
// used by the city filter tabs on the agencies page.
$cityLabels = [
    'marrakech'  => 'Marrakech',
    'casablanca' => 'Casablanca',
    'tangier'    => 'Tangier',
    'rabat'      => 'Rabat',
    'fès'        => 'Fès',
    'essaouira'  => 'Essaouira',
];

$stmt = $conn->prepare("
    SELECT
        u.id,
        u.firstname,
        u.lastname,
        u.agencyName,
        u.city,
        u.profile_image,
        u.is_verified,
        u.created_at,
        (SELECT COUNT(*) FROM properties p WHERE p.user_id = u.id) AS listings_count
    FROM users u
    WHERE u.role = 'agency'
    ORDER BY u.id DESC
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Query prepare failed: ' . $conn->error]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$agencies = [];
while ($row = $result->fetch_assoc()) {
    $name = trim((string) $row['agencyName']);
    if ($name === '') {
        $name = trim($row['firstname'] . ' ' . $row['lastname']);
    }

    $cityRaw   = (string) ($row['city'] ?? '');
    $cityLabel = $cityLabels[$cityRaw] ?? ($cityRaw !== '' ? ucfirst($cityRaw) : '');

    $agencies[] = [
        'id'          => (int) $row['id'],
        'name'        => $name,
        'city'        => $cityLabel,
        'logo'        => $row['profile_image'] ?: '',
        'listings'    => (int) $row['listings_count'],
        'is_verified' => (int) $row['is_verified'] === 1,
        'joined'      => $row['created_at'],
    ];
}

$stmt->close();
$conn->close();

echo json_encode($agencies, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
