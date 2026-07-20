<?php


session_start();
require "db/connect.php";
require_once __DIR__ . "/php/upload-limits.php";
if (!isset($_SESSION['user_id'])) {
    header('location: index.php');
    exit;
}

//enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', '1');
//function to upload an image securely 
//clean input
function clean_input($data)
{
    $data = trim($data);
    $data = htmlspecialchars($data);
    return $data;
}
/*function for title error
function title_errors($title)
{
    if (!isset($title)) {
        $error = "property title required";
    }
    return $error;
}
//function for price
function price_errors($price)
{
    if (!preg_match("/[0-9]/", $price)) {
        $error = "price accept only numbers *0-9* ";
    } elseif (empty($price)) {
        $error = "price required";
    }
    return $error;
}
//description errors

function desc_errors($desc)
{

    if (empty($desc)) {
        $error = "discreption require";
    }
    return $error;
}
//district
function district_errors($district)
{
    if (empty($district)) {
        $error = "district is required";
    }
    return $error;
}*/

function uploadImage($file)
{
    $image_name = $file['name'];
    $tmp_name = $file['tmp_name'];
    //if there is no file return empty string 
    if (empty($image_name))
        return "";
    //allowed image extensions
    $allowed_ext = ["jpg", "png", "jpeg", "gif"];
    //get the file extension
    $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
    //only proceed if extension is allowed
    if (in_array($ext, $allowed_ext)) {
        //replace any character not a-z A-Z 0-9 dot with underscore
        $clean_name = preg_replace("/[^a-zA-Z0-9.]/", "_", $image_name);
        //unique name by adding current timestamp
        $new_name = time() . "_" . $clean_name;
        //full path to save the upload file
        $folder = "uploads/property_images/" . $new_name;
        //ensure upload directory exists
        if (!is_dir(dirname($folder)))
            mkdir(dirname($folder), 0755, true);
        //move the file from temp folder to upload folder
        if (move_uploaded_file($tmp_name, $folder)) {
            //return the final file name to store in db
            return $new_name;
        }
    }
    return "";
}
//function to add the product informations
$user_id = $_SESSION['user_id'];
function add_property($conn, $user_id, $title, $type, $price, $status, $desc, $city, $district, $beds, $baths, $area)
{
    // insert informations first
    $stmt = $conn->prepare("INSERT into properties (user_id, title, property_type, price, status, description, city, district, bedrooms, bathrooms, area) values(?,?,?,?,?,?,?,?,?,?,?) ");
    $stmt->bind_param("ississssiii", $user_id, $title, $type, $price, $status, $desc, $city, $district, $beds, $baths, $area);
    $stmt->execute();
    return $conn->insert_id;
}
//function to add the product images
function add_images($conn, $property_id, $img)
{

    $stmt = $conn->prepare("INSERT into property_images (property_id , image_path) values(?,?)  ");
    $stmt->bind_param("is", $property_id, $img);
    return $stmt->execute();


}

//handle the add
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = clean_input($_POST['title']);
    $type = clean_input($_POST['type']);
    $price = clean_input($_POST['price']);
    $status = clean_input($_POST['status']);
    $desc = clean_input($_POST['desc']);
    $city = clean_input($_POST['city']);
    $district = clean_input($_POST['district']);
    $beds = clean_input($_POST['beds']);
    $baths = clean_input($_POST['baths']);
    $area = clean_input($_POST['area']);

    // Handle multiple image uploads
    $uploaded_images = [];

    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        // Limit the number of files to process
        $max_files = 10; // Process max 10 files
        $file_count = min(count($_FILES['images']['name']), $max_files);

        for ($i = 0; $i < $file_count; $i++) {
            // Check if file uploaded without errors
            if ($_FILES['images']['error'][$i] == 0) {
                $file = [
                    'name' => $_FILES['images']['name'][$i],
                    'tmp_name' => $_FILES['images']['tmp_name'][$i],
                    'error' => $_FILES['images']['error'][$i],
                    'size' => $_FILES['images']['size'][$i]
                ];

                $img_name = uploadImage($file);
                if (!empty($img_name)) {
                    $uploaded_images[] = $img_name;
                }
            }
        }
    }

    // Add property to database
    $property_id = add_property($conn, $user_id, $title, $type, $price, $status, $desc, $city, $district, $beds, $baths, $area);


    if ($property_id && !empty($uploaded_images)) {
        foreach ($uploaded_images as $img_name) {
            add_images($conn, $property_id, $img_name);
        }
    }

    // Redirect to my-properties page after successful insertion
    if ($property_id) {
        header('location: my-properties.php?success=1');
        exit;
    } else {
        $error_message = "Failed to add property. Please try again.";
    }
}






