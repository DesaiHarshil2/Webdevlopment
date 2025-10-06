<?php
// session_refresh.php - Endpoint for refreshing session
header('Content-Type: application/json');

// Include session management utilities
include 'session_check.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Check if session is still valid
if (!isSessionValid()) {
    echo json_encode(['success' => false, 'message' => 'Invalid session']);
    exit;
}

// Update the last login time to extend the session
$_SESSION['last_login'] = time();

// Log the refresh action
logUserActivity('session_refresh');

// Return success response
echo json_encode([
    'success' => true,
    'message' => 'Session refreshed successfully',
    'time_remaining' => getSessionTimeRemaining()
]);
?>