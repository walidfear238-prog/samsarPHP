<?php

session_start();
// check if the user loged in
require "db/connect.php";
if (!isset($_SESSION['user_id'])) {
    header('location: index.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="refresh" content="0; url=09-register.php" />
    <title>SAMSAR · Join</title>
    <link rel="stylesheet" href="styles/10-register-choose.css" />
</head>

<body>
    <script src="scripts/10-register-choose.js"></script>
    <noscript>
        <meta http-equiv="refresh" content="0; url=09-register.php" />
    </noscript>
    <p style="font-family:Inter,system-ui,sans-serif;padding:40px;text-align:center;color:#4A4A4A">Redirecting to <a
            href="09-register.php" style="color:#C72C41">SAMSAR signup →</a></p>
</body>

</html>