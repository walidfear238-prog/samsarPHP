<?php
/* ============================================================
   SAMSAR — api/send-contact.php  (NEW FILE)
   Handles submissions from the 07-contact.php form.

   - Validates + sanitizes all fields
   - Stores the message in the `contact_messages` table
   - Emails a notification to MAIL_TO_ADDRESS via Gmail SMTP
   - Always responds with JSON, so scripts/07-contact.js can
     show a success or error state.

   Expects a JSON POST body:
   { "name": "...", "email": "...", "phone": "...", "topic": "...",
     "message": "...", "website": "" }

   "website" is an optional honeypot field (see 07-contact.php).
   Leave it out of your payload entirely if you don't want the
   honeypot check — it's simply ignored when absent.
   ============================================================ */

header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../php/mail-config.php';
require __DIR__ . '/../db/connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

function respond(int $httpCode, array $payload): void
{
    http_response_code($httpCode);
    echo json_encode($payload);
    exit;
}

// Only POST is allowed.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['success' => false, 'message' => 'Method not allowed.']);
}

// ---------- 1. Read + decode the request body ----------
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    respond(400, ['success' => false, 'message' => 'Invalid request payload.']);
}

// ---------- 2. Honeypot check (silently "succeed" for bots) ----------
if (!empty($data['website'])) {
    respond(200, ['success' => true, 'message' => 'Thanks! Your message has been sent.']);
}

// ---------- 3. Collect + trim raw fields ----------
$name    = isset($data['name']) ? trim((string) $data['name']) : '';
$email   = isset($data['email']) ? trim((string) $data['email']) : '';
$phone   = isset($data['phone']) ? trim((string) $data['phone']) : '';
$topic   = isset($data['topic']) ? trim((string) $data['topic']) : 'General enquiry';
$message = isset($data['message']) ? trim((string) $data['message']) : '';

// ---------- 4. Validation ----------
$errors = [];

// Reject header-injection attempts (newlines in fields that end up in email headers).
$hasLineBreak = static fn(string $v): bool => (bool) preg_match('/[\r\n]/', $v);

if ($name === '' || strlen($name) < 2) {
    $errors['name'] = 'Please enter your full name.';
} elseif (strlen($name) > 255 || $hasLineBreak($name)) {
    $errors['name'] = 'Please enter a valid name.';
}

if ($email === '') {
    $errors['email'] = 'Please enter your email address.';
} elseif ($hasLineBreak($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address.';
} elseif (strlen($email) > 255) {
    $errors['email'] = 'Email address is too long.';
}

if ($phone !== '' && (strlen($phone) > 50 || $hasLineBreak($phone))) {
    $errors['phone'] = 'Please enter a valid phone number.';
}

if ($topic === '') {
    $topic = 'General enquiry';
} elseif (strlen($topic) > 100 || $hasLineBreak($topic)) {
    $errors['topic'] = 'Invalid topic.';
}

if ($message === '') {
    $errors['message'] = 'Please enter a message.';
} elseif (strlen($message) > 5000) {
    $errors['message'] = 'Message is too long (max 5000 characters).';
}

if (!empty($errors)) {
    respond(400, [
        'success' => false,
        'message' => 'Please check the highlighted fields and try again.',
        'errors'  => $errors,
    ]);
}

// Strip any stray HTML tags before storing / emailing (defense in depth).
$name    = strip_tags($name);
$email   = strip_tags($email);
$phone   = strip_tags($phone);
$topic   = strip_tags($topic);
$message = strip_tags($message);

// ---------- 5. Store in the database ----------
$stmt = $conn->prepare(
    'INSERT INTO contact_messages (name, email, phone, topic, message) VALUES (?, ?, ?, ?, ?)'
);
$stmt->bind_param('sssss', $name, $email, $phone, $topic, $message);

if (!$stmt->execute()) {
    error_log('SAMSAR contact form: DB insert failed — ' . $stmt->error);
    $stmt->close();
    $conn->close();
    respond(500, ['success' => false, 'message' => 'Something went wrong. Please try again later.']);
}
$stmt->close();
$conn->close();

// ---------- 6. Send the notification email ----------
$mail = new PHPMailer(true);
$emailSent = true;
$emailError = null;

try {
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USERNAME;
    $mail->Password   = SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;
    $mail->Timeout    = 10; // seconds — fail fast instead of hanging the request if SMTP is unreachable

    // Gmail requires the From address to match the authenticated account.
    $mail->setFrom(SMTP_USERNAME, MAIL_FROM_NAME);
    $mail->addAddress(MAIL_TO_ADDRESS, MAIL_TO_NAME);
    // Replying to the notification goes straight to the person who wrote in.
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = 'SAMSAR contact form — ' . $topic;

    $safe = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

    $mail->Body = '
        <div style="font-family:Arial,sans-serif;font-size:14px;color:#1A1A1A;line-height:1.6">
            <h2 style="color:#C72C41;margin:0 0 16px">New contact form message</h2>
            <p><strong>Name:</strong> ' . $safe($name) . '</p>
            <p><strong>Email:</strong> ' . $safe($email) . '</p>
            <p><strong>Phone:</strong> ' . $safe($phone !== '' ? $phone : '—') . '</p>
            <p><strong>Topic:</strong> ' . $safe($topic) . '</p>
            <p><strong>Message:</strong></p>
            <p style="white-space:pre-wrap;background:#F5F5F5;padding:12px;border-radius:6px">' . $safe($message) . '</p>
        </div>';

    $mail->AltBody = "New contact form message\n\n"
        . "Name: {$name}\n"
        . "Email: {$email}\n"
        . "Phone: " . ($phone !== '' ? $phone : '-') . "\n"
        . "Topic: {$topic}\n\n"
        . "Message:\n{$message}\n";

    $mail->send();
} catch (PHPMailerException $e) {
    $emailSent = false;
    $emailError = $mail->ErrorInfo;
    error_log('SAMSAR contact form: PHPMailer failed — ' . $emailError);
}

// The message is already safely stored in contact_messages even if the
// email notification fails, so we still report success to the visitor.
respond(200, [
    'success' => true,
    'message' => 'Thanks! Your message has been sent — we\'ll reply within 24h.',
    'emailed' => $emailSent,
]);
