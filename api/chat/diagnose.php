<?php
/**
 * Standalone diagnostic page for the chat system.
 * Open this directly in your browser:
 *   http://localhost/<your-folder>/api/chat/diagnose.php
 *
 * It does NOT depend on _bootstrap.php on purpose — this needs to work
 * even if the bootstrap/connection layer itself is the thing that's broken.
 * It prints a plain, human-readable pass/fail report and never dies silently.
 */

ini_set('display_errors', '1'); // we WANT to see raw PHP errors on this page
error_reporting(E_ALL);

$rows = [];
function step($label, $ok, $detail = '') {
    global $rows;
    $rows[] = [$label, $ok, $detail];
}

// ── 1. PHP basics ────────────────────────────────────────────────────────
step('PHP version', true, PHP_VERSION);
step('mysqli extension loaded', extension_loaded('mysqli'), extension_loaded('mysqli') ? 'yes' : 'MISSING — enable extension=mysqli in php.ini');

// ── 2. Session ───────────────────────────────────────────────────────────
if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
$hasSession = isset($_SESSION['user_id']);
step(
    'Logged-in session detected',
    $hasSession,
    $hasSession ? ('user_id = ' . $_SESSION['user_id']) : 'No $_SESSION[\'user_id\'] found. Open this page in the SAME BROWSER TAB/SESSION where you are logged into SAMSAR (log in first, then visit this URL).'
);

// ── 3. DB connection ─────────────────────────────────────────────────────
$conn = null;
$connectFile = __DIR__ . '/../../db/connect.php';
step('db/connect.php exists', file_exists($connectFile), $connectFile);

if (file_exists($connectFile)) {
    try {
        // Don't let a die() inside connect.php kill this whole diagnostic —
        // include it, then check the resulting $conn variable.
        include $connectFile;
        if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
            step('Database connection', true, 'Connected successfully to database "' . ($dbname ?? '?') . '"');
        } else {
            $err = isset($conn) ? $conn->connect_error : 'no $conn object created';
            step('Database connection', false, $err);
        }
    } catch (Throwable $e) {
        step('Database connection', false, 'Exception: ' . $e->getMessage());
    }
}

// ── 4. Required tables exist ─────────────────────────────────────────────
$requiredTables = ['users', 'properties', 'conversations', 'messages', 'notifications'];
$tableStatus = [];
if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
    foreach ($requiredTables as $t) {
        $res = $conn->query("SHOW TABLES LIKE '" . $conn->real_escape_string($t) . "'");
        $exists = $res && $res->num_rows > 0;
        $tableStatus[$t] = $exists;
        step("Table `$t` exists", $exists);
    }
}

// ── 5. Real insert/rollback test (proves INSERT actually works) ─────────
// Uses REAL existing ids so it doesn't trip foreign-key constraints —
// picks any existing user/property rather than inventing fake ones.
if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
    $missing = array_keys(array_filter($tableStatus, fn($ok) => !$ok));
    if (!empty($missing)) {
        step('Test INSERT into `messages` table', false, 'Skipped — missing table(s): ' . implode(', ', $missing) . '. Create them first (see the SQL script provided alongside this fix), then reload this page.');
    } else {
    try {
        $anyUser = $conn->query("SELECT id FROM users LIMIT 1")->fetch_assoc();
        $anyProp = $conn->query("SELECT id, user_id FROM properties LIMIT 1")->fetch_assoc();

        if (!$anyUser || !$anyProp) {
            step('Test INSERT into `messages` table', false, 'Skipped — need at least one row in `users` and `properties` to test with (none found).');
        } else {
            $conn->begin_transaction();

            $senderId = (int) $anyUser['id'];
            $propId   = (int) $anyProp['id'];
            $ownerId  = (int) $anyProp['user_id'];

            $conn->query("INSERT INTO conversations (property_id, user_id, agency_id) VALUES ({$propId}, {$senderId}, {$ownerId})");
            $convId = $conn->insert_id;

            $conn->query("INSERT INTO messages (conversation_id, sender_id, message, is_read) VALUES ({$convId}, {$senderId}, 'diagnostic test message', 0)");
            $msgId = $conn->insert_id;

            $check = $conn->query("SELECT id FROM messages WHERE id = {$msgId}");
            $inserted = $check && $check->num_rows > 0;

            $conn->rollback(); // undo — this was only a test, nothing is kept

            step('Test INSERT into `messages` table', $inserted, $inserted
                ? "Insert worked (test row id {$msgId}, rolled back — not kept)"
                : 'Insert did not appear to persist within the transaction.');
        }
    } catch (Throwable $e) {
        try { $conn->rollback(); } catch (Throwable $e2) {}
        step('Test INSERT into `messages` table', false, 'Exception: ' . $e->getMessage());
    }
    }
}

// ── 6. Real data check ────────────────────────────────────────────────────
if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error && !empty($tableStatus['conversations']) && !empty($tableStatus['messages'])) {
    try {
        $res = $conn->query("SELECT COUNT(*) AS n FROM conversations");
        step('Existing conversations in DB', true, ($res ? $res->fetch_assoc()['n'] : '?') . ' row(s)');
    } catch (Throwable $e) {
        step('Existing conversations in DB', false, 'Exception: ' . $e->getMessage());
    }
    try {
        $res = $conn->query("SELECT COUNT(*) AS n FROM messages");
        step('Existing messages in DB', true, ($res ? $res->fetch_assoc()['n'] : '?') . ' row(s)');
    } catch (Throwable $e) {
        step('Existing messages in DB', false, 'Exception: ' . $e->getMessage());
    }
}

// ── 7. Debug log file ─────────────────────────────────────────────────────
$logFile = __DIR__ . '/chat-debug.log';
if (file_exists($logFile)) {
    $lines = @file($logFile);
    $tail = $lines ? implode('', array_slice($lines, -25)) : '(empty)';
} else {
    $tail = '(no log file yet — it is created automatically the first time you use a chat feature)';
}

?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>SAMSAR Chat Diagnostics</title>
<style>
  body { font-family: -apple-system, Segoe UI, Arial, sans-serif; max-width: 820px; margin: 40px auto; padding: 0 20px; color: #222; }
  h1 { font-size: 20px; }
  table { width: 100%; border-collapse: collapse; margin-top: 16px; }
  td, th { padding: 8px 10px; border-bottom: 1px solid #e5e5e5; text-align: left; font-size: 14px; vertical-align: top; }
  .ok { color: #1a7f37; font-weight: 600; }
  .fail { color: #c0392b; font-weight: 600; }
  pre { background: #f6f6f6; padding: 14px; border-radius: 8px; overflow-x: auto; font-size: 12px; white-space: pre-wrap; }
</style>
</head>
<body>
<h1>SAMSAR Chat System — Diagnostics</h1>
<table>
<tr><th>Check</th><th>Result</th><th>Detail</th></tr>
<?php foreach ($rows as [$label, $ok, $detail]): ?>
<tr>
  <td><?= htmlspecialchars($label) ?></td>
  <td class="<?= $ok ? 'ok' : 'fail' ?>"><?= $ok ? 'OK' : 'FAIL' ?></td>
  <td><?= htmlspecialchars((string) $detail) ?></td>
</tr>
<?php endforeach; ?>
</table>

<h2 style="font-size:16px;margin-top:32px;">Last 25 lines of chat-debug.log</h2>
<pre><?= htmlspecialchars($tail) ?></pre>
</body>
</html>
