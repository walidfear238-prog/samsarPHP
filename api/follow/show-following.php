<?php

session_start();
require __DIR__ . "/../../db/connect.php";
require_once __DIR__ . "/../../php/lang.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => t('api.err.unauthorized')]);
    exit;
}

$follower_id = (int) $_SESSION['user_id'];

// Maps the `city` enum values stored in the DB to display labels
$cityLabels = [
    'marrakech'  => 'Marrakech',
    'casablanca' => 'Casablanca',
    'tangier'    => 'Tangier',
    'rabat'      => 'Rabat',
    'fès'        => 'Fès',
    'essaouira'  => 'Essaouira',
];

// Select all the users that the logged-in user is following
$stmt = $conn->prepare("
    SELECT
        u.id,
        u.firstname,
        u.lastname,
        u.agencyName,
        u.city,
        u.profile_image,
        (SELECT COUNT(*) FROM properties p WHERE p.user_id = u.id) AS listings_count
    FROM following f
    JOIN users u ON u.id = f.following_id
    WHERE f.follower_id = ?
    ORDER BY f.created_at DESC
");
$stmt->bind_param("i", $follower_id);
$stmt->execute();
$result = $stmt->get_result();

$following = [];
while ($row = $result->fetch_assoc()) {
    $name = trim((string) $row['agencyName']);
    if ($name === '') {
        $name = trim($row['firstname'] . ' ' . $row['lastname']);
    }

    $cityRaw   = (string) ($row['city'] ?? '');
    $cityLabel = $cityLabels[$cityRaw] ?? ($cityRaw !== '' ? ucfirst($cityRaw) : '');

    $following[] = [
        'id'       => (int) $row['id'],
        'name'     => $name,
        'city'     => $cityLabel,
        'avatar'   => $row['profile_image'] ?: '',
        'listings' => (int) $row['listings_count'],
    ];
}

$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'following' => $following], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


?>