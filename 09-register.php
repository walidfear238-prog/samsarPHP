<?php
session_start();
require "db/connect.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!$conn) {
  die("Database connection failed: " . mysqli_connect_error());
}

function clean_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function validate_signup($first_name, $last_name, $email, $password, $role, $agency_name, $city)
{
  $errors = [];

  if ($first_name == "") {
    $errors[] = "First name is required.";
  } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $first_name)) {
    $errors[] = "Only letters and white space allowed in first name.";
  }

  if ($last_name == "") {
    $errors[] = "Last name is required.";
  } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $last_name)) {
    $errors[] = "Only letters and white space allowed in last name.";
  }

  if ($email == "") {
    $errors[] = "Email is required.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
  }

  if ($password == "") {
    $errors[] = "Password is required.";
  } elseif (strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters long.";
  }

  // ✅ FIX: Validate role properly
  $allowed_roles = ["user", "agency"];
  if (empty($role) || !in_array($role, $allowed_roles)) {
    $errors[] = "Please select a valid account type.";
  }

  if ($role == "agency") {
    if (empty($agency_name)) {
      $errors[] = "Agency name is required for agency accounts.";
    }
    if (empty($city)) {
      $errors[] = "City is required for agency accounts.";
    }
  }

  return $errors;
}

function upload_profile_picture($file)
{
  $target_dir = "uploads/profile/";
  if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
  }

  $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
  $unique_filename = time() . "_" . uniqid() . "." . $file_extension;
  $target_file = $target_dir . $unique_filename;

  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
  $check = getimagesize($file["tmp_name"]);

  if ($check === false) {
    return ["error" => "File is not an image."];
  }
  if ($file["size"] > 500000) {
    return ["error" => "Sorry, your file is too large."];
  }
  if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
    return ["error" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."];
  }
  if (move_uploaded_file($file["tmp_name"], $target_file)) {
    return ["path" => $target_file];
  } else {
    return ["error" => "Sorry, there was an error uploading your file."];
  }
}

