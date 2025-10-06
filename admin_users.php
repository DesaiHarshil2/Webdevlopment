<!-- <?php
include 'session_check.php';
requireAdmin();
include 'db_connect.php';

// Handle actions: delete, toggle status, update role
$success = null; $error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $ok = false;
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt) { $stmt->bind_param('i', $id); $ok = $stmt->execute(); $stmt->close(); }
        $success = $ok ? 'User deleted' : 'Delete failed';
    } elseif ($action === 'toggle' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $ok = false;
        $stmt = $conn->prepare("UPDATE users SET status = IF(status='active','inactive','active') WHERE id = ?");
        if ($stmt) { $stmt->bind_param('i', $id); $ok = $stmt->execute(); $stmt->close(); }
        $success = $ok ? 'Status updated' : 'Update failed';
    } elseif ($action === 'role' && isset($_POST['id'], $_POST['role'])) {
        $id = intval($_POST['id']);
        $role = $_POST['role'] === 'admin' ? 'admin' : 'user';
        $ok = false;
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        if ($stmt) { $stmt->bind_param('si', $role, $id); $ok = $stmt->execute(); $stmt->close(); }
        $success = $ok ? 'Role updated' : 'Update failed';
    }
}

$users = $conn->query("SELECT id, first_name, last_name, email, phone, role, status, created_at FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Users</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo"><h2>Admin Dashboard</h2></div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="index.html" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="events.php" class="nav-link">Events</a></li>
                <li class="nav-item"><a href="admin_users.php" class="nav-link active">Users</a></li>
            </ul>
        </div>
    </nav>
    <section class="container" style="padding: 40px 0;">
        <h2>User Management</h2>
        <?php if ($success) { echo "<p style='color:green;'>" . htmlspecialchars($success) . "</p>"; } ?>
        <?php if ($error) { echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>"; } ?>
        <table class="management-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users && $users->num_rows > 0) { while($u = $users->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($u['id']); ?></td>
                    <td><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo htmlspecialchars($u['phone']); ?></td>
                    <td>
                        <form method="post" style="display:inline-block;">
                            <input type="hidden" name="action" value="role">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($u['id']); ?>">
                            <select name="role" onchange="this.form.submit()">
                                <option value="user" <?php echo $u['role']==='user'?'selected':''; ?>>user</option>
                                <option value="admin" <?php echo $u['role']==='admin'?'selected':''; ?>>admin</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <form method="post" style="display:inline-block;">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($u['id']); ?>">
                            <button type="submit" class="btn" style="padding:6px 10px">
                                <?php echo $u['status']==='active'?'Deactivate':'Activate'; ?>
                            </button>
                        </form>
                    </td>
                    <td><?php echo htmlspecialchars($u['created_at']); ?></td>
                    <td>
                        <form method="post" style="display:inline-block;" onsubmit="return confirm('Delete this user?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($u['id']); ?>">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php } } else { ?>
                    <tr><td colspan="8">No users found.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </section>
</body>
</html>
 -->
<?php
include 'session_check.php';
requireAdmin();
include 'db_connect.php';

// Initialize messages
$success = null; $error = null;

// Handle all form submissions (delete, toggle status, update role, and ADD NEW USER)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- Action: Delete User ---
    if ($action === 'delete' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $ok = false;
        // Prevent accidental deletion of the current admin user (assuming the logged-in user's ID is stored in a session variable, e.g., $_SESSION['user_id'])
        if (!isset($_SESSION['user_id']) || $id != $_SESSION['user_id']) {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            if ($stmt) { $stmt->bind_param('i', $id); $ok = $stmt->execute(); $stmt->close(); }
            $success = $ok ? 'User deleted.' : 'Delete failed.';
        } else {
            $error = 'Cannot delete your own admin account.';
        }

    // --- Action: Toggle Status ---
    } elseif ($action === 'toggle' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $ok = false;
        $stmt = $conn->prepare("UPDATE users SET status = IF(status='active','inactive','active') WHERE id = ?");
        if ($stmt) { $stmt->bind_param('i', $id); $ok = $stmt->execute(); $stmt->close(); }
        $success = $ok ? 'Status updated.' : 'Update failed.';

    // --- Action: Update Role ---
    } elseif ($action === 'role' && isset($_POST['id'], $_POST['role'])) {
        $id = intval($_POST['id']);
        $role = $_POST['role'] === 'admin' ? 'admin' : 'user';
        $ok = false;
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        if ($stmt) { $stmt->bind_param('si', $role, $id); $ok = $stmt->execute(); $stmt->close(); }
        $success = $ok ? 'Role updated.' : 'Update failed.';

    // --- Action: Add New User ---
    } elseif ($action === 'add_user' && isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['new_user_role'])) {
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $role = $_POST['new_user_role'] === 'admin' ? 'admin' : 'user';

        if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
            $error = 'All fields (Name, Email, Password) are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format.';
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $ok = false;
            // Assuming default phone and status for a new user
            $default_phone = $_POST['phone'] ?? '';
            $default_status = 'active';

            // Check if email already exists
            $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check_stmt->bind_param('s', $email);
            $check_stmt->execute();
            $check_stmt->store_result();
            if ($check_stmt->num_rows > 0) {
                $error = 'A user with this email already exists.';
            } else {
                $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password_hash, phone, role, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    // password_hash is what your column name should be if you followed standard practice, not 'password'
                    $stmt->bind_param('sssssss', $firstName, $lastName, $email, $hashed_password, $default_phone, $role, $default_status);
                    $ok = $stmt->execute();
                    $stmt->close();
                }
                $success = $ok ? 'New user added successfully!' : 'Failed to add new user.';
            }
            $check_stmt->close();
        }
    }
}

