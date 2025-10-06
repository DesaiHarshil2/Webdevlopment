<?php
// session_check.php - Utility for checking session status

// Include secure session management
include 'session_boot.php';

/**
 * Check if user is logged in
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn()
{
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

/**
 * Get current user ID
 * @return int|null User ID if logged in, null otherwise
 */
function getCurrentUserId()
{
    return isLoggedIn() ? $_SESSION['user_id'] : null;
}

/**
 * Get current user email
 * @return string|null User email if logged in, null otherwise
 */
function getCurrentUserEmail()
{
    return isLoggedIn() ? $_SESSION['user_email'] : null;
}

/**
 * Get current user role
 * @return string|null Role if logged in, null otherwise
 */
function getCurrentUserRole()
{
    return isLoggedIn() ? ($_SESSION['user_role'] ?? null) : null;
}

/**
 * Require login - redirect to login page if not logged in
 * @param string $redirect_url Optional redirect URL after login
 */
function requireLogin($redirect_url = 'login.html')
{
    if (!isLoggedIn()) {
        header("Location: $redirect_url");
        exit;
    }
}

/**
 * Require admin role - redirect to login if not admin
 */
function requireAdmin($redirect_url = 'login.html')
{
    requireLogin($redirect_url);
    $role = getCurrentUserRole();
    if ($role !== 'admin') {
        header('HTTP/1.1 403 Forbidden');
        exit('Forbidden: Admins only');
    }
}

/**
 * Check if session is valid (has required data)
 * @return bool True if session is valid, false otherwise
 */
function isSessionValid()
{
    return isLoggedIn() &&
        isset($_SESSION['user_id']) &&
        isset($_SESSION['user_email']) &&
        isset($_SESSION['last_login']);
}

/**
 * Get session timeout remaining time in seconds
 * @return int Seconds remaining before timeout, 0 if expired
 */
function getSessionTimeRemaining()
{
    if (!isset($_SESSION['last_login'])) {
        return 0;
    }

    $session_timeout = 1800; // 30 minutes
    $time_remaining = $session_timeout - (time() - $_SESSION['last_login']);

    return max(0, $time_remaining);
}

/**
 * Log user activity (optional audit trail)
 * @param string $action The action being performed
 */
function logUserActivity($action)
{
    if (isLoggedIn()) {
        $user_id = getCurrentUserId();
        $timestamp = date('Y-m-d H:i:s');
        error_log("User Activity: User ID $user_id performed '$action' at $timestamp");
    }
}
?>