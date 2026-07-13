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
    <title data-i18n-doctitle="registerchoose.title">SAMSAR · Join</title>
    <link rel="stylesheet" href="styles/10-register-choose.css" />
    <link rel="stylesheet" href="css/rtl.css" />
    <script src="js/translations.js"></script>
    <script src="js/language-switcher.js"></script>
</head>

<body>
    <script src="scripts/10-register-choose.js"></script>
    <noscript>
        <meta http-equiv="refresh" content="0; url=09-register.php" />
    </noscript>
    <p style="font-family:Inter,system-ui,sans-serif;padding:40px;text-align:center;color:#4A4A4A"><span data-i18n="registerchoose.redirecting">Redirecting to</span> <a
            href="09-register.php" style="color:#C72C41" data-i18n="registerchoose.link">SAMSAR signup →</a></p>
</body>

</html>