<?php
include 'db_connect.php';

header('Content-Type: application/json');

$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 5;
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'open';
if ($statusFilter !== 'open' && $statusFilter !== 'closed' && $statusFilter !== 'all') {
    $statusFilter = 'open';
}

if ($statusFilter === 'all') {
    $stmt = $conn->prepare("SELECT id, title, date, time, location, capacity, status FROM events ORDER BY date DESC, time DESC LIMIT ?");
    $stmt->bind_param("i", $limit);
} else {
    $stmt = $conn->prepare("SELECT id, title, date, time, location, capacity, status FROM events WHERE status = ? ORDER BY date DESC, time DESC LIMIT ?");
    $stmt->bind_param("si", $statusFilter, $limit);
}

$events = array();
if ($stmt && $stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    echo json_encode(["success" => true, "data" => $events]);
} else {
    $errorMsg = $stmt ? $stmt->error : $conn->error;
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Failed to load events: " . $errorMsg]);
}

if ($stmt) {
    $stmt->close();
}
$conn->close();
?>