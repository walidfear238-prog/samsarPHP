<?php
require_once __DIR__ . '/../../php/lang.php';
/**
 * Shared bootstrap for every api/chat/*.php endpoint.
 *
 * Guarantees:
 *   1. The client ALWAYS gets back valid JSON — even if something fatal
 *      happens (DB connection failure, a PHP fatal error, a stray die()
 *      somewhere, a warning printed to output). No more blank pages or
 *      broken HTML where JSON was expected.
 *   2. Every single request is logged to api/chat/chat-debug.log with a
 *      timestamp, so failures can be diagnosed by opening one file —
 *      no digging through Apache/PHP logs required.
 *
 * Usage in an endpoint file:
 *   require __DIR__ . '/_bootstrap.php';
 *   // $conn          -> ready mysqli connection
 *   // $CHAT_USER_ID  -> (int) logged-in user id (already validated)
 *   // json_out($success, $payload, $http_code, $extra = [])
 */

ini_set('display_errors', '0');   // never let raw PHP errors leak into JSON output
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Buffer everything from here on. If anything writes to output before we
// call json_out() (a warning, a notice, a die() inside a required file),
// the shutdown handler below will catch it and convert it into clean JSON
// instead of letting broken output reach the browser.
ob_start();

define('CHAT_DEBUG_LOG', __DIR__ . '/chat-debug.log');

function chat_log($label, $data = null) {
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $label;
    if ($data !== null) {
        $line .= ' :: ' . (is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    @file_put_contents(CHAT_DEBUG_LOG, $line . "\n", FILE_APPEND);
}

/**
 * The ONLY sanctioned way for a chat endpoint to respond to the client.
 */
function json_out($success, $payload, $http_code = 200, $extra = []) {
    if (ob_get_level() > 0) { ob_end_clean(); }
    http_response_code($http_code);
    header('Content-Type: application/json');

    $body = $extra;
    $body['success'] = (bool) $success;
    if ($success) {
        $body['data'] = $payload;
    } else {
        $body['message'] = $payload;
    }

    chat_log('RESPONSE ' . $http_code, $body);
    echo json_encode($body, JSON_UNESCAPED_UNICODE);
    $GLOBALS['__chat_json_sent'] = true;
    exit;
}

// Last line of defense — runs even after a fatal error or a raw die().
register_shutdown_function(function () {
    if (!empty($GLOBALS['__chat_json_sent'])) return; // json_out() already ran cleanly

    $leftover = ob_get_level() > 0 ? ob_get_contents() : '';
    if (ob_get_level() > 0) { ob_end_clean(); }

    $error = error_get_last();
    chat_log('FATAL/UNEXPECTED', ['leftover' => $leftover, 'last_error' => $error]);

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json');
    }
    echo json_encode([
        'success' => false,
        'message' => t('api.err.server_error_log'),
        'debug'   => [
            'php_error'       => $error['message'] ?? null,
            'php_error_file'  => $error['file'] ?? null,
            'php_error_line'  => $error['line'] ?? null,
            'raw_output'      => $leftover !== '' ? substr($leftover, 0, 800) : null,
        ],
    ], JSON_UNESCAPED_UNICODE);
});

// CORS / preflight
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    if (ob_get_level() > 0) { ob_end_clean(); }
    http_response_code(204);
    $GLOBALS['__chat_json_sent'] = true; // not JSON, but intentional — skip the shutdown handler
    exit;
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

chat_log('REQUEST ' . $_SERVER['REQUEST_METHOD'] . ' ' . ($_SERVER['REQUEST_URI'] ?? ''), [
    'session_user_id' => $_SESSION['user_id'] ?? null,
    'get'             => $_GET,
]);

require_once __DIR__ . '/../../db/connect.php';

if (!isset($conn) || !($conn instanceof mysqli)) {
    json_out(false, t('api.err.db_connection_missing'), 500);
}
if ($conn->connect_error) {
    json_out(false, t('api.err.db_connection_failed') . ': ' . $conn->connect_error, 500);
}

if (!isset($_SESSION['user_id'])) {
    json_out(false, t('api.err.not_authenticated'), 401);
}

$CHAT_USER_ID = (int) $_SESSION['user_id'];
