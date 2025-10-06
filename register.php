<?php
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     // Sanitize & validate inputs
//     $firstName = htmlspecialchars(trim($_POST['first-name']));
//     $lastName  = htmlspecialchars(trim($_POST['last-name']));
//     $email     = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
//     $phone     = htmlspecialchars(trim($_POST['phone']));
//     $password  = htmlspecialchars(trim($_POST['password']));
//     $confirmPassword = htmlspecialchars(trim($_POST['confirm-password']));
//     $terms     = isset($_POST['terms']) ? true : false;

//     // Validation checks
//     if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
//         die("Error: All required fields must be filled.");
//     }

//     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//         die("Error: Invalid email format.");
//     }

//     if ($password !== $confirmPassword) {
//         die("Error: Passwords do not match.");
//     }

//     if (!$terms) {
//         die("Error: You must accept the terms and conditions.");
//     }

//     // Hash password for security
//     $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

//     // ✅ Store in CSV file
//     $csvFile = "users.csv";
//     $csvData = [$firstName, $lastName, $email, $phone, $hashedPassword, date("Y-m-d H:i:s")];

//     $fp = fopen($csvFile, "a");
//     fputcsv($fp, $csvData);
//     fclose($fp);

//     // ✅ Also store in JSON file (Advanced)
//     $jsonFile = "users.json";
//     $userData = [
//         "firstName" => $firstName,
//         "lastName"  => $lastName,
//         "email"     => $email,
//         "phone"     => $phone,
//         "password"  => $hashedPassword,
//         "createdAt" => date("Y-m-d H:i:s")
//     ];

//     if (file_exists($jsonFile)) {
//         $existingData = json_decode(file_get_contents($jsonFile), true);
//     } else {
//         $existingData = [];
//     }

//     $existingData[] = $userData;
//     file_put_contents($jsonFile, json_encode($existingData, JSON_PRETTY_PRINT));

//     // ✅ Confirmation message
//     echo "<h2>Registration Successful!</h2>";
//     echo "<p>Welcome, <strong>$firstName $lastName</strong>. Your account has been created.</p>";
//     echo "<p><a href='login.html'>Click here to Login</a></p>";
// }
?>
<?php
// register.php (hardened)
include 'session_boot.php';
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $firstName = trim($_POST['first-name'] ?? '');
    $lastName = trim($_POST['last-name'] ?? '');
    $emailRaw = trim($_POST['email'] ?? '');
    $email = filter_var($emailRaw, FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm-password'] ?? '');
    $captchaAnswer = trim($_POST['captcha_answer'] ?? '');
    $terms = isset($_POST['terms']);

    // Server-side validation
    $fail = function (string $msg) {
        header("Location: error.php?msg=" . urlencode($msg));
        exit; };
    if ($firstName === '' || $lastName === '' || $email === '' || $password === '' || $confirmPassword === '') {
        $fail('All required fields must be filled.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $fail('Invalid email format.');
    }
    if (!preg_match("/^(?=.*[A-Z])(?=.*\\d).{8,}$/", $password)) {
        $fail('Password must be 8+ chars, include uppercase and number.');
    }
    if ($password !== $confirmPassword) {
        $fail('Passwords do not match.');
    }
    if (!$terms) {
        $fail('You must accept the terms and conditions.');
    }

    // CAPTCHA check
    $expected = $_SESSION['captcha_answer'] ?? null;
    if ($expected === null || !ctype_digit($captchaAnswer) || intval($captchaAnswer) !== intval($expected)) {
        $fail('CAPTCHA failed. Please try again.');
    }
    unset($_SESSION['captcha_answer']);

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    if ($hashedPassword === false) {
        $fail('Password hashing failed.');
    }

    // Try DB first (users table: id, first_name, last_name, email, phone, password_hash, created_at)
    $insertedToDb = false;
    if ($conn && !$conn->connect_error) {
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, password, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param("sssss", $firstName, $lastName, $email, $phone, $hashedPassword);
            if ($stmt->execute()) {
                $insertedToDb = true;
            }
            $stmt->close();
        }
    }

    // Fallback to CSV/JSON storage for compatibility
    if (!$insertedToDb) {
        $csvFile = __DIR__ . DIRECTORY_SEPARATOR . "users.csv";
        $csvData = [$firstName, $lastName, $email, $phone, $hashedPassword, date("Y-m-d H:i:s")];
        if (($fp = fopen($csvFile, "a")) !== false) {
            fputcsv($fp, $csvData);
            fclose($fp);
        }

        $jsonFile = __DIR__ . DIRECTORY_SEPARATOR . "users.json";
        $existingData = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
        if (!is_array($existingData)) {
            $existingData = [];
        }
        $existingData[] = [
            "firstName" => $firstName,
            "lastName" => $lastName,
            "email" => $email,
            "phone" => $phone,
            "password" => $hashedPassword,
            "createdAt" => date("Y-m-d H:i:s")
        ];
        file_put_contents($jsonFile, json_encode($existingData, JSON_PRETTY_PRINT));
    }

    header("Location: success.php?name=" . urlencode($firstName));
    exit;
}
?>