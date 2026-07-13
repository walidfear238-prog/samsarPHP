<?php
/* ============================================================
   SAMSAR — php/lang.php  (NEW FILE)
   Server-side language management. Does NOT touch any existing
   PHP file or session key used elsewhere in the app — it only
   reads/writes $_SESSION['lang'].

   Usage:
   - Included at the top of a page:      require_once "php/lang.php";
     -> makes get_lang() / t($key) available, $_SESSION['lang'] set.
   - Called directly via fetch (AJAX):   php/lang.php?lang=fr
     -> updates the session and returns { "lang": "fr" } as JSON.
   ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const SAMSAR_SUPPORTED_LANGS = ['en', 'fr', 'ar'];

/**
 * Returns the current language for this session, defaulting to 'en'.
 */
function get_lang(): string
{
    if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], SAMSAR_SUPPORTED_LANGS, true)) {
        return $_SESSION['lang'];
    }
    return 'en';
}

/**
 * Sets the session language if valid.
 */
function set_lang(string $lang): void
{
    if (in_array($lang, SAMSAR_SUPPORTED_LANGS, true)) {
        $_SESSION['lang'] = $lang;
    }
}

/**
 * Optional server-side translation helper for future PHP-rendered
 * strings (e.g. flash messages). Reads the SAME keys used in
 * js/translations.js so both stay in sync; mirrors only a small
 * subset needed server-side today — extend as needed.
 */
function t(string $key, array $params = []): string
{
    static $dict = null;
    static $dictLang = null;
    $lang = get_lang();
    if ($dict === null || $dictLang !== $lang) {
        $path = __DIR__ . "/../translations/{$lang}.php";
        $dict = file_exists($path) ? include $path : [];
        $dictLang = $lang;
    }
    $str = $dict[$key] ?? $key;
    if (!empty($params)) {
        foreach ($params as $k => $v) {
            $str = str_replace('{' . $k . '}', (string) $v, $str);
        }
    }
    return $str;
}

// If hit directly (e.g. fetch("php/lang.php?lang=ar")), handle the request
// and respond with JSON without affecting any other endpoint.
if (basename($_SERVER['SCRIPT_FILENAME']) === 'lang.php') {
    if (isset($_GET['lang'])) {
        set_lang($_GET['lang']);
    }
    header('Content-Type: application/json');
    echo json_encode(['lang' => get_lang()]);
    exit;
}
