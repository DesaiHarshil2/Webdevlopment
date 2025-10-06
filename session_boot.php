<?php
// session_boot.php - Secure session management

// Prevent session fixation attacks
if (session_status() === PHP_SESSION_NONE) {
    // Set session cookie parameters for security
    $params = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => 0, // Session cookie (expires when browser closes)
        'path' => '/',
        'domain' => '', // Let PHP determine the domain
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', // Use HTTPS if available
        'httponly' => true, // Prevent JavaScript access to cookie
        'samesite' => 'Lax' // Protect against CSRF
    ]);

    // Start the session
    session_start();

    // Regenerate session ID to prevent session fixation
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
    }
}

// Supplementary Problem: Add session timeout
$session_timeout = 1800; // 30 minutes (30 * 60 seconds) - consistent across all files
if (isset($_SESSION['last_login']) && (time() - $_SESSION['last_login'] > $session_timeout)) {
    // Session has expired
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header('Location: login.html?expired=1');
    exit;
}

// Update last login time on every page load (only if user is logged in)
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $_SESSION['last_login'] = time();
}