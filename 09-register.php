<?php
session_start();
include "db/connect.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'path/to/PHPMailer/src/Exception.php';
require 'path/to/PHPMailer/src/PHPMailer.php';
require 'path/to/PHPMailer/src/SMTP.php';

// function for helping with input 
// function to clean the input
function test_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
//funvction to check te input 
function validate_input($data)
{
  $errors = [];
  //chech if the input is empty
  if (empty($data["fir"])) {
    $errors[] = "First name is required.";
  }
  if (empty($data["las"])) {
    $errors[] = "Last name is required.";
  }
  // check if the email is empty and valid
  if (empty($data["email"])) {
    $errors[] = "Email is required.";
  } elseif (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
  }
  // check if the password is empty and has at least 8 characters
  if (empty($data["password"])) {
    $errors[] = "Password is required.";
  } elseif (strlen($data["password"]) < 8) {
    $errors[] = "Password must be at least 8 characters long.";
  } elseif (!preg_match("/[A-Z]/", $data["password"])) {
    $errors[] = "Password must contain at least one uppercase letter.";
  } elseif (!preg_match("/[a-z]/", $data["password"])) {
    $errors[] = "Password must contain at least one lowercase letter.";
  } elseif (!preg_match("/[0-9]/", $data["password"])) {
    $errors[] = "Password must contain at least one number.";
  } elseif (!preg_match("/[!@#$%^&*]/", $data["password"])) {
    $errors[] = "Password must contain at least one special character.";
  }
  return $errors;
}
//function to handle the file upload
function handle_file_upload($file)
{
  $target_dir = "uploads/";
  $target_file = $target_dir . basename($file["name"]);
  $uploadOk = 1;
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

  // Check if image file is a actual image or fake image
  if (isset($_POST["submit"])) {
    $check = getimagesize($file["tmp_name"]);
    if ($check !== false) {
      echo "File is an image - " . $check["mime"] . ".";
      $uploadOk = 1;
    } else {
      echo "File is not an image.";
      $uploadOk = 0;
    }
  }

  // Check if file already exists
  if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
  }

  // Check file size
  if ($file["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
  }

  // Allow certain file formats
  if (
    $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif"
  ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
  }

  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
  } else {
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
      echo "The file " . basename($file["name"]) . " has been uploaded.";
    } else {
      echo "Sorry, there was an error uploading your file.";
    }
  }
}
// code to handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $data = [
    "fir" => test_input($_POST["fir"]),
    "las" => test_input($_POST["las"]),
    "email" => test_input($_POST["email"]),
    "password" => test_input($_POST["password"]),
    "profile-picture" => isset($_FILES["profile-picture"]) ? handle_file_upload($_FILES["profile-picture"]) : ""
  ];

  $errors = validate_input($data);
  if (empty($errors)) {
    // hash the password
    $hashed_password = password_hash($data["password"], PASSWORD_DEFAULT);
    // insert the user into the database using oop
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password , profile_picture) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $data["fir"], $data["las"], $data["email"], $hashed_password, $data["profile-picture"]);
    if ($stmt->execute()) {
      // registration successful, redirect to login page
      header("Location: 20-verify-email.php");
      exit();
    } else {
      // registration failed
      $error = "Error: " . $stmt->error;
    }
  } else {
    // validation errors
    $error = implode("<br>", $errors);
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

                <!-- User / Agency toggle -->
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

                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST"
                    enctype="multipart/form-data" id="reg-form" class="form">
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
                                <select name="city" required>
                                    <option disabled selected hidden></option>
                                    <option>Marrakech</option>
                                    <option>Casablanca</option>
                                    <option>Tangier</option>
                                    <option>Rabat</option>
                                    <option>Fès</option>
                                    <option>Essaouira</option>
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
                    <!--upload a profile picture-->
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
                            <a href="#">Terms
                                of
                                Service</a> and <a href="#">Privacy Policy</a></span></label>

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