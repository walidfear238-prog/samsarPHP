<?php


session_start();
require "db/connect.php";
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
    <title>SAMSAR · Add Property</title>
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
                <svg class="dashboard-brand-mark" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3z" />
                </svg>
                <span class="dashboard-brand-word">SAMSAR</span>
            </a>
            <nav class="dashboard-nav">
                <div class="dashboard-group">MAIN</div>
                <a class="dashboard-link" href="dashboard.php"><span class="ico">⌂</span>Overview</a>
                <a class="dashboard-link" href="my-properties.php"><span class="ico">▤</span>My Properties</a>
                <a class="dashboard-link active" href="add-property.php"><span class="ico">+</span>Add Property</a>
                <div class="dashboard-group">SOCIAL</div>
                <a class="dashboard-link" href="messages.php"><span class="ico">✉</span>Messages <em
                        class="dashboard-badge red" id="bdg-msg">0</em></a>
                <a class="dashboard-link" href="favorites.php"><span class="ico">♡</span>Favorites <em
                        class="dashboard-badge red" id="bdg-fav">0</em></a>
                <a class="dashboard-link" href="following.php"><span class="ico">࿄</span>Following</a>
                <a class="dashboard-link" href="notifications.php"><span class="ico">⌖</span>Notifications <em
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
                        echo "<img src='" . htmlspecialchars($user['profile_image']) . "'" .
                            "alt='profile picture'/>";


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
                    <h1>Add Property</h1>
                    <p>Create a new listing for sale or rent.</p>
                </div>
            </header>

            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST"
                enctype="multipart/form-data" id="add-form" class="ap-form">
                <div class="content-card">
                    <h3 class="ap-section-title">Basic information</h3>
                    <div class="ap-grid">
                        <label class="ap-field">
                            <span>Property Title *</span>
                            <input name="title" type="text" required placeholder="e.g. Villa Tazri — Palmeraie" />

                        </label>
                        <label class="ap-field">
                            <span>Property Type *</span>
                            <select name="type" required>
                                <option value="Villa">Villa</option>
                                <option value="Riad">Riad</option>
                                <option value="Apartment">Apartment</option>
                                <option value="Penthouse">Penthouse</option>
                                <option value="Land">Land</option>
                                <option value="Commercial">Commercial</option>
                            </select>
                        </label>
                        <label class="ap-field">
                            <span>Price (MAD) *</span>
                            <input name="price" type="text" required placeholder="e.g. 12,400,000" />
                        </label>
                        <label class="ap-field">
                            <span>Status *</span>
                            <select name="status" required>
                                <option value="available">For sale</option>
                                <option value="rented">For rent</option>
                                <option value="sold">Sold</option>
                                <option value="pending">Pending</option>
                            </select>
                        </label>
                    </div>
                    <label class="ap-field" style="margin-top:14px;display:block">
                        <span>Description</span>
                        <textarea name="desc" rows="4"
                            placeholder="Describe the property, location, and key features…"></textarea>
                    </label>
                </div>

                <div class="content-card">
                    <h3 class="ap-section-title">Location</h3>
                    <div class="ap-grid">
                        <label class="ap-field">
                            <span>City *</span>
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
                            <span>District</span>
                            <input name="district" type="text" placeholder="e.g. Palmeraie" />
                        </label>
                    </div>
                </div>

                <div class="content-card">
                    <h3 class="ap-section-title">Specifications</h3>
                    <div class="ap-grid ap-grid-4">
                        <label class="ap-field">
                            <span>Bedrooms</span>
                            <input name="beds" type="number" min="0" value="3" />
                        </label>
                        <label class="ap-field">
                            <span>Bathrooms</span>
                            <input name="baths" type="number" min="0" value="2" />
                        </label>
                        <label class="ap-field">
                            <span>Area (m²)</span>
                            <input name="area" type="number" min="0" value="150" />
                        </label>
                        <label class="ap-field">
                            <span>Upload Image</span>
                            <input name="images[]" type="file" accept="image/*" multiple />
                        </label>
                    </div>
                </div>

                <div class="ap-actions">
                    <a href="my-properties.php" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Publish Listing</button>
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
    (function() {
        const Store = window.SamsarStore;
        const form = document.getElementById('add-form');
    })();
    </script>
</body>

</html>