<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . "/../db/connect.php";
require_once __DIR__ . "/../php/lang.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => t('api.err.unauthorized_login')]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => t('api.err.method_not_allowed')]);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

/**
 * Moves an uploaded avatar into uploads/profile/, mirroring the same
 * validation rules used at registration time (see upload_profile_picture()
 * in 09-register.php), but with the 2MB limit already shown in the
 * profile UI instead of the 500KB registration limit.
 *
 * Returns ['path' => ...] on success or ['error' => ...] on failure.
 */
function upload_avatar($file)
{
    $target_dir = __DIR__ . "/../uploads/profile/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ["error" => t('profile.err.file_upload')];
    }

    // 2MB limit
    if ($file["size"] > 2 * 1024 * 1024) {
        return ["error" => t('profile.err.file_too_large')];
    }

    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    if (!in_array($file_extension, ["jpg", "jpeg", "png"])) {
        return ["error" => t('profile.err.file_type')];
    }

    // Confirm it's really an image (not just a renamed file)
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ["error" => t('profile.err.file_not_image')];
    }

    $unique_filename = time() . "_" . uniqid() . "." . $file_extension;
    $target_file = $target_dir . $unique_filename;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["path" => "uploads/profile/" . $unique_filename];
    }

    return ["error" => t('profile.err.file_upload')];
}

// ── Input ────────────────────────────────────────────────────────────────
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$city = trim($_POST['city'] ?? '');
$new_password = (string) ($_POST['new_password'] ?? '');
$confirm_password = (string) ($_POST['confirm_password'] ?? '');

$allowed_cities = ['marrakech', 'casablanca', 'tangier', 'rabat', 'fès', 'essaouira'];

// ── Validation ───────────────────────────────────────────────────────────
if ($name === '') {
    echo json_encode(['success' => false, 'message' => t('profile.err.name_required')]);
    exit;
}

if ($email === '') {
    echo json_encode(['success' => false, 'message' => t('profile.err.email_required')]);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => t('profile.err.email_invalid')]);
    exit;
}

if ($city !== '' && !in_array($city, $allowed_cities, true)) {
    $city = ''; // silently ignore an unexpected value rather than failing the whole update
}

if ($new_password !== '' || $confirm_password !== '') {
    if (strlen($new_password) < 6) {
        echo json_encode(['success' => false, 'message' => t('profile.err.password_length')]);
        exit;
    }
    if ($new_password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => t('profile.err.password_mismatch')]);
        exit;
    }
}

// Email must stay unique across accounts
$emailCheck = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$emailCheck->bind_param("si", $email, $user_id);
$emailCheck->execute();
if ($emailCheck->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => t('profile.err.email_taken')]);
    $emailCheck->close();
    exit;
}
$emailCheck->close();

// Phone must stay unique across accounts too (users.phone has a UNIQUE key)
if ($phone !== '') {
    $phoneCheck = $conn->prepare("SELECT id FROM users WHERE phone = ? AND id != ?");
    $phoneCheck->bind_param("si", $phone, $user_id);
    $phoneCheck->execute();
    if ($phoneCheck->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => t('profile.err.phone_taken')]);
        $phoneCheck->close();
        exit;
    }
    $phoneCheck->close();
}

// Split "Full name" into firstname/lastname to match the users table columns
$nameParts = explode(' ', $name, 2);
$firstname = $nameParts[0];
$lastname = $nameParts[1] ?? '';

// Store blanks as NULL: phone has a UNIQUE key (two users both saving ''
// would collide) and city is an ENUM that shouldn't hold an empty string.
$phone = $phone !== '' ? $phone : null;
$city = $city !== '' ? $city : null;

// ── Optional avatar upload ──────────────────────────────────────────────
$newAvatarPath = null;
$oldAvatarPath = null;
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
    // Grab the current avatar path first so the old file can be removed
    // once the new one is safely saved.
    $currentStmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
    $currentStmt->bind_param("i", $user_id);
    $currentStmt->execute();
    $oldAvatarPath = $currentStmt->get_result()->fetch_assoc()['profile_image'] ?? null;
    $currentStmt->close();

    $uploadResult = upload_avatar($_FILES['avatar']);
    if (isset($uploadResult['error'])) {
        echo json_encode(['success' => false, 'message' => $uploadResult['error']]);
        exit;
    }
    $newAvatarPath = $uploadResult['path'];
}

// ── Build the update ─────────────────────────────────────────────────────
$fields = "firstname = ?, lastname = ?, email = ?, phone = ?, city = ?";
$types = "sssss";
$params = [$firstname, $lastname, $email, $phone, $city];

if ($new_password !== '') {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $fields .= ", password = ?";
    $types .= "s";
    $params[] = $hashed_password;
}

if ($newAvatarPath !== null) {
    $fields .= ", profile_image = ?";
    $types .= "s";
    $params[] = $newAvatarPath;
}

$types .= "i";
$params[] = $user_id;

$stmt = $conn->prepare("UPDATE users SET {$fields} WHERE id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => t('profile.err.update_failed')]);
    exit;
}
$stmt->bind_param($types, ...$params);

if (!$stmt->execute()) {
    error_log("Profile update failed for user {$user_id}: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => t('profile.err.update_failed')]);
    $stmt->close();
    exit;
}
$stmt->close();

// Delete the old avatar file now that the new one is saved (only ever
// touches files this app uploaded itself under uploads/profile/, never an
// external URL like the default placeholder avatar)
if ($newAvatarPath !== null && $oldAvatarPath && strpos($oldAvatarPath, 'uploads/profile/') === 0) {
    $oldFile = __DIR__ . "/../" . $oldAvatarPath;
    if (file_exists($oldFile)) {
        unlink($oldFile);
    }
}

// Keep the session in sync with what's shown elsewhere in the app
$_SESSION['user_name'] = $firstname;
$_SESSION['user_email'] = $email;

echo json_encode([
    'success' => true,
    'message' => t('profile.success.updated'),
    'user' => [
        'name' => trim($firstname . ' ' . $lastname),
        'email' => $email,
        'avatar' => $newAvatarPath !== null ? $newAvatarPath : null,
    ]
]);

$conn->close();
