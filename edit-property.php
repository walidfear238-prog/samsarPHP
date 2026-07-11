<?php
session_start();
require "db/connect.php";
if (!isset($_SESSION['user_id'])) {
    header('location: index.php');
    exit;
}

$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

// Verify property belongs to user
$stmt = $conn->prepare("SELECT * FROM properties WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $property_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$property = $result->fetch_assoc();

if (!$property) {
    header('location: my-properties.php?error=Property not found');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SAMSAR · Edit Property</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles/dashboard-shell.css" />
    <link rel="stylesheet" href="styles/samsar-transitions.css" />
</head>

<body>
    <div class="cursor"></div>
    <div class="cursor-dot"></div>

    <div class="dashboard-shell">
        <aside class="dashboard-sidebar">
            <a class="dashboard-brand" href="index.php">
                <svg width="25" height="25
                " class="brand-mark" viewBox="0 0 1080 1080" fill="currentColor" aria-hidden="true">
                    <path
                        d="M734.34,464.81v-21.85c0-2.87-1.34-5.57-3.62-7.31l-152.36-116.23c-17.21-13.13-40.93-13.69-58.74-1.39l-170,117.41c-2.48,1.72-3.97,4.54-3.97,7.56v48.22c0,5.08,4.11,9.19,9.19,9.19h517.47c5.08,0,9.19,4.11,9.19,9.19v362.76c0,5.08-4.11,9.19-9.19,9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-189.17c0-5.08,4.11-9.19,9.19-9.19h128.79c5.08,0,9.19,4.11,9.19,9.19v42c0,5.08,4.11,9.19,9.19,9.19h370.3c5.08,0,9.19-4.11,9.19-9.19v-68.42c0-5.08-4.11-9.19-9.19-9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-272.61c0-3.02,1.48-5.85,3.97-7.56l223.99-154.69,97.47-67.32c17.82-12.3,41.53-11.74,58.74,1.39l94.18,71.86,57.49,43.85,143.55,109.51c2.28,1.74,3.62,4.44,3.62,7.31v94.68c0,5.08-4.11,9.19-9.19,9.19h-128.79c-5.08,0-9.19-4.11-9.19-9.19Z" />
                </svg>
                <span class="dashboard-brand-word">SAMSAR</span>
            </a>
            <nav class="dashboard-nav">
                <div class="dashboard-group">MAIN</div>
                <a class="dashboard-link" href="dashboard.php"><span class="ico">⌂</span>Overview</a>
                <a class="dashboard-link" href="my-properties.php"><span class="ico">▤</span>My Properties</a>
                <a class="dashboard-link" href="add-property.php"><span class="ico">+</span>Add Property</a>
                <div class="dashboard-group">SOCIAL</div>
                <a class="dashboard-link" href="messages.php"><span class="ico">✉</span>Messages <em
                        class="dashboard-badge red" id="bdg-msg">0</em></a>
                <a class="dashboard-link" href="favorites.php"><span class="ico">♡</span>Favorites <em
                        class="dashboard-badge red" id="bdg-fav">0</em></a>
                <a class="dashboard-link" href="following.php"><span class="ico">࿄</span>Following</a>
                <a class="dashboard-link" href="notifications.php"><span class="ico">⌖</span>Notifications <em
                        class="dashboard-badge red" id="bdg-notif-2">0</em></a>
            </nav>
            <?php
            $id = $_SESSION['user_id'];
            $stmt = $conn->prepare("SELECT firstname , role , profile_image FROM users where id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            ?>
            <div class="dashboard-side-foot">
                <div class="dashboard-user">
                    <?php
                    echo "<img src='" . htmlspecialchars($user['profile_image']) . "' alt='profile picture'/>";
                    echo " <div><strong>" . htmlspecialchars($user['firstname']) . "</strong><span>" .
                        htmlspecialchars($user['role']) . "</span></div>";
                    ?>
                </div>
                <a class="dashboard-signout" href="logout.php" data-logout>Sign out →</a>
            </div>
        </aside>

        <main class="dashboard-main">
            <header class="dashboard-head">
                <div>
                    <h1>Edit Property</h1>
                    <p>Update listing details.</p>
                </div>
                <a href="my-properties.php" class="btn btn-ghost">← Back to Properties</a>
            </header>

            <form id="edit-form" class="ap-form" method="POST" action="api/update-property.php"
                enctype="multipart/form-data">
                <input type="hidden" name="property_id" value="<?php echo $property['id']; ?>">

                <div class="content-card">
                    <h3 class="ap-section-title">Basic information</h3>
                    <div class="ap-grid">
                        <label class="ap-field">
                            <span>Property Title *</span>
                            <input name="title" type="text" value="<?php echo htmlspecialchars($property['title']); ?>"
                                required />
                        </label>
                        <label class="ap-field">
                            <span>Property Type *</span>
                            <select name="type" required>
                                <option value="Villa"
                                    <?php echo $property['property_type'] == 'Villa' ? 'selected' : ''; ?>>Villa
                                </option>
                                <option value="Riad"
                                    <?php echo $property['property_type'] == 'Riad' ? 'selected' : ''; ?>>Riad</option>
                                <option value="Apartment"
                                    <?php echo $property['property_type'] == 'Apartment' ? 'selected' : ''; ?>>Apartment
                                </option>
                                <option value="Penthouse"
                                    <?php echo $property['property_type'] == 'Penthouse' ? 'selected' : ''; ?>>Penthouse
                                </option>
                                <option value="Land"
                                    <?php echo $property['property_type'] == 'Land' ? 'selected' : ''; ?>>Land</option>
                                <option value="Commercial"
                                    <?php echo $property['property_type'] == 'Commercial' ? 'selected' : ''; ?>>
                                    Commercial</option>
                            </select>
                        </label>
                        <label class="ap-field">
                            <span>Price (MAD) *</span>
                            <input name="price" type="number" value="<?php echo $property['price']; ?>" required />
                        </label>
                        <label class="ap-field">
                            <span>Status *</span>
                            <select name="status" required>
                                <option value="available"
                                    <?php echo $property['status'] == 'available' ? 'selected' : ''; ?>>For sale
                                </option>
                                <option value="rented" <?php echo $property['status'] == 'rented' ? 'selected' : ''; ?>>
                                    For rent</option>
                                <option value="sold" <?php echo $property['status'] == 'sold' ? 'selected' : ''; ?>>Sold
                                </option>
                                <option value="pending"
                                    <?php echo $property['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            </select>
                        </label>
                    </div>
                    <label class="ap-field" style="margin-top:14px;display:block">
                        <span>Description</span>
                        <textarea name="desc"
                            rows="4"><?php echo htmlspecialchars($property['description'] ?? ''); ?></textarea>
                    </label>
                </div>

                <div class="content-card">
                    <h3 class="ap-section-title">Location</h3>
                    <div class="ap-grid">
                        <label class="ap-field">
                            <span>City *</span>
                            <select name="city" required>
                                <option value="Casablanca"
                                    <?php echo $property['city'] == 'Casablanca' ? 'selected' : ''; ?>>Casablanca
                                </option>
                                <option value="Marrakech"
                                    <?php echo $property['city'] == 'Marrakech' ? 'selected' : ''; ?>>Marrakech</option>
                                <option value="Rabat" <?php echo $property['city'] == 'Rabat' ? 'selected' : ''; ?>>
                                    Rabat</option>
                                <option value="Tangier" <?php echo $property['city'] == 'Tangier' ? 'selected' : ''; ?>>
                                    Tangier</option>
                                <option value="Fès" <?php echo $property['city'] == 'Fès' ? 'selected' : ''; ?>>Fès
                                </option>
                                <option value="Essaouira"
                                    <?php echo $property['city'] == 'Essaouira' ? 'selected' : ''; ?>>Essaouira</option>
                                <option value="Agadir" <?php echo $property['city'] == 'Agadir' ? 'selected' : ''; ?>>
                                    Agadir</option>
                            </select>
                        </label>
                        <label class="ap-field">
                            <span>District</span>
                            <input name="district" type="text"
                                value="<?php echo htmlspecialchars($property['district'] ?? ''); ?>" />
                        </label>
                    </div>
                </div>

                <div class="content-card">
                    <h3 class="ap-section-title">Specifications</h3>
                    <div class="ap-grid ap-grid-4">
                        <label class="ap-field">
                            <span>Bedrooms</span>
                            <input name="beds" type="number" min="0"
                                value="<?php echo $property['bedrooms'] ?? 0; ?>" />
                        </label>
                        <label class="ap-field">
                            <span>Bathrooms</span>
                            <input name="baths" type="number" min="0"
                                value="<?php echo $property['bathrooms'] ?? 0; ?>" />
                        </label>
                        <label class="ap-field">
                            <span>Area (m²)</span>
                            <input name="area" type="number" min="0" value="<?php echo $property['area'] ?? 0; ?>" />
                        </label>
                    </div>
                </div>
                <div class="content-card">
                    <h3 class="ap-section-title">Property Images</h3>

                    <!-- Current Images Display -->
                    <div id="current-images"
                        style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px;">
                        <?php
        // Fetch current images for this property
        $img_stmt = $conn->prepare("SELECT id, image_path, is_primary FROM property_images WHERE property_id = ?");
        $img_stmt->bind_param("i", $property_id);
        $img_stmt->execute();
        $img_result = $img_stmt->get_result();
        $images = $img_result->fetch_all(MYSQLI_ASSOC);
        
        foreach ($images as $image):
        ?>
                        <div class="image-item" data-image-id="<?php echo $image['id']; ?>">
                            <img src="uploads/property_images/<?php echo $image['image_path']; ?>"
                                style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px;">
                            <div style="display: flex; gap: 5px; margin-top: 8px;">
                                <?php if ($image['is_primary']): ?>
                                <span
                                    style="background: #2D7D5A; color: white; padding: 2px 8px; border-radius: 4px; font-size: 11px;">Primary</span>
                                <?php else: ?>
                                <button type="button" class="set-primary-btn" data-id="<?php echo $image['id']; ?>"
                                    style="background: #666; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 11px;">
                                    Set as Primary
                                </button>
                                <?php endif; ?>
                                <button type="button" class="remove-image-btn" data-id="<?php echo $image['id']; ?>"
                                    style="background: #C72C41; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 11px;">
                                    Remove
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php $img_stmt->close(); ?>
                    </div>

                    <!-- Upload New Images -->
                    <label class="ap-field">
                        <span>Add New Images</span>
                        <input type="file" name="images[]" multiple
                            accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" />
                        <small style="color: #666; margin-top: 5px;">You can select multiple images. Supported formats:
                            JPG, PNG, GIF, WEBP</small>
                    </label>

                    <!-- Hidden inputs for image management -->
                    <input type="hidden" name="remove_images" id="remove_images_input" value="[]">
                    <input type="hidden" name="primary_image_id" id="primary_image_id_input" value="">
                </div>

                <div class="ap-actions">
                    <a href="my-properties.php" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </main>
    </div>

    <style>
    .ap-form {
        display: flex;
        flex-direction: column;
        gap: 18px;
        max-width: 980px
    }

    .ap-section-title {
        font-family: Fraunces, serif;
        font-size: 20px;
        margin: 0 0 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f0f0f0
    }

    .ap-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px
    }

    .ap-grid-4 {
        grid-template-columns: repeat(4, 1fr)
    }

    .ap-field {
        display: flex;
        flex-direction: column;
        gap: 6px
    }

    .ap-field span {
        font-size: 11px;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #666;
        font-weight: 600
    }

    .ap-field input,
    .ap-field select,
    .ap-field textarea {
        background: #fff;
        border: 1px solid #e5e5e5;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 14px;
        font-family: inherit;
        transition: all .2s
    }

    .ap-field input:focus,
    .ap-field select:focus,
    .ap-field textarea:focus {
        outline: none;
        border-color: #C72C41;
        box-shadow: 0 0 0 3px rgba(199, 44, 65, .12)
    }

    .ap-field textarea {
        resize: vertical;
        min-height: 90px;
        grid-column: 1 / -1
    }

    .ap-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 8px
    }

    .btn-ghost {
        background: transparent;
        border: 1px solid #ddd;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        color: #333;
    }

    .btn-ghost:hover {
        border-color: #999;
    }

    @media(max-width:780px) {

        .ap-grid,
        .ap-grid-4 {
            grid-template-columns: 1fr
        }
    }
    </style>

    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/dashboard-shell.js"></script>
    <script src="scripts/dashboard.js"></script>

    <script>
    // Image management
    let imagesToRemove = [];

    // Remove image
    document.querySelectorAll('.remove-image-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const imageId = this.getAttribute('data-id');
            const imageDiv = this.closest('.image-item');

            if (confirm('Are you sure you want to remove this image?')) {
                imagesToRemove.push(imageId);
                imageDiv.style.display = 'none';
                document.getElementById('remove_images_input').value = JSON.stringify(imagesToRemove);
            }
        });
    });

    // Set primary image
    document.querySelectorAll('.set-primary-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const imageId = this.getAttribute('data-id');

            // Update UI for all images
            document.querySelectorAll('.image-item').forEach(item => {
                const primarySpan = item.querySelector('span');
                if (primarySpan && primarySpan.textContent === 'Primary') {
                    primarySpan.remove();
                    const setBtn = document.createElement('button');
                    setBtn.type = 'button';
                    setBtn.className = 'set-primary-btn';
                    setBtn.setAttribute('data-id', imageId);
                    setBtn.textContent = 'Set as Primary';
                    setBtn.style.cssText =
                        'background: #666; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 11px;';
                    item.querySelector('div').appendChild(setBtn);

                    // Add event listener to the new button
                    setBtn.addEventListener('click', arguments.callee);
                }
            });

            // Update current button to show it's primary
            this.remove();
            const primarySpan = document.createElement('span');
            primarySpan.textContent = 'Primary';
            primarySpan.style.cssText =
                'background: #2D7D5A; color: white; padding: 2px 8px; border-radius: 4px; font-size: 11px;';
            this.parentElement.appendChild(primarySpan);

            document.getElementById('primary_image_id_input').value = imageId;
        });
    });

    // Handle form submission (single handler)
    const form = document.getElementById('edit-form');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Create FormData with all form data including files
        const formData = new FormData(form);

        // Add images to remove
        if (imagesToRemove.length > 0) {
            formData.append('remove_images', JSON.stringify(imagesToRemove));
        }

        // Add primary image
        const primaryImageId = document.getElementById('primary_image_id_input').value;
        if (primaryImageId) {
            formData.append('primary_image_id', primaryImageId);
        }

        // Debug: Log all form data being sent (excluding file contents)
        console.log("Form data being sent:");
        for (let pair of formData.entries()) {
            if (pair[0] !== 'images[]') {
                console.log(pair[0] + ': ' + pair[1]);
            } else {
                console.log(pair[0] + ': ' + pair[1].name);
            }
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;

        // Show loading state
        submitBtn.textContent = 'Saving...';
        submitBtn.disabled = true;

        try {
            const response = await fetch('api/update-property.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            console.log("Server response:", result);

            if (result.success) {
                // Show success message
                submitBtn.textContent = 'Saved ✓';
                submitBtn.style.background = '#2D7D5A';

                // Redirect after delay
                setTimeout(() => {
                    if (window.SamsarTransition) {
                        window.SamsarTransition.leave(() => {
                            window.location.href = 'my-properties.php';
                        });
                    } else {
                        window.location.href = 'my-properties.php';
                    }
                }, 700);
            } else {
                alert(result.message || 'Failed to update property');
                console.error('Update failed:', result);
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                submitBtn.style.background = '';
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while saving. Please check the console for details.');
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    });
    </script>
</body>

</html>