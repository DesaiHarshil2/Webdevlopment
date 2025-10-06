<?php
// logout.php
// Include secure session management
include 'session_boot.php';

// Check if user is actually logged in before logging out
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Log the logout action (optional - for audit trail)
    $user_id = $_SESSION['user_id'] ?? 'unknown';
    error_log("User logout: User ID $user_id at " . date('Y-m-d H:i:s'));
}

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to the login page after a successful logout
header("Location: login.html?loggedout=1");
exit;
?>