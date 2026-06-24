<?php
session_start();
require __DIR__ . "/../../db/connect.php";

header('Content-Type: application/json');

// ── Auth ───────────────────────────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// ── Input ──────────────────────────────────────────────────────────────────
$follower_id  = (int) $_SESSION['user_id'];
$following_id = isset($_POST['following_id']) ? (int) $_POST['following_id'] : 0;

if ($following_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

if ($following_id === $follower_id) {
    echo json_encode(['success' => false, 'message' => 'You cannot follow yourself']);
    exit;
}

// ── Target user exists ────────────────────────────────────────────────────
$chk = $conn->prepare("SELECT id FROM users WHERE id = ?");
$chk->bind_param("i", $following_id);
$chk->execute();

if ($chk->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// ── Already following ─────────────────────────────────────────────────────
$dup = $conn->prepare("SELECT id FROM following WHERE follower_id = ? AND following_id = ?");
$dup->bind_param("ii", $follower_id, $following_id);
$dup->execute();

if ($dup->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Already following this user']);
    exit;
}

// ── Insert ─────────────────────────────────────────────────────────────────
$stmt = $conn->prepare("INSERT INTO following (follower_id, following_id) VALUES (?, ?)");
$stmt->bind_param("ii", $follower_id, $following_id);

if ($stmt->execute()) {
    $cnt = $conn->prepare("SELECT COUNT(*) AS count FROM following WHERE following_id = ?");
    $cnt->bind_param("i", $following_id);
    $cnt->execute();
    $count = (int) $cnt->get_result()->fetch_assoc()['count'];

    echo json_encode([
        'success'         => true,
        'message'         => 'Followed successfully',
        'followers_count' => $count
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to follow. Please try again.'
    ]);
}