function create_user($conn, $first_name, $last_name, $email, $hashed_password, $role, $agency_name, $phone, $city, $profile_picture)
{
  $stmt = $conn->prepare("INSERT INTO users 
    (firstname, lastname, email, password, role, agencyName, phone, city, profile_image) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

  if (!$stmt) {
    die("Prepare failed: " . $conn->error);
  }

  $stmt->bind_param(
    "sssssssss",
    $first_name,
    $last_name,
    $email,
    $hashed_password,
    $role,
    $agency_name,
    $phone,
    $city,
    $profile_picture
  );

  if ($stmt->execute()) {
    return true;
  } else {
    error_log("Execute error: " . $stmt->error);
    return false;
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  if (isset($_SESSION['errors'])) {
    unset($_SESSION['errors']);
  }

  // ✅ FIX: Use null coalescing operator for ALL post fields
  $first_name = isset($_POST["fir"]) ? clean_input($_POST["fir"]) : "";
  $last_name = isset($_POST["las"]) ? clean_input($_POST["las"]) : "";
  $email = isset($_POST["email"]) ? clean_input($_POST["email"]) : "";
  $password = isset($_POST["password"]) ? $_POST["password"] : "";
  $role = isset($_POST["acct"]) ? clean_input($_POST["acct"]) : "";
  $city = isset($_POST["city"]) ? clean_input($_POST["city"]) : "";
  $agency_name = isset($_POST["agency-name"]) ? clean_input($_POST["agency-name"]) : "";
  $phone = isset($_POST["phone"]) ? clean_input($_POST["phone"]) : "";

  // ✅ DEBUG: Uncomment to inspect what's being submitted
  // echo "<pre>"; print_r($_POST); echo "</pre>"; die();

  $errors = validate_signup($first_name, $last_name, $email, $password, $role, $agency_name, $city);

  if (empty($errors)) {
    $profile_picture_path = null;

    if (isset($_FILES["profile-picture"]) && $_FILES["profile-picture"]["error"] == 0) {
      $upload_result = upload_profile_picture($_FILES["profile-picture"]);
      if (isset($upload_result["error"])) {
        $errors[] = $upload_result["error"];
      } else {
        $profile_picture_path = $upload_result["path"];
      }
    }

    if (empty($errors)) {
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      if (
        create_user(
          $conn,
          $first_name,
          $last_name,
          $email,
          $hashed_password,
          $role,
          $agency_name,
          $phone,
          $city,
          $profile_picture_path
        )
      ) {
        $_SESSION["success_message"] = "Account created successfully! Please log in.";
        header("Location: 08-login.php");
        exit();
      } else {
        $errors[] = "Error creating account. Please try again.";
      }
    }
  }

  if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    // ✅ Redirect back to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SAMSAR · Create your account</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles/09-register.css" />
    <link rel="stylesheet" href="styles/samsar-transitions.css" />
</head>

<body>
    <div class="page-trans"><span></span><span></span><span></span></div>
    <div class="cursor"></div>
    <div class="cursor-dot"></div>

    <div class="ambient"><span class="orb orb-1"></span><span class="orb orb-2"></span></div>

    <main class="shell">
        <!-- Photo card (left) -->
        <aside class="photo-card">
            <div class="pc-top">
                <a href="index.php" class="brand">
                    <svg class="brand-mark" viewBox="0 0 100 100">
                        <path
                            d="M22 44 L50 18 L78 44 L78 86 Q78 90 74 90 L26 90 Q22 90 22 86 Z M38 38 L62 38 L62 50 L38 50 Z M38 60 L62 60 L62 72 L38 72 Z"
                            fill-rule="evenodd" />
                    </svg>
                    <span class="brand-word">SAMSAR</span>
                </a>
            </div>
            <div class="pc-img">
                <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1400&q=85"
                    alt="Sunlit Moroccan riad courtyard with arches" />
            </div>
            <div class="pc-foot">
                <div class="pc-quote">
                    <p>"In Morocco, the samsar's word has always been older than any contract."</p>
                    <span>— Salma El Idrissi, co-founder</span>
                </div>
                <span class="pc-caption">Riad in the medina of Marrakech</span>
            </div>
        </aside>

        <!-- Form (right) -->
        <section class="form-side">
            <div class="form-inner">
                <header class="form-head">
                    <h1>Create your account.<br />Open every <em>door.</em></h1>
                    <div class="head-actions">
                        <a href="index.php" class="circle-btn" aria-label="Back">←</a>
                        <span class="head-text">Already a member? <a href="08-login.php" class="head-link">Sign
                                in</a></span>
                    </div>
                </header>

                <!-- Display errors -->
                <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                <div
                    style="color: red; padding: 15px; background: #ffeeee; margin-bottom: 20px; border-radius: 8px; border-left: 4px solid red;">
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin: 10px 0 0 0;">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>


                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST"
                    enctype="multipart/form-data" id="reg-form" class="form">
                    <!-- Account Type Toggle -->
                    <div class="type-toggle" role="radiogroup" aria-label="Account type">
                        <label class="tt-opt active" data-type="user">
                            <input type="radio" name="acct" value="user" checked />
                            <span class="tt-ico">⌂</span>
                            <span class="tt-text"><strong>Personal User</strong><em>Browse & message sellers</em></span>
                        </label>
                        <label class="tt-opt" data-type="agency">
                            <input type="radio" name="acct" value="agency" />
                            <span class="tt-ico">▤</span>
                            <span class="tt-text"><strong>Real Estate Agency</strong><em>List properties & build a
                                    brand</em></span>
                        </label>
                    </div>


                    <div class="row">
                        <label class="floating">
                            <input name="fir" type="text" required placeholder=" " />
                            <span>First name</span>
                        </label>
                        <label class="floating">
                            <input name="las" type="text" required placeholder=" " />
                            <span>Last name</span>
                        </label>
                    </div>

                    <div class="agency-fields" id="agency-fields" hidden>
                        <div class="row">
                            <label class="floating">
                                <input name="agency-name" type="text" placeholder=" " />
                                <span>Agency name</span>
                            </label>
                            <label class="floating">
                                <select name="city">
                                    <option value="" disabled selected hidden></option>
                                    <option value="marrakech">Marrakech</option>
                                    <option value="casablanca">Casablanca</option>
                                    <option value="tangier">Tangier</option>
                                    <option value="rabat">Rabat</option>
                                    <option value="fès">Fès</option>
                                    <option value="essaouira">Essaouira</option>
                                </select>
                                <span>City</span>
                            </label>
                        </div>
                    </div>

                    <label class="floating">
                        <input name="email" type="email" required placeholder=" " />
                        <span>Email address</span>
                    </label>

                    <label class="floating">
                        <input name="phone" type="tel" placeholder=" " />
                        <span>Phone (optional)</span>
                    </label>

                    <label class="floating">
                        <input name="profile-picture" type="file" accept="image/*" />
                        <span>Upload a profile picture</span>
                    </label>

                    <label class="floating">
                        <input name="password" type="password" id="pw" required placeholder=" " />
                        <span>Password</span>
                        <button type="button" class="eye" id="pw-toggle" aria-label="Show password">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </label>
                    <div class="meter"><span></span></div>

                    <label class="check terms"><input name="terms" type="checkbox" required /><span>I agree to the
                            <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span></label>

                    <button class="pill-btn" type="submit">
                        <span>Create free account</span>
                        <span class="pill-arrow"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M13 6l6 6-6 6" />
                            </svg></span>
                    </button>

                    <p class="legal">No credit card. No spam. Cancel anytime — your data is never sold.</p>
                </form>
            </div>
        </section>
    </main>

    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/09-register.js"></script>
    <script>
    (function() {
        if (window.SamsarTransition && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            var old = document.querySelector('.page-trans');
            if (old) old.remove();
            setTimeout(function() {
                SamsarTransition.play('clip-circle-corner', 'slow')
            }, 50);
        }
    })();
    </script>
</body>

</html>