// Fetch users after all actions
$users = $conn->query("SELECT id, first_name, last_name, email, phone, role, status, created_at FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Users</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Minimal styles for the new form */
        .add-user-form {
            background-color: #f8f8f8;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .add-user-form input, .add-user-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .add-user-form button.btn-primary {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .add-user-form button.btn-primary:hover {
            background-color: #0056b3;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo"><h2><i class="fas fa-user-shield"></i> Admin User Management</h2></div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="index.html" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="admin.html" class="nav-link">Dashboard</a></li>
                <li class="nav-item"><a href="events.php" class="nav-link">Events</a></li>
                <li class="nav-item"><a href="admin_users.php" class="nav-link active">Users</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link booking-btn" style="background-color: #dc3545;">Logout</a></li>
            </ul>
        </div>
    </nav>

    <section class="container" style="padding: 40px 0;">
        <?php if ($success) { echo "<p style='color:green; font-weight: bold;'>✅ " . htmlspecialchars($success) . "</p>"; } ?>
        <?php if ($error) { echo "<p style='color:red; font-weight: bold;'>❌ " . htmlspecialchars($error) . "</p>"; } ?>

        <h2>Add New User</h2>
        <div class="add-user-form">
            <form method="post">
                <input type="hidden" name="action" value="add_user">
                <div class="form-row">
                    <input type="text" name="first_name" placeholder="First Name" required>
                    <input type="text" name="last_name" placeholder="Last Name" required>
                </div>
                <div class="form-row">
                    <input type="email" name="email" placeholder="Email Address" required>
                    <input type="text" name="phone" placeholder="Phone (Optional)">
                </div>
                <div class="form-row">
                    <input type="password" name="password" placeholder="Temporary Password" required>
                    <select name="new_user_role">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Create User</button>
            </form>
        </div>

        ---

        <h2>Existing User Management</h2>
        <table class="management-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users && $users->num_rows > 0) { while($u = $users->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($u['id']); ?></td>
                    <td><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo htmlspecialchars($u['phone']); ?></td>
                    <td>
                        <form method="post" style="display:inline-block;">
                            <input type="hidden" name="action" value="role">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($u['id']); ?>">
                            <select name="role" onchange="this.form.submit()">
                                <option value="user" <?php echo $u['role']==='user'?'selected':''; ?>>user</option>
                                <option value="admin" <?php echo $u['role']==='admin'?'selected':''; ?>>admin</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <form method="post" style="display:inline-block;">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($u['id']); ?>">
                            <button type="submit" class="btn" style="padding:6px 10px; background-color: <?php echo $u['status']==='active'?'#ffc107':'#28a745'; ?>; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                <?php echo $u['status']==='active'?'Deactivate':'Activate'; ?>
                            </button>
                        </form>
                    </td>
                    <td><?php echo htmlspecialchars($u['created_at']); ?></td>
                    <td>
                        <form method="post" style="display:inline-block;" onsubmit="return confirm('WARNING: Are you sure you want to permanently delete this user?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($u['id']); ?>">
                            <button type="submit" class="delete-btn" style="background-color: #dc3545; color: white; padding: 6px 10px; border: none; border-radius: 4px; cursor: pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php } } else { ?>
                    <tr><td colspan="8">No users found.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </section>
</body>
</html>