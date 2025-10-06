<?php
include 'db_connect.php';

// Check if the form was submitted using POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and escape user input to prevent SQL injection
    $title = $conn->real_escape_string($_POST['title']);
    $date = $conn->real_escape_string($_POST['date']);
    $time = $conn->real_escape_string($_POST['time']);
    $location = $conn->real_escape_string($_POST['location']);
    $capacity = (int)$_POST['capacity']; // Cast capacity to integer

    // Prepare the SQL INSERT statement
    $sql = "INSERT INTO events (title, date, time, location, capacity) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Check for SQL preparation errors
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    
    // Bind parameters and execute the statement
    $stmt->bind_param("ssssi", $title, $date, $time, $location, $capacity);
    
    if ($stmt->execute()) {
        // Redirect to the events page on success
        echo "New event added successfully!";
        header("Location: events.html");
        exit();
    } else {
        // Handle execution errors
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>