?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title data-i18n-doctitle="addproperty.title">SAMSAR · Add Property</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles/dashboard-shell.css" />
    <link rel="stylesheet" href="styles/samsar-transitions.css" />
    <link rel="stylesheet" href="styles/samsar-uploader.css" />
    <link rel="stylesheet" href="css/rtl.css" />
    <script src="js/translations.js"></script>
    <script src="js/language-switcher.js"></script>
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
                <div class="dashboard-group"><span data-i18n="dash.group.main">MAIN</span></div>
                <a class="dashboard-link" href="dashboard.php"><span class="ico">⌂</span><span data-i18n="dash.overview">Overview</span></a>
                <a class="dashboard-link" href="my-properties.php"><span class="ico">▤</span><span data-i18n="dash.myproperties">My Properties</span></a>
                <a class="dashboard-link active" href="add-property.php"><span class="ico">+</span><span data-i18n="dash.addproperty">Add Property</span></a>
                <div class="dashboard-group"><span data-i18n="dash.group.social">SOCIAL</span></div>
                <a class="dashboard-link" href="messages.php"><span class="ico">✉</span><span data-i18n="dash.messages">Messages</span> <em
                        class="dashboard-badge red" id="bdg-msg">0</em></a>
                <a class="dashboard-link" href="favorites.php"><span class="ico">♡</span><span data-i18n="dash.favorites">Favorites</span> <em
                        class="dashboard-badge red" id="bdg-fav">0</em></a>
                <a class="dashboard-link" href="following.php"><span class="ico">࿄</span><span data-i18n="dash.following">Following</span></a>
                <a class="dashboard-link" href="notifications.php"><span class="ico">⌖</span><span data-i18n="dash.notifications">Notifications</span> <em
                        class="dashboard-badge red" id="bdg-notif-2">0</em></a>
            </nav>
            <div class="dashboard-side-foot">
                <!-- profile name and role and profile image -->
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
                        if(isset($user['profile_image'])){
                                                   echo "<img src='" . htmlspecialchars($user['profile_image']) . "'" .
                            "alt='profile picture'/>"; 
                        }else{
                            echo "<img src='upload/property_images/1781142662___________________2026_04_16_182326.png'";
                        }



                        echo " <div><strong>" . htmlspecialchars($user['firstname']) . "</strong><span>" .
                            htmlspecialchars($user['role']) . "</span></div>";
                        ?>
                    </div>
                    <a class="dashboard-signout" href="logout.php" data-logout><span data-i18n="dash.signout">Sign out</span> →</a>
                </div>
        </aside>

        <main class="dashboard-main">
            <header class="dashboard-head">
                <div>
                    <h1 data-i18n="dash.addproperty">Add Property</h1>
                    <p data-i18n="addproperty.subtitle">Create a new listing for sale or rent.</p>
                </div>
            </header>

            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST"
                enctype="multipart/form-data" id="add-form" class="ap-form">
                <div class="content-card">
                    <h3 class="ap-section-title" data-i18n="addproperty.basicinfo">Basic information</h3>
                    <div class="ap-grid">
                        <label class="ap-field">
                            <span data-i18n="addproperty.title_field">Property Title *</span>
                            <input name="title" type="text" required placeholder="e.g. Villa Tazri — Palmeraie" />

                        </label>
                        <label class="ap-field">
                            <span data-i18n="addproperty.type_field">Property Type *</span>
                            <select name="type" required>
                                <option value="Villa" data-i18n="proptype.villa">Villa</option>
                                <option value="Riad" data-i18n="proptype.riad">Riad</option>
                                <option value="Apartment" data-i18n="proptype.apartment">Apartment</option>
                                <option value="Penthouse" data-i18n="proptype.penthouse">Penthouse</option>
                                <option value="Land" data-i18n="proptype.land">Land</option>
                                <option value="Commercial" data-i18n="proptype.commercial">Commercial</option>
                            </select>
                        </label>
                        <label class="ap-field">
                            <span data-i18n="addproperty.price_field">Price (MAD) *</span>
                            <input name="price" type="text" required placeholder="e.g. 12,400,000" />
                        </label>
                        <label class="ap-field">
                            <span data-i18n="propdetails.status">Status *</span>
                            <select name="status" required>
                                <option value="available" data-i18n="propstatus.available">For sale</option>
                                <option value="rented" data-i18n="propstatus.rented">For rent</option>
                                <option value="sold" data-i18n="propstatus.sold">Sold</option>
                                <option value="pending" data-i18n="propstatus.pending">Pending</option>
                            </select>
                        </label>
                    </div>
                    <label class="ap-field" style="margin-top:14px;display:block">
                        <span data-i18n="propdetails.description">Description</span>
                        <textarea name="desc" rows="4"
                            placeholder="Describe the property, location, and key features…" data-i18n-placeholder="addproperty.description.placeholder"></textarea>
                    </label>
                </div>

                <div class="content-card">
                    <h3 class="ap-section-title" data-i18n="properties.filters.city">Location</h3>
                    <div class="ap-grid">
                        <label class="ap-field">
                            <span data-i18n="addproperty.city_field">City *</span>
                            <select name="city" required>
                                <option>Casablanca</option>
                                <option>Marrakech</option>
                                <option>Rabat</option>
                                <option>Tangier</option>
                                <option>Fès</option>
                                <option>Essaouira</option>
                                <option>Agadir</option>
                            </select>
                        </label>
                        <label class="ap-field">
                            <span data-i18n="addproperty.district">District</span>
                            <input name="district" type="text" placeholder="e.g. Palmeraie" data-i18n-placeholder="addproperty.district.placeholder" />
                        </label>
                    </div>
                </div>

                <div class="content-card">
                    <h3 class="ap-section-title" data-i18n="addproperty.specifications">Specifications</h3>
                    <div class="ap-grid ap-grid-3">
                        <label class="ap-field">
                            <span data-i18n="propdetails.bedrooms">Bedrooms</span>
                            <input name="beds" type="number" min="0" value="3" />
                        </label>
                        <label class="ap-field">
                            <span data-i18n="propdetails.bathrooms">Bathrooms</span>
                            <input name="baths" type="number" min="0" value="2" />
                        </label>
                        <label class="ap-field">
                            <span data-i18n="addproperty.area_field">Area (m²)</span>
                            <input name="area" type="number" min="0" value="150" />
                        </label>
                    </div>
                </div>

                <div class="content-card">
                    <h3 class="ap-section-title" data-i18n="addproperty.upload_image">Upload Image</h3>
                    <div class="samsar-uploader" data-max-files="10"
                        data-max-bytes="<?php echo (int) samsar_max_upload_bytes(); ?>"
                        data-accept="image/jpeg,image/png,image/jpg,image/gif">
                        <input class="su-input" id="ap-images-input" type="file" name="images[]"
                            accept="image/jpeg,image/png,image/jpg,image/gif" multiple />
                        <label class="su-dropzone" for="ap-images-input" tabindex="0">
                            <span class="su-icon">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 16V4M12 4l-4.5 4.5M12 4l4.5 4.5" />
                                    <path d="M4 16v2.5A2.5 2.5 0 0 0 6.5 21h11a2.5 2.5 0 0 0 2.5-2.5V16" />
                                </svg>
                            </span>
                            <span class="su-title" data-i18n="uploader.choose_image">Choose an image</span>
                            <span class="su-sub" data-i18n="uploader.drag_drop">or drag and drop here</span>
                            <span class="su-meta">
                                <span class="su-chip">JPG</span>
                                <span class="su-chip">PNG</span>
                                <span class="su-chip">GIF</span>
                                <?php $max_upload_label = samsar_max_upload_label(); ?>
                                <?php if ($max_upload_label): ?>
                                <span class="su-chip">Max <?php echo htmlspecialchars($max_upload_label); ?></span>
                                <?php endif; ?>
                            </span>
                        </label>
                        <p class="su-warning" hidden></p>
                        <div class="su-previews"></div>
                        <p class="su-count" hidden></p>
                    </div>
                </div>

                <div class="ap-actions">
                    <a href="my-properties.php" class="btn btn-ghost" data-i18n="myproperties.cancel">Cancel</a>
                    <button type="submit" class="btn btn-primary" data-i18n="addproperty.publish">Publish Listing</button>
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

    .ap-grid-3 {
        grid-template-columns: repeat(3, 1fr)
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

    @media(max-width:780px) {

        .ap-grid,
        .ap-grid-4,
        .ap-grid-3 {
            grid-template-columns: 1fr
        }
    }
    </style>

    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/dashboard-shell.js"></script>
    <script src="scripts/dashboard.js"></script>
    <script src="scripts/samsar-uploader.js"></script>
    <script>
    (function() {
        const Store = window.SamsarStore;
        const form = document.getElementById('add-form');
    })();
    </script>
    <script src="scripts/dashboard-mobile-nav.js"></script>
</body>

</html>