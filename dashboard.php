<?php
// dashboard.php
// Include session management utilities
include 'session_check.php';

// Require login - redirect if not logged in
requireLogin();

// Log dashboard access
logUserActivity('dashboard_access');

// Get user information
$user_id = getCurrentUserId();
$user_email = getCurrentUserEmail();
$session_time_remaining = getSessionTimeRemaining();

// The user is logged in, display the dashboard content
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Grand Palace Hotel</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2><i class="fas fa-hotel"></i> Grand Palace Hotel</h2>
            </div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="index.html" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="rooms.html" class="nav-link">Rooms</a></li>
                <li class="nav-item"><a href="services.html" class="nav-link">Services</a></li>
                <li class="nav-item"><a href="gallery.html" class="nav-link">Gallery</a></li>
                <li class="nav-item"><a href="contact.html" class="nav-link">Contact</a></li>
                <li class="nav-item"><a href="dashboard.php" class="nav-link active">Dashboard</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link">Logout</a></li>
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>

    <section class="auth-section">
        <div class="container">
            <div class="auth-container">
                <div class="auth-form-container">
                    <div class="auth-header">
                        <h2>Welcome to Your Dashboard!</h2>
                        <p>You are successfully logged in. Enjoy your stay at the Grand Palace Hotel.</p>
                    </div>

                    <div class="user-info"
                        style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">
                        <h4>Account Information</h4>
                        <p><strong>User ID:</strong> <?php echo htmlspecialchars($user_id); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
                        <p><strong>Session Status:</strong>
                            <span style="color: #28a745;">Active</span>
                            <?php if ($session_time_remaining > 0): ?>
                                (<?php echo floor($session_time_remaining / 60); ?> minutes remaining)
                            <?php endif; ?>
                        </p>
                    </div>

                    <p>From here, you can manage your bookings, view your profile, and access exclusive member benefits.
                    </p>

                    <div class="dashboard-actions" style="margin: 20px 0;">
                        <a href="logout.php" class="btn btn-primary btn-full" style="margin-bottom: 10px;">Logout</a>
                        <button onclick="refreshSession()" class="btn btn-secondary btn-full">Refresh Session</button>
                    </div>
                </div>
                <div class="auth-info">
                    <div class="info-content">
                        <h3>Your Grand Palace Account</h3>
                        <p>Enjoy exclusive access and personalized services.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Grand Palace Hotel</h3>
                    <p>Your luxury destination in the heart of the city</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="rooms.html">Rooms</a></li>
                        <li><a href="services.html">Services</a></li>
                        <li><a href="login.html">Login</a></li>
                        <li><a href="events.html">Events</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <p><i class="fas fa-map-marker-alt"></i> Junagadh, Gujarat, India</p>
                    <p><i class="fas fa-phone"></i> +91 9825000000</p>
                    <p><i class="fas fa-envelope"></i> info@grandpalacehotel.com</p>
                </div>
                <div class="footer-section">
                    <h4>Management</h4>
                    <ul>
                        <li><a href="admin.html">Admin Panel</a></li>
                        <li><a href="staff.html">Staff Portal</a></li>
                        <li><a href="booking.html">Reservations</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Grand Palace Hotel. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="script.js"></script>
    <script>
        // Session management JavaScript
        function refreshSession() {
            // Make a request to refresh the session
            fetch('session_refresh.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Session refreshed successfully!');
                        location.reload(); // Reload the page to show updated session info
                    } else {
                        alert('Failed to refresh session: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while refreshing the session.');
                });
        }

        // Auto-refresh session every 10 minutes
        setInterval(function () {
            fetch('session_refresh.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        // Session expired, redirect to login
                        window.location.href = 'login.html?expired=1';
                    }
                })
                .catch(error => {
                    console.error('Session check failed:', error);
                });
        }, 600000); // 10 minutes
    </script>
</body>

</html>