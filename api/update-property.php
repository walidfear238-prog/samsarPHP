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

$user_id = $_SESSION['user_id'];

// Check if property_id is provided
if (!isset($_POST['property_id']) || empty($_POST['property_id'])) {
    echo json_encode(['success' => false, 'message' => t('api.property.err.id_required')]);
    exit;
}

$property_id = intval($_POST['property_id']);

// Verify ownership
$check_stmt = $conn->prepare("SELECT id FROM properties WHERE id = ? AND user_id = ?");
$check_stmt->bind_param("ii", $property_id, $user_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => t('api.property.err.edit_permission')]);
    $check_stmt->close();
    exit;
}
$check_stmt->close();

// Get form data
$title = trim($_POST['title'] ?? '');
$property_type = trim($_POST['type'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$status = trim($_POST['status'] ?? 'available');
$city = trim($_POST['city'] ?? '');
$district = trim($_POST['district'] ?? '');
$bedrooms = intval($_POST['beds'] ?? 0);
$bathrooms = intval($_POST['baths'] ?? 0);
$area = intval($_POST['area'] ?? 0);
$description = trim($_POST['desc'] ?? '');

// Validate required fields
if (empty($title)) {
    echo json_encode(['success' => false, 'message' => t('api.property.err.title_required')]);
    exit;
}
if (empty($property_type)) {
    echo json_encode(['success' => false, 'message' => t('api.property.err.type_required')]);
    exit;
}
if ($price <= 0) {
    echo json_encode(['success' => false, 'message' => t('api.property.err.price_required')]);
    exit;
}
if (empty($city)) {
    echo json_encode(['success' => false, 'message' => t('api.property.err.city_required')]);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Update property details
    $update_stmt = $conn->prepare("
        UPDATE properties 
        SET title = ?, 
            property_type = ?, 
            price = ?, 
            status = ?, 
            city = ?, 
            district = ?, 
            bedrooms = ?, 
            bathrooms = ?, 
            area = ?, 
            description = ?
        WHERE id = ? AND user_id = ?
    ");

    if (!$update_stmt) {
        throw new Exception('Database prepare error: ' . $conn->error);
    }

    $update_stmt->bind_param(
        "ssdsssiiisii",
        $title,
        $property_type,
        $price,
        $status,
        $city,
        $district,
        $bedrooms,
        $bathrooms,
        $area,
        $description,
        $property_id,
        $user_id
    );

    if (!$update_stmt->execute()) {
        throw new Exception('Update failed: ' . $update_stmt->error);
    }
    $update_stmt->close();

    // Handle image uploads
    $uploaded_images = [];
    $image_paths = [];

    // Check if images were uploaded
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $upload_dir = __DIR__ . "/../uploads/property_images/";

        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $files = $_FILES['images'];
        $file_count = count($files['name']);

        for ($i = 0; $i < $file_count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $file_name = time() . '_' . uniqid() . '_' . basename($files['name'][$i]);
                $target_path = $upload_dir . $file_name;

                // Validate file type
                $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                $file_type = $files['type'][$i];

                if (in_array($file_type, $allowed_types)) {
                    if (move_uploaded_file($files['tmp_name'][$i], $target_path)) {
                        $image_paths[] = $file_name;
                        $uploaded_images[] = $file_name;
                    }
                }
            }
        }

        // Insert new images into database
        if (!empty($image_paths)) {
            $insert_stmt = $conn->prepare("INSERT INTO property_images (property_id, image_path, is_primary) VALUES (?, ?, ?)");

            foreach ($image_paths as $index => $image_path) {
                $is_primary = ($index === 0 && !has_primary_image($conn, $property_id)) ? 1 : 0;
                $insert_stmt->bind_param("isi", $property_id, $image_path, $is_primary);
                $insert_stmt->execute();
            }
            $insert_stmt->close();
        }
    }

    // Handle removal of images
    if (isset($_POST['remove_images']) && !empty($_POST['remove_images'])) {
        $remove_images = json_decode($_POST['remove_images'], true);

        if (is_array($remove_images) && !empty($remove_images)) {
            foreach ($remove_images as $image_id) {
                // Get image path first
                $get_img_stmt = $conn->prepare("SELECT image_path FROM property_images WHERE id = ? AND property_id = ?");
                $get_img_stmt->bind_param("ii", $image_id, $property_id);
                $get_img_stmt->execute();
                $img_result = $get_img_stmt->get_result();
                $img = $img_result->fetch_assoc();

                if ($img) {
                    // Delete file from server
                    $file_path = __DIR__ . "/../uploads/property_images/" . $img['image_path'];
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }

                    // Delete from database
                    $delete_stmt = $conn->prepare("DELETE FROM property_images WHERE id = ? AND property_id = ?");
                    $delete_stmt->bind_param("ii", $image_id, $property_id);
                    $delete_stmt->execute();
                    $delete_stmt->close();
                }
                $get_img_stmt->close();
            }
        }
    }

    // Handle setting primary image
    if (isset($_POST['primary_image_id']) && !empty($_POST['primary_image_id'])) {
        $primary_image_id = intval($_POST['primary_image_id']);

        // Reset all images to non-primary
        $reset_stmt = $conn->prepare("UPDATE property_images SET is_primary = 0 WHERE property_id = ?");
        $reset_stmt->bind_param("i", $property_id);
        $reset_stmt->execute();
        $reset_stmt->close();

        // Set the selected image as primary
        $set_primary_stmt = $conn->prepare("UPDATE property_images SET is_primary = 1 WHERE id = ? AND property_id = ?");
        $set_primary_stmt->bind_param("ii", $primary_image_id, $property_id);
        $set_primary_stmt->execute();
        $set_primary_stmt->close();
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => t('api.property.update_success'),
        'uploaded_images' => $uploaded_images
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();

function has_primary_image($conn, $property_id)
{
    $check = $conn->prepare("SELECT id FROM property_images WHERE property_id = ? AND is_primary = 1");
    $check->bind_param("i", $property_id);
    $check->execute();
    $result = $check->get_result();
    $has_primary = $result->num_rows > 0;
    $check->close();
    return $has_primary;
}
?>