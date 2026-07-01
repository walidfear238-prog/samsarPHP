<?php


// nO whitespace or ANYTHING before this opening PHP tag!
require 'db/connect.php';

// handle verification logic BEFORE any HTML output
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify'])) {
    $user_code = trim($_POST['code[0]'] . $_POST['code[1]'] . $_POST['code[2]'] . $_POST['code[3]'] . $_POST['code[4]'] . $_POST['code[5]']);

    // validate numeric input
    if (!is_numeric($user_code)) {
        $error_message = "Please enter a valid numeric code.";
    } else {
        $numeric_code = (int) $user_code;

        // check what code is being submitted
        // echo "Submitted code: " . $numeric_code; // Uncomment for debugging

        $stmt = $conn->prepare("SELECT * FROM users WHERE verification_code = ?");
        $stmt->bind_param("i", $numeric_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // get the user ID to update by ID instead of verification_code
            $user = $result->fetch_assoc();
            $user_id = $user['id'];

            $stmt2 = $conn->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
            $stmt2->bind_param("i", $user_id);
            $stmt2->execute();

            // redirect AFTER all processing, before any HTML output
            header('Location: 08-login.php');
            exit(); // always call exit after header redirect
        } else {
            $error_message = "Wrong verification code!";
        }
    }
}




?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SAMSAR · Verify your email</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles/20-verify-email.css" />
    <link rel="stylesheet" href="styles/samsar-transitions.css" />
</head>

<body>
    <div class="cursor"></div>
    <div class="cursor-dot"></div>
    <div class="ambient"><span class="orb orb-1"></span><span class="orb orb-2"></span></div>

    <main class="shell">
        <a href="08-login.php" class="brand">
            <svg class="brand-mark" viewBox="0 0 1080 1080" fill="currentColor" aria-hidden="true">
                <path
                    d="M734.34,464.81v-21.85c0-2.87-1.34-5.57-3.62-7.31l-152.36-116.23c-17.21-13.13-40.93-13.69-58.74-1.39l-170,117.41c-2.48,1.72-3.97,4.54-3.97,7.56v48.22c0,5.08,4.11,9.19,9.19,9.19h517.47c5.08,0,9.19,4.11,9.19,9.19v362.76c0,5.08-4.11,9.19-9.19,9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-189.17c0-5.08,4.11-9.19,9.19-9.19h128.79c5.08,0,9.19,4.11,9.19,9.19v42c0,5.08,4.11,9.19,9.19,9.19h370.3c5.08,0,9.19-4.11,9.19-9.19v-68.42c0-5.08-4.11-9.19-9.19-9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-272.61c0-3.02,1.48-5.85,3.97-7.56l223.99-154.69,97.47-67.32c17.82-12.3,41.53-11.74,58.74,1.39l94.18,71.86,57.49,43.85,143.55,109.51c2.28,1.74,3.62,4.44,3.62,7.31v94.68c0,5.08-4.11,9.19-9.19,9.19h-128.79c-5.08,0-9.19-4.11-9.19-9.19Z" />
            </svg>
            <span class="brand-word">SAMSAR</span>
        </a>

        <div class="card">
            <div class="icon-ring">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                    <rect x="2" y="4" width="20" height="16" rx="3" />
                    <path d="M22 4 L12 13 L2 4" />
                </svg>
            </div>

            <h1>Check your <em>inbox.</em></h1>
            <p>We sent a verification link to</p>
            <span class="email-display" id="email-display">yassine@email.com</span>
            <p class="sub">Click the link in the email to activate your SAMSAR account. The link expires in 24 hours.
            </p>

            <div class="code-input" aria-label="Verification code">
                <input name="code[]" type="text" maxlength="1" data-idx="0" autofocus />
                <input name="code[]" type="text" maxlength="1" data-idx="1" />
                <input name="code[]" type="text" maxlength="1" data-idx="2" />
                <input name="code[]" type="text" maxlength="1" data-idx="3" />
                <input name="code[]" type="text" maxlength="1" data-idx="4" />
                <input name="code[]" type="text" maxlength="1" data-idx="5" />
            </div>
            <p class="code-hint">Or enter the 6-digit code from the email</p>

            <button class="pill-btn" id="verify-btn">
                <span>Verify & continue</span>
                <span class="pill-arrow"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M13 6l6 6-6 6" />
                    </svg></span>
            </button>

            <div class="footer-actions">
                <button class="link-btn" id="resend">Didn't receive it? <strong>Resend email</strong></button>
                <span class="divider-dot">·</span>
                <a href="09-register.php" class="link-btn">Wrong email? <strong>Go back</strong></a>
            </div>

            <div class="status" id="status" hidden>
                <span class="status-ico">✓</span>
                <span class="status-text">Verified — redirecting to your dashboard…</span>
            </div>
        </div>

        <p class="legal">Need help? <a href="07-contact.php">Contact support</a></p>
    </main>

    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/20-verify-email.js"></script>
</body>

</html>