<?php
session_start();
require "db/connect.php";
require __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/php/lang.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!$conn) {
  die("Database connection failed: " . mysqli_connect_error());
}
// function to sanitize user input
function clean_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

// function to validate signup form data
function validate_signup($first_name, $last_name, $email, $password, $role, $agency_name, $city)
{
  $errors = [];
  // Validate first name
  if ($first_name == "") {
    $errors[] = t("register.err.firstname_required");
  } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $first_name)) {
    $errors[] = t("register.err.firstname_letters");
  }
  // Validate last name

  if ($last_name == "") {
    $errors[] = t("register.err.lastname_required");
  } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $last_name)) {
    $errors[] = t("register.err.lastname_letters");
  }

  // Validate email
  if ($email == "") {
    $errors[] = t("register.err.email_required");
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = t("register.err.email_invalid");
  }

  // Validate password
  if ($password == "") {
    $errors[] = t("register.err.password_required");
  } elseif (strlen($password) < 8) {
    $errors[] = t("register.err.password_length");
  } elseif (!preg_match("/[A-Z]/", $password)) {
    $errors[] = t("register.err.password_upper");
  } elseif (!preg_match("/[a-z]/", $password)) {
    $errors[] = t("register.err.password_lower");
  } elseif (!preg_match("/[0-9]/", $password)) {
    $errors[] = t("register.err.password_number");
  }

  /* $allowed_roles = ["user", "agency"];
   if (empty($role) || !in_array($role, $allowed_roles)) {
     $errors[] = t("register.err.account_type");
   }*/

  if ($role == "agency") {
    if (empty($agency_name)) {
      $errors[] = t("register.err.agency_name_required");
    }
    if (empty($city)) {
      $errors[] = t("register.err.agency_city_required");
    }
  }

  return $errors;
}

function upload_profile_picture($file)
{

  // Ensure uploads/profile/ directory exists
  $target_dir = "uploads/profile/";
  if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
  }

  // Generate unique filename to prevent overwriting
  $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
  $unique_filename = time() . "_" . uniqid() . "." . $file_extension;
  $target_file = $target_dir . $unique_filename;

  // Validate file type and size
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
  // Check if file is an actual image
  $check = getimagesize($file["tmp_name"]);


  // Check if file is an actual image
  if ($check === false) {
    return ["error" => t("register.err.file_not_image")];
  }
  // Check file size (limit to 500KB)
  if ($file["size"] > 500000) {
    return ["error" => t("register.err.file_too_large")];
  }
  // Allow only certain file formats
  if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
    return ["error" => t("register.err.file_type")];
  }
  // Attempt to move uploaded file to target directory
  if (move_uploaded_file($file["tmp_name"], $target_file)) {
    return ["path" => $target_file];
  } else {
    return ["error" => t("register.err.file_upload")];
  }
}

