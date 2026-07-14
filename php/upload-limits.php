<?php
/* ============================================================
   SAMSAR — php/upload-limits.php  (NEW FILE)
   Read-only helper for displaying the server's real image upload
   limit in the UI. Does not change any upload behaviour — the
   limit is still enforced by PHP itself (upload_max_filesize) and
   by the existing upload handlers, exactly as before.
   ============================================================ */

/**
 * Converts a php.ini size shorthand (e.g. "2M", "8M", "512K") into bytes.
 */
function samsar_ini_size_to_bytes(string $iniValue): int
{
    $iniValue = trim($iniValue);
    if ($iniValue === '') {
        return 0;
    }

    $unit = strtoupper(substr($iniValue, -1));
    $num = (float) $iniValue;

    switch ($unit) {
        case 'G':
            return (int) ($num * 1024 * 1024 * 1024);
        case 'M':
            return (int) ($num * 1024 * 1024);
        case 'K':
            return (int) ($num * 1024);
        default:
            return (int) $iniValue;
    }
}

/**
 * Human-friendly label for a byte count, e.g. 2097152 -> "2 MB".
 */
function samsar_format_bytes(int $bytes): string
{
    if ($bytes <= 0) {
        return '';
    }
    if ($bytes >= 1024 * 1024 * 1024) {
        return round($bytes / (1024 * 1024 * 1024), 1) . ' GB';
    }
    if ($bytes >= 1024 * 1024) {
        return round($bytes / (1024 * 1024)) . ' MB';
    }
    if ($bytes >= 1024) {
        return round($bytes / 1024) . ' KB';
    }
    return $bytes . ' B';
}

/**
 * The effective max size (in bytes) for a single uploaded image, based on
 * this server's real php.ini configuration.
 */
function samsar_max_upload_bytes(): int
{
    return samsar_ini_size_to_bytes((string) ini_get('upload_max_filesize'));
}

/**
 * Human-friendly version of samsar_max_upload_bytes(), or '' if unknown.
 */
function samsar_max_upload_label(): string
{
    return samsar_format_bytes(samsar_max_upload_bytes());
}
