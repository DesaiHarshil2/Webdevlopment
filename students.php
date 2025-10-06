<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db_connect.php';

function respond($data, $status = 200)
{
    http_response_code($status);
    echo json_encode($data);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : ($method === 'GET' ? 'list' : '');

if ($method === 'GET' && $action === 'list') {
    // Optional query params: search (by name), page, per_page
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $perPage = isset($_GET['per_page']) ? min(100, max(1, intval($_GET['per_page']))) : 20;
    $offset = ($page - 1) * $perPage;

    if ($search !== '') {
        $term = "%$search%";
        $countStmt = $conn->prepare('SELECT COUNT(*) AS total FROM students WHERE name LIKE ?');
        $countStmt->bind_param('s', $term);
        $countStmt->execute();
        $countRes = $countStmt->get_result()->fetch_assoc();
        $total = intval($countRes['total']);
        $countStmt->close();

        $stmt = $conn->prepare('SELECT id, name, department, year, email, created_at FROM students WHERE name LIKE ? ORDER BY name ASC LIMIT ? OFFSET ?');
        $stmt->bind_param('sii', $term, $perPage, $offset);
    } else {
        $totalRes = $conn->query('SELECT COUNT(*) AS total FROM students');
        $totalRow = $totalRes ? $totalRes->fetch_assoc() : ['total' => 0];
        $total = intval($totalRow['total']);
        $stmt = $conn->prepare('SELECT id, name, department, year, email, created_at FROM students ORDER BY name ASC LIMIT ? OFFSET ?');
        $stmt->bind_param('ii', $perPage, $offset);
    }

    if (!$stmt->execute()) {
        respond(['status' => 'error', 'message' => 'Query failed'], 500);
    }
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();

    respond([
        'status' => 'success',
        'data' => $rows,
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => max(1, intval(ceil($total / $perPage)))
        ]
    ]);
}

if ($method === 'POST' && $action === 'create') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $department = isset($_POST['department']) ? trim($_POST['department']) : '';
    $year = isset($_POST['year']) ? intval($_POST['year']) : 0;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;

    if ($name === '' || $department === '' || $year < 1 || $year > 4) {
        respond(['status' => 'error', 'message' => 'Invalid input'], 422);
    }

    $stmt = $conn->prepare('INSERT INTO students (name, department, year, email) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssis', $name, $department, $year, $email);
    if (!$stmt->execute()) {
        respond(['status' => 'error', 'message' => $stmt->error], 500);
    }
    $id = $stmt->insert_id;
    $stmt->close();

    respond(['status' => 'success', 'id' => $id], 201);
}

if ($method === 'POST' && $action === 'delete') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if ($id <= 0) {
        respond(['status' => 'error', 'message' => 'Invalid id'], 422);
    }
    $stmt = $conn->prepare('DELETE FROM students WHERE id = ?');
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) {
        respond(['status' => 'error', 'message' => $stmt->error], 500);
    }
    $affected = $stmt->affected_rows;
    $stmt->close();
    respond(['status' => 'success', 'deleted' => $affected]);
}

respond(['status' => 'error', 'message' => 'Unsupported operation'], 400);
?>