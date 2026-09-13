<?php
/**
 * Core helper functions
 * Redgum Community Library - ICT726 Assignment 4
 *
 * Handles: session management, role-based access control,
 * input sanitisation and CSRF protection.
 */

/**
 * Automatically work out how many folders deep the current page is
 * relative to the project root (e.g. "" for index.php, "../" for
 * admin/books.php or member/my_books.php). This lets includes/header.php
 * and includes/footer.php use a single set of links/asset paths that
 * work correctly no matter which folder the page lives in.
 */
$__root  = realpath(__DIR__ . '/..');
$__here  = realpath(dirname($_SERVER['SCRIPT_FILENAME']));
$__depth = 0;
if ($__root !== false && $__here !== false && $__here !== $__root) {
    $__relative = ltrim(str_replace($__root, '', $__here), DIRECTORY_SEPARATOR);
    $__depth    = $__relative === '' ? 0 : substr_count($__relative, DIRECTORY_SEPARATOR) + 1;
}
define('BASE_URL', str_repeat('../', $__depth));

if (session_status() === PHP_SESSION_NONE) {
    // Harden the session cookie a little.
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/** Escape output to prevent XSS when printing user-supplied data. */
function clean($value) {
    return htmlspecialchars(trim($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/** Is a user currently logged in? */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

/** Is the logged-in user an admin? */
function is_admin(): bool {
    return is_logged_in() && ($_SESSION['role'] ?? '') === 'admin';
}

/** Redirect helper. */
function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}

function require_login(): void {
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Please log in to access that page.';
        redirect(BASE_URL . 'login.php');
    }
}

function require_admin(): void {
    require_login();
    if (!is_admin()) {
        $_SESSION['flash_error'] = 'You do not have permission to view that page.';
        redirect(BASE_URL . 'dashboard.php');
    }
}
/** Generate (or reuse) a CSRF token for the current session. */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Verify a submitted CSRF token matches the session's token. */
function csrf_verify(?string $token): bool {
    return !empty($token) && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

/** Pop and return a one-time flash message (success or error). */
function flash(string $key): ?string {
    if (!empty($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
    return null;
}