function create_user($conn, $first_name, $last_name, $email, $hashed_password, $role, $agency_name, $phone, $city, $profile_picture, $verification_code)
{

  $stmt = $conn->prepare("INSERT INTO users 
        (firstname, lastname, email, password, role, agencyName, phone, city, profile_image, verification_code, is_verified) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");

  if (!$stmt) {
    die("Prepare failed: " . $conn->error);
  }

  $stmt->bind_param(
    "ssssssssss",
    $first_name,
    $last_name,
    $email,
    $hashed_password,
    $role,
    $agency_name,
    $phone,
    $city,
    $profile_picture,
    $verification_code
  );

  if ($stmt->execute()) {
    return true;
  } else {
    error_log("Execute error: " . $stmt->error);
    return false;
  }
}

// FIXED: Complete function to send verification email
function send_verification_email($email, $code, $first_name)
{
  try {
    $mail = new PHPMailer(true);

    // Server settings
    $mail->SMTPDebug = 0; // Set to 2 for debugging
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'walidfear238@gmail.com';
    $mail->Password = 'wtifpwqjfvotuhby';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('walidfear238@gmail.com', 'ma7laba');
    $mail->addAddress($email, $first_name);

    // Content
    $mail->isHTML(true);
    $mail->Subject = "Verify Your Account - samsar";
    $mail->AltBody = "Your verification code is: $code";
    $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: linear-gradient(135deg, #bb2626 0%, #ff7698 100%); padding: 30px; text-align: center;'>
                <h1 style='color: white; margin: 0;'>Welcome to samsar!</h1>
            </div>
            <div style='padding: 30px; background: #e5e5e6;'>
                <p style='font-size: 16px; color: #5a5a5a;'>Hello <strong>$first_name</strong>,</p>
                <p style='font-size: 16px; color: #4d2626;'>Thank you for signing up! Please use the verification code below to activate your account:</p>
                <div style='background: white; padding: 20px; text-align: center; margin: 20px 0; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                    <h1 style='color: #ea6666; font-size: 48px; letter-spacing: 10px; margin: 0;'>$code</h1>
                </div>
                <p style='font-size: 14px; color: #806b6b;'>This code will expire in 10 minutes.</p>
                <hr style='margin: 20px 0; border-color: #ebe5e5;'>
                <p style='font-size: 12px; color: #af9c9c;'>If you didn't create an account, please ignore this email.</p>
            </div>
        </div>
        ";

    $mail->send();
    return true;
  } catch (Exception $e) {
    error_log("Email could not be sent. Error: {$mail->ErrorInfo}");
    return false;
  }
}

// Check if email already exists
function email_exists($conn, $email)
{
  $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();
  $exists = $stmt->num_rows > 0;
  $stmt->close();
  return $exists;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_SESSION['errors'])) {
    unset($_SESSION['errors']);
  }

  // Get form data
  $first_name = isset($_POST["fir"]) ? clean_input($_POST["fir"]) : "";
  $last_name = isset($_POST["las"]) ? clean_input($_POST["las"]) : "";
  $email = isset($_POST["email"]) ? clean_input($_POST["email"]) : "";
  $password = isset($_POST["password"]) ? $_POST["password"] : "";
  $role = isset($_POST["acct"]) ? clean_input($_POST["acct"]) : "";
  $city = isset($_POST["city"]) ? clean_input($_POST["city"]) : "";
  $agency_name = isset($_POST["agency-name"]) ? clean_input($_POST["agency-name"]) : "";
  $phone = isset($_POST["phone"]) ? clean_input($_POST["phone"]) : "";

  // Generate verification code
  $verification_code = rand(100000, 999999);

  // Check if email already exists
  if (email_exists($conn, $email)) {
    $errors[] = t("register.err.email_exists");
  }

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
          $profile_picture_path,
          $verification_code
        )
      ) {
        // Send verification email
        if (send_verification_email($email, $verification_code, $first_name)) {
          // Store email in session for verification page
          $_SESSION['temp_email'] = $email;
          $_SESSION['temp_first_name'] = $first_name;
          $_SESSION['success_message'] = t("register.success.created");

          // Redirect to verification page
          header("Location: 20-verify-email.php");
          exit();
        } else {
          $errors[] = t("register.err.email_send_failed");
        }
      } else {
        $errors[] = t("register.err.create_failed");
      }
    }
  }

  //errors 
  if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
    echo '<div class="error-messages">';
    foreach ($_SESSION['errors'] as $error) {
      echo '<p class="error">' . htmlspecialchars($error) . '</p>';
    }
    echo '</div>';
    unset($_SESSION['errors']);
  }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title data-i18n-doctitle="register.title">SAMSAR · Create your account</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles/09-register.css" />
    <link rel="stylesheet" href="styles/samsar-transitions.css" />
    <link rel="stylesheet" href="css/rtl.css" />
    <script src="js/translations.js"></script>
    <script src="js/language-switcher.js"></script>
</head>

