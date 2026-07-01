<?php
session_start();
include "db/connect.php";

// function for helping with input sanitization
// function to clean the input 
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
// code to handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = test_input($_POST["email"]);
    $password = test_input($_POST["pw"]);
    // query to check if the user exists in the database usind oop
    $stmt = $conn->prepare("SELECT id, firstname, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // verify the password
        if (password_verify($password, $row["password"])) {
            // password is correct, start a new session
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["user_name"] = $row["firstname"];
            $_SESSION["user_email"] = $row["email"];
            // redirect to the dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // password is incorrect
            $error = "Invalid email or password.";
        }
    } else {
        // user not found
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SAMSAR · Sign in</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles/08-login.css" />
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
                    <svg class="brand-mark" viewBox="0 0 1080 1080" fill="currentColor" aria-hidden="true">
                        <path
                            d="M734.34,464.81v-21.85c0-2.87-1.34-5.57-3.62-7.31l-152.36-116.23c-17.21-13.13-40.93-13.69-58.74-1.39l-170,117.41c-2.48,1.72-3.97,4.54-3.97,7.56v48.22c0,5.08,4.11,9.19,9.19,9.19h517.47c5.08,0,9.19,4.11,9.19,9.19v362.76c0,5.08-4.11,9.19-9.19,9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-189.17c0-5.08,4.11-9.19,9.19-9.19h128.79c5.08,0,9.19,4.11,9.19,9.19v42c0,5.08,4.11,9.19,9.19,9.19h370.3c5.08,0,9.19-4.11,9.19-9.19v-68.42c0-5.08-4.11-9.19-9.19-9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-272.61c0-3.02,1.48-5.85,3.97-7.56l223.99-154.69,97.47-67.32c17.82-12.3,41.53-11.74,58.74,1.39l94.18,71.86,57.49,43.85,143.55,109.51c2.28,1.74,3.62,4.44,3.62,7.31v94.68c0,5.08-4.11,9.19-9.19,9.19h-128.79c-5.08,0-9.19-4.11-9.19-9.19Z" />
                    </svg>
                    <span class="brand-word">SAMSAR</span>
                </a>
            </div>
            <div class="pc-img">
                <img src="https://images.unsplash.com/photo-1539020140153-e479b8c22e70?auto=format&fit=crop&w=1400&q=85"
                    alt="Marrakech medina rooftops at golden hour" />
            </div>
            <div class="pc-foot">
                <span class="pc-caption">Marrakech medina · captured at sunrise</span>
            </div>
        </aside>

        <!-- Form (right) -->
        <section class="form-side">
            <div class="form-inner">
                <header class="form-head">
                    <h1>Welcome back to<br /><em>SAMSAR.</em></h1>
                    <div class="head-actions">
                        <a href="index.php" class="circle-btn" aria-label="Back">←</a>
                        <span class="head-text">New here? <a href="09-register.php" class="head-link"
                                data-transition="clip-circle-corner" data-duration="slow">Join free</a></span>
                    </div>
                </header>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="form">
                    <label class="floating">
                        <input name="email" type="email" id="email" required />
                        <span>Email address</span>
                    </label>

                    <label class="floating">
                        <input name="pw" type="password" id="pw" required />
                        <span>Password</span>
                        <button type="button" class="eye" id="pw-toggle" aria-label="Show password">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </label>

                    <div class="form-aux">
                        <label class="check"><input name="keep_signed_in" type="checkbox" checked /><span>Keep me signed
                                in</span></label>
                        <a href="#" class="forgot">Forgot password?</a>
                    </div>

                    <button class="pill-btn" type="submit">
                        <span>Sign in to SAMSAR</span>
                        <span class="pill-arrow"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M13 6l6 6-6 6" />
                            </svg></span>
                    </button>

                    <div class="divider"><span>or continue with</span></div>
                    <div class="socials">
                        <button type="button" class="social-btn"><span class="s-ico">G</span> Google</button>
                        <button type="button" class="social-btn"><span class="s-ico"></span> Apple</button>
                        <button type="button" class="social-btn"><span class="s-ico">f</span> Facebook</button>
                    </div>

                    <p class="legal">By signing in, you agree to SAMSAR's <a href="#">Terms of Service</a>, <a
                            href="#">Privacy
                            Policy</a> and <a href="#">Data Usage Properties</a>.</p>
                </form>
            </div>
        </section>
    </main>

    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/08-login.js"></script>
    <script>
    (function() {
        if (window.SamsarTransition && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            var old = document.querySelector('.page-trans');
            if (old) old.remove();
            setTimeout(function() {
                SamsarTransition.play('zoom-blur', 'slow');
            }, 50);
        }
    })();
    </script>
</body>

</html>