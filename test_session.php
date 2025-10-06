<?php
// test_session.php - Test file to demonstrate session functionality
include 'session_check.php';

// Set content type
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Test - Grand Palace Hotel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .success {
            color: #28a745;
        }

        .error {
            color: #dc3545;
        }

        .btn {
            padding: 10px 20px;
            margin: 10px;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }
    </style>
</head>

<body>
    <h1>Session Management Test</h1>

    <div class="info">
        <h2>Session Status</h2>
        <?php if (isLoggedIn()): ?>
            <p class="success">✓ User is logged in</p>
            <p><strong>User ID:</strong> <?php echo htmlspecialchars(getCurrentUserId()); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars(getCurrentUserEmail()); ?></p>
            <p><strong>Session Valid:</strong> <?php echo isSessionValid() ? 'Yes' : 'No'; ?></p>
            <p><strong>Time Remaining:</strong> <?php echo getSessionTimeRemaining(); ?> seconds</p>
        <?php else: ?>
            <p class="error">✗ User is not logged in</p>
        <?php endif; ?>
    </div>

    <div class="info">
        <h2>Available Actions</h2>
        <?php if (isLoggedIn()): ?>
            <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        <?php else: ?>
            <a href="login.html" class="btn btn-primary">Login</a>
            <a href="signup.html" class="btn btn-secondary">Sign Up</a>
        <?php endif; ?>
    </div>

    <div class="info">
        <h2>Session Security Features</h2>
        <ul>
            <li>✓ Secure session cookie parameters</li>
            <li>✓ Session ID regeneration on login</li>
            <li>✓ Session timeout (30 minutes)</li>
            <li>✓ Session validation checks</li>
            <li>✓ Proper session destruction on logout</li>
            <li>✓ CSRF protection with SameSite cookies</li>
            <li>✓ HttpOnly cookies (JavaScript protection)</li>
        </ul>
    </div>
</body>

</html>