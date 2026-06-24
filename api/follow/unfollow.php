<?php
session_start();
require __DIR__ . "/../../db/connect.php";

header('Content-Type: application/json');

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

// ── Delete ─────────────────────────────────────────────────────────────────
$stmt = $conn->prepare("DELETE FROM following WHERE follower_id = ? AND following_id = ?");
$stmt->bind_param("ii", $follower_id, $following_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $cnt = $conn->prepare("SELECT COUNT(*) AS count FROM following WHERE following_id = ?");
        $cnt->bind_param("i", $following_id);
        $cnt->execute();
        $count = (int) $cnt->get_result()->fetch_assoc()['count'];

        echo json_encode([
            'success'         => true,
            'message'         => 'Unfollowed successfully',
            'followers_count' => $count
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'You were not following this user']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to unfollow. Please try again.']);
}