<body>
    <div class="page-trans"><span></span><span></span><span></span></div>
    <div class="cursor"></div>
    <div class="cursor-dot"></div>

    <div class="ambient"><span class="orb orb-1"></span><span class="orb orb-2"></span></div>

    <main class="shell">
        <aside class="photo-card">
            <div class="pc-top">
                <a href="index.php" class="brand">
                    <svg class="brand-mark" viewBox="0 0 1080 1080" fill="currentColor" aria-hidden="true">
                        <path
                            d="M734.34,464.81v-21.85c0-2.87-1.34-5.57-3.62-7.31l-152.36-116.23c-17.21-13.13-40.93-13.69-58.74-1.39l-170,117.41c-2.48,1.72-3.97,4.54-3.97,7.56v48.22c0,5.08,4.11,9.19,9.19,9.19h517.47c5.08,0,9.19,4.11,9.19,9.19v362.76c0,5.08-4.11,9.19-9.19,9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-189.17c0-5.08,4.11-9.19,9.19-9.19h128.79c5.08,0,9.19,4.11,9.19,9.19v42c0,5.08,4.11,9.19,9.19,9.19h370.3c5.08,0,9.19-4.11,9.19-9.19v-68.42c0-5.08-4.11-9.19-9.19-9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-272.61c0-3.02,1.48-5.85,3.97-7.56l223.99-154.69,97.47-67.32c17.82-12.3,41.53-11.74,58.74,1.39l94.18,71.86,57.49,43.85,143.55,109.51c2.28,1.74,3.62,4.44,3.62,7.31v94.68c0,5.08-4.11,9.19-9.19,9.19h-128.79c-5.08,0-9.19-4.11-9.19-9.19Z" />
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
                    <p data-i18n="register.quote">"In Morocco, the samsar's word has always been older than any contract."</p>
                    <span data-i18n="register.quote.author">— Salma El Idrissi, co-founder</span>
                </div>
                <span class="pc-caption" data-i18n="register.photo_caption">Riad in the medina of Marrakech</span>
            </div>
        </aside>

        <section class="form-side">
            <div class="form-inner">
                <header class="form-head">
                    <h1><span data-i18n="register.title1">Create your account.</span><br /><span data-i18n="register.title2">Open every </span><em data-i18n="register.title2_em">door.</em></h1>
                    <div class="head-actions">
                        <a href="index.php" class="circle-btn" aria-label="Back" data-i18n-aria-label="common.back">←</a>
                        <span class="head-text"><span data-i18n="register.already_member">Already a member?</span> <a href="08-login.php" class="head-link" data-i18n="register.signin">Sign
                                in</a></span>
                    </div>
                </header>

                <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                <div
                    style="color: red; padding: 15px; background: #ffeeee; margin-bottom: 20px; border-radius: 8px; border-left: 4px solid red;">
                    <strong data-i18n="register.fix_errors">Please fix the following errors:</strong>
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
                    <div class="type-toggle" role="radiogroup" aria-label="Account type" data-i18n-aria-label="register.account_type">
                        <label class="tt-opt active" data-type="user">
                            <input type="radio" name="acct" value="user" checked />
                            <span class="tt-ico">⌂</span>
                            <span class="tt-text"><strong data-i18n="register.personal_user">Personal User</strong><em data-i18n="register.personal_user.desc">Browse & message sellers</em></span>
                        </label>
                        <label class="tt-opt" data-type="agency">
                            <input type="radio" name="acct" value="agency" />
                            <span class="tt-ico">▤</span>
                            <span class="tt-text"><strong data-i18n="register.agency">Real Estate Agency</strong><em data-i18n="register.agency.desc">List properties & build a
                                    brand</em></span>
                        </label>
                    </div>

                    <div class="row">
                        <label class="floating">
                            <input name="fir" type="text" required placeholder=" " />
                            <span data-i18n="register.firstname">First name</span>
                        </label>
                        <label class="floating">
                            <input name="las" type="text" required placeholder=" " />
                            <span data-i18n="register.lastname">Last name</span>
                        </label>
                    </div>

                    <div class="agency-fields" id="agency-fields" hidden>
                        <div class="row">
                            <label class="floating">
                                <input name="agency-name" type="text" placeholder=" " />
                                <span data-i18n="register.agencyname">Agency name</span>
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
                                <span data-i18n="modal.city">City</span>
                            </label>
                        </div>
                    </div>

                    <label class="floating">
                        <input name="email" type="email" required placeholder=" " />
                        <span data-i18n="login.email">Email address</span>
                    </label>

                    <label class="floating">
                        <input name="phone" type="tel" placeholder=" " />
                        <span data-i18n="register.phone_optional">Phone (optional)</span>
                    </label>

                    <label class="floating">
                        <input name="profile-picture" type="file" accept="image/*" />
                        <span data-i18n="register.upload_photo">Upload a profile picture</span>
                    </label>

                    <label class="floating">
                        <input name="password" type="password" id="pw" required placeholder=" " />
                        <span data-i18n="login.password">Password</span>
                        <button type="button" class="eye" id="pw-toggle" aria-label="Show password" data-i18n-aria-label="login.show_password">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </label>
                    <div class="meter"><span></span></div>

                    <label class="check terms"><input name="terms" type="checkbox" required /><span><span data-i18n="register.agree">I agree to the</span>
                            <a href="#" data-i18n="login.terms">Terms of Service</a> <span data-i18n="login.legal2">and</span> <a href="#" data-i18n="login.privacy">Privacy Policy</a></span></label>

                    <button class="pill-btn" type="submit">
                        <span data-i18n="register.submit">Create free account</span>
                        <span class="pill-arrow"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M13 6l6 6-6 6" />
                            </svg></span>
                    </button>

                    <p class="legal" data-i18n="register.legal">No credit card. No spam. Cancel anytime — your data is never sold.</p>
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