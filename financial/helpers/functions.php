<?php
/**
 * helpers/functions.php
 * Small reusable utility functions used across the whole system.
 * Include this once, early, e.g. from public/index.php or a bootstrap file.
 */

/**
 * Sanitize a string for safe output (prevents XSS when echoing user input into HTML).
 * NOTE: This is for OUTPUT escaping. Use prepared statements (PDO) for DB safety,
 * never rely on this function alone to protect against SQL injection.
 */
function sanitize(?string $value): string
{
    return htmlspecialchars(trim($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Format a number as Philippine Peso currency, e.g. 12345.5 -> "₱12,345.50"
 */
function formatCurrency(float|int|string|null $amount, ?string $symbol = null): string
{
    $symbol ??= defined('APP_CURRENCY_SYMBOL') ? APP_CURRENCY_SYMBOL : '₱';
    $amount = (float) ($amount ?? 0);
    return $symbol . number_format($amount, 2);
}

/**
 * Format a date string/DateTime into a consistent display format, e.g. "Jul 24, 2026"
 * Accepts Y-m-d, Y-m-d H:i:s, or a DateTime instance. Returns '—' on invalid/empty input.
 * Defaults to APP_DATE_FORMAT from config/database.php if defined.
 */
function formatDate(DateTime|string|null $date, ?string $format = null): string
{
    $format ??= defined('APP_DATE_FORMAT') ? APP_DATE_FORMAT : 'M d, Y';
    if (empty($date)) {
        return '—';
    }

    try {
        $dt = $date instanceof DateTime ? $date : new DateTime($date);
        return $dt->format($format);
    } catch (Exception $e) {
        return '—';
    }
}

/**
 * Format a datetime including time, e.g. "Jul 24, 2026 3:45 PM"
 */
function formatDateTime(DateTime|string|null $date): string
{
    return formatDate($date, 'M d, Y g:i A');
}

/**
 * Generate a unique reference number for a given prefix/module.
 * Example: generateReferenceNo('AP') -> "AP-20260724-4F27A1"
 * Combine with a DB uniqueness check in the model layer for guaranteed collision safety.
 */
function generateReferenceNo(string $prefix = 'REF'): string
{
    $datePart   = date('Ymd');
    $randomPart = strtoupper(bin2hex(random_bytes(3))); // 6 hex chars
    return strtoupper($prefix) . '-' . $datePart . '-' . $randomPart;
}

/**
 * Build an app-relative URL that respects BASE_URL (e.g. '/financial').
 * Usage: url('/chart-of-accounts') -> "/financial/chart-of-accounts"
 * Pass an already-absolute URL (http://, https://) through unchanged.
 */
function url(string $path = ''): string
{
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
    $path = '/' . ltrim($path, '/');

    return $base . $path;
}

/**
 * Redirect helper — sends a Location header and stops execution.
 * Automatically prefixes app-relative paths with BASE_URL via url().
 */
function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

/**
 * Flash message helper — stash a message in the session to show after a redirect.
 * Usage: setFlash('success', 'Record saved.');  then in the view: getFlash('success')
 */
function setFlash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function getFlash(string $key): ?string
{
    if (!empty($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

/**
 * Quick boolean check for whether the current request is a POST request.
 */
function isPost(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}

/**
 * Get a POST value safely, with an optional default.
 */
function post(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $default;
}

/**
 * Get a GET value safely, with an optional default.
 */
function get(string $key, mixed $default = null): mixed
{
    return $_GET[$key] ?? $default;
}