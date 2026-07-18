<?php
/* ============================================================
   SAMSAR — php/mail-config.php  (NEW FILE)
   Central place for the contact form's SMTP + destination
   settings. Edit the four values below with your own details.

   IMPORTANT:
   - Do NOT commit this file with real credentials to a public
     git repository. Add "php/mail-config.php" to your .gitignore
     (a starter .gitignore is included in this patch).
   - SMTP_USERNAME must be a real Gmail address you control.
   - SMTP_PASSWORD must be a 16-character Gmail "App Password",
     NOT your normal Gmail login password. Regular passwords no
     longer work with Gmail SMTP unless App Passwords are used.
     Generate one at: https://myaccount.google.com/apppasswords
     (requires 2-Step Verification to be enabled on the account).
   ============================================================ */

// The Gmail account PHPMailer logs into and sends FROM.
// Gmail requires the "From" address to match the authenticated
// account, so this is also the address that will appear as sender.
define('SMTP_USERNAME', 'walidfear238@gmail.com');

// The 16-character Gmail App Password (no spaces), NOT your normal password.
define('SMTP_PASSWORD', 'wtifpwqjfvotuhby');

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587); // STARTTLS port. Use 465 with SMTPS if you prefer implicit TLS.

// Display name shown as the sender in the recipient's inbox.
define('MAIL_FROM_NAME', 'SAMSAR Website');

// Where contact form messages are delivered.
define('MAIL_TO_ADDRESS', 'walidfear238@gmail.com');
define('MAIL_TO_NAME', 'SAMSAR Team');