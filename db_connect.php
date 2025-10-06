<?php
// db_connect.php (robust for XAMPP defaults)

// Preferred defaults for XAMPP: root with no password on 127.0.0.1:3306
$servername = "127.0.0.1";
$username = "root";
$password = ""; // empty password is default on many XAMPP installs
$dbname = "grand_palace_hotel";
$port = 3306;

// Allow optional overrides via environment variables (set in Apache/PHP if needed)
$servername = getenv('DB_HOST') ?: $servername;
$username = getenv('DB_USER') ?: $username;
$password = getenv('DB_PASS') ?: $password;
$dbname = getenv('DB_NAME') ?: $dbname;
$portEnv = getenv('DB_PORT');
if ($portEnv && ctype_digit($portEnv)) {
    $port = (int) $portEnv;
}

// Try a few safe fallbacks to accommodate local setups
function try_connect($host, $user, $pass, $db, $port)
{
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @new mysqli($host, $user, $pass, $db, $port);
    return ($conn && !$conn->connect_errno) ? $conn : null;
}

$conn = try_connect($servername, $username, $password, $dbname, $port);

if (!$conn && $password !== '') {
    // Fallback 1: try empty password
    $conn = try_connect($servername, $username, '', $dbname, $port);
}

if (!$conn && $servername !== '127.0.0.1') {
    // Fallback 2: try loopback IP
    $conn = try_connect('127.0.0.1', $username, $password, $dbname, $port) ?: try_connect('127.0.0.1', $username, '', $dbname, $port);
}

if (!$conn) {
    // Graceful, generic error to avoid leaking credentials
    http_response_code(500);
    die('Database connection failed. Ensure MySQL is running (port 3306) and credentials are correct.');
}
?>








//<?php
// $servername = "localhost";
// $username = "root"; // Your MySQL username
// $password = "Harshil@123"; // Your MySQL password
// $dbname = "grand_palace_hotel"; // The name of your database

// // Create a new database connection
// $conn = new mysqli($servername, $username, $password, $dbname);

// // Check if the connection was successful
// if ($conn->connect_error) {
//     // If the connection fails, log the error and stop script execution
//     die("Connection failed: " . $conn->connect_error);
// }
// 
?>