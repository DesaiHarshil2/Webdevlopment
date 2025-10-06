<?php
// login.php
// Include secure session management
include 'session_boot.php';

// Database connection details from db_connect.php
include 'db_connect.php';

// Set the response header to JSON
header('Content-Type: application/json');

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email and password from the POST request
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate and sanitize input
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
        $conn->close();
        exit;
    }

    // Additional validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        $conn->close();
        exit;
    }

    // Prepare a SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, email, password, role, status FROM users WHERE email = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
        $conn->close();
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user was found in the database
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify the provided password against the stored hash
        if (password_verify($password, $user['password'])) {
            if (isset($user['status']) && $user['status'] === 'inactive') {
                echo json_encode(['success' => false, 'message' => 'Account is inactive. Contact support.']);
                $stmt->close();
                $conn->close();
                exit;
            }
            // Password is correct, start the session
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            if (isset($user['role'])) {
                $_SESSION['user_role'] = $user['role'];
            }
            $_SESSION['last_login'] = time(); // For session timeout feature

            // Regenerate session ID for security after successful login
            session_regenerate_id(true);

            // Return a success JSON response with redirect URL
            echo json_encode([
                'success' => true,
                'message' => 'Login successful!',
                'redirect' => 'dashboard.php'
            ]);
        } else {
            // Invalid password
            echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        }
    } else {
        // No DB match - Fallback to JSON/CSV storage used by register.php

        // Helper to finalize successful login
        $finalizeLogin = function (string $email) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = 0; // No numeric ID from file storage
            $_SESSION['user_email'] = $email;
            $_SESSION['last_login'] = time();
            session_regenerate_id(true);
            echo json_encode([
                'success' => true,
                'message' => 'Login successful!',
                'redirect' => 'dashboard.php'
            ]);
        };

        $emailLower = strtolower($email);
        $matched = false;

        // 1) Try users.json
        $jsonFile = __DIR__ . DIRECTORY_SEPARATOR . 'users.json';
        if (file_exists($jsonFile)) {
            $jsonData = json_decode(file_get_contents($jsonFile), true);
            if (is_array($jsonData)) {
                foreach ($jsonData as $u) {
                    if (isset($u['email'], $u['password']) && strtolower($u['email']) === $emailLower) {
                        if (password_verify($password, $u['password'])) {
                            $matched = true;
                            $finalizeLogin($u['email']);
                        }
                        break; // email matched (password may or may not), stop searching
                    }
                }
            }
        }

        // 2) If not matched, try users.csv
        if (!$matched) {
            $csvFile = __DIR__ . DIRECTORY_SEPARATOR . 'users.csv';
            if (file_exists($csvFile) && ($fp = fopen($csvFile, 'r')) !== false) {
                while (($row = fgetcsv($fp)) !== false) {
                    // CSV format: [firstName, lastName, email, phone, hashedPassword, createdAt]
                    if (isset($row[2], $row[4]) && strtolower(trim($row[2])) === $emailLower) {
                        if (password_verify($password, trim($row[4]))) {
                            fclose($fp);
                            $matched = true;
                            $finalizeLogin(trim($row[2]));
                        }
                        break; // email matched (password may or may not), stop searching
                    }
                }
                if (is_resource($fp)) {
                    fclose($fp);
                }
            }
        }

        if (!$matched) {
            // User not found in DB nor file storage
            echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        }
    }

    $stmt->close();
} else {
    // If the request is not a POST, send an error
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>