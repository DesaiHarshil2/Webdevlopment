<?php
include 'db_connect.php';

// Function to get the latest 5 events for the public display
function getLatestEvents($conn)
{
    $sql = "SELECT * FROM events WHERE status = 'open' ORDER BY date DESC, time DESC LIMIT 5";
    $result = $conn->query($sql);
    if ($result === FALSE) {
        die("Error fetching events: " . $conn->error);
    }
    return $result;
}

// Function to get all events for the management dashboard
function getAllEvents($conn)
{
    $sql = "SELECT * FROM events ORDER BY date DESC, time DESC";
    $result = $conn->query($sql);
    if ($result === FALSE) {
        die("Error fetching events: " . $conn->error);
    }
    return $result;
}

// Handle form submission for adding a new event
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $capacity = intval($_POST['capacity']);
    $status = ($_POST['status'] === 'closed') ? 'closed' : 'open';

    $stmt = $conn->prepare("INSERT INTO events (title, date, time, location, capacity, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $title, $date, $time, $location, $capacity, $status);
    if ($stmt->execute()) {
        $success = "Event added successfully!";
    } else {
        $error = "Error adding event: " . $stmt->error;
    }
    $stmt->close();
}

// Handle update (edit)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update' && isset($_POST['id'])) {
    $event_id = intval($_POST['id']);
    $title = $_POST['title'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $capacity = intval($_POST['capacity']);
    $status = ($_POST['status'] === 'closed') ? 'closed' : 'open';

    $stmt = $conn->prepare("UPDATE events SET title = ?, date = ?, time = ?, location = ?, capacity = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssssisi", $title, $date, $time, $location, $capacity, $status, $event_id);
    if ($stmt->execute()) {
        $success = "Event updated successfully!";
    } else {
        $error = "Error updating event: " . $stmt->error;
    }
    $stmt->close();
}

// Handle event deletion via a GET request
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $event_id = intval($_GET['id']);

    // Using prepared statements for security
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    if ($stmt->execute()) {
        $success = "Event deleted successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch event for edit mode
$edit_event = null;
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id, title, date, time, location, capacity, status FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result && $result->num_rows === 1) {
            $edit_event = $result->fetch_assoc();
        } else {
            $error = "Event not found.";
        }
    } else {
        $error = "Error loading event: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events & Meetings - Grand Palace Hotel</title>
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
                <li class="nav-item"><a href="events.php" class="nav-link active">Events</a></li>
                <li class="nav-item"><a href="gallery.html" class="nav-link">Gallery</a></li>
                <li class="nav-item"><a href="about.html" class="nav-link">About</a></li>
                <li class="nav-item"><a href="contact.html" class="nav-link">Contact</a></li>
                <li class="nav-item"><a href="login.html" class="nav-link">Login</a></li>
                <li class="nav-item"><a href="signup.html" class="nav-link">Sign Up</a></li>
                <li class="nav-item"><a href="booking.html" class="nav-link booking-btn">Book Now</a></li>
            </ul>
            <div class="hamburger"><span class="bar"></span><span class="bar"></span><span class="bar"></span></div>
        </div>
    </nav>

    <section class="page-header">
        <div class="container">
            <h1>Events & Meetings</h1>
            <p>Host your perfect event in our world-class facilities</p>
        </div>
    </section>

    <section class="event-types">
        <div class="container">
            <h2>Event Types</h2>
            <div class="event-types-grid">
                <div class="event-type-card"><i class="fas fa-heart"></i>
                    <h3>Weddings</h3>
                    <p>Create unforgettable memories with our elegant wedding venues and personalized service</p>
                    <ul>
                        <li>Grand Ballroom (up to 500 guests)</li>
                        <li>Garden Pavilion (up to 200 guests)</li>
                        <li>Intimate Chapel (up to 100 guests)</li>
                        <li>Bridal Suite & Groom's Room</li>
                    </ul>
                </div>
                <div class="event-type-card"><i class="fas fa-graduation-cap"></i>
                    <h3>Conferences & Seminars</h3>
                    <p>Large-scale events with comprehensive support and technical facilities</p>
                    <ul>
                        <li>Main Conference Hall</li>
                        <li>Breakout Rooms</li>
                        <li>Exhibition Space</li>
                        <li>Registration Area</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="event-spaces">
        <div class="container">
            <h2>Event Spaces</h2>
            <div class="spaces-grid">
                <div class="space-card">
                    <div class="space-image"><img
                            src="https://images.unsplash.com/photo-1549488344-93510e8d011f?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                            alt="Grand Ballroom"></div>
                    <div class="space-content">
                        <h3>Grand Ballroom</h3>
                        <div class="space-details">
                            <div class="detail-item"><i class="fas fa-users"></i><span>Capacity: 500 guests</span></div>
                            <div class="detail-item"><i class="fas fa-expand-arrows-alt"></i><span>Size: 5,000 sq
                                    ft</span></div>
                            <div class="detail-item"><i class="fas fa-chandelier"></i><span>Chandelier lighting</span>
                            </div>
                        </div>
                        <p>Our magnificent Grand Ballroom features soaring ceilings, crystal chandeliers, and elegant
                            décor. Perfect for large weddings, galas, and corporate events.</p>
                        <div class="space-features"><span class="feature-tag">Dance Floor</span><span
                                class="feature-tag">Stage</span><span class="feature-tag">AV Equipment</span><span
                                class="feature-tag">Catering Kitchen</span></div>
                    </div>
                </div>
                <div class="space-card">
                    <div class="space-image"><img
                            src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                            alt="Executive Conference Center"></div>
                    <div class="space-content">
                        <h3>Executive Conference Center</h3>
                        <div class="space-details">
                            <div class="detail-item"><i class="fas fa-users"></i><span>Capacity: 200 guests</span></div>
                            <div class="detail-item"><i class="fas fa-expand-arrows-alt"></i><span>Size: 2,500 sq
                                    ft</span></div>
                            <div class="detail-item"><i class="fas fa-laptop"></i><span>Full AV setup</span></div>
                        </div>
                        <p>State-of-the-art conference facility with advanced technology, comfortable seating, and
                            professional presentation equipment.</p>
                        <div class="space-features"><span class="feature-tag">Projector</span><span
                                class="feature-tag">Sound System</span><span class="feature-tag">WiFi</span><span
                                class="feature-tag">Recording</span></div>
                    </div>
                </div>
                <div class="space-card">
                    <div class="space-image"><img
                            src="https://images.unsplash.com/photo-1577708577964-b52e37989938?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                            alt="Boardroom"></div>
                    <div class="space-content">
                        <h3>Executive Boardroom</h3>
                        <div class="space-details">
                            <div class="detail-item"><i class="fas fa-users"></i><span>Capacity: 20 guests</span></div>
                            <div class="detail-item"><i class="fas fa-expand-arrows-alt"></i><span>Size: 800 sq
                                    ft</span></div>
                            <div class="detail-item"><i class="fas fa-chair"></i><span>Executive seating</span></div>
                        </div>
                        <p>Intimate boardroom with premium furnishings, perfect for executive meetings and small
                            corporate gatherings.</p>
                        <div class="space-features"><span class="feature-tag">Executive Chairs</span><span
                                class="feature-tag">Video Conferencing</span><span
                                class="feature-tag">Whiteboard</span><span class="feature-tag">Coffee Service</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="event-services">
        <div class="container">
            <h2>Event Services</h2>
            <div class="services-grid">
                <div class="service-card"><i class="fas fa-utensils"></i>
                    <h3>Catering Services</h3>
                    <p>Customized menus for any occasion, from intimate dinners to grand banquets</p>
                    <ul>
                        <li>Custom menu design</li>
                        <li>Dietary accommodations</li>
                        <li>Beverage service</li>
                        <li>Chef stations</li>
                    </ul>
                </div>
                <div class="service-card"><i class="fas fa-camera"></i>
                    <h3>Photography & Video</h3>
                    <p>Professional photography and videography services</p>
                    <ul>
                        <li>Event photography</li>
                        <li>Video recording</li>
                        <li>Live streaming</li>
                        <li>Photo editing</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="container" style="padding: 40px 0;">
        <h2>Upcoming Events</h2>
        <div id="events-list" class="card-grid">
            <?php
            $latest_events = getLatestEvents($conn);
            if ($latest_events->num_rows > 0) {
                while ($row = $latest_events->fetch_assoc()) {
                    echo "<div class='event-card'>";
                    echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                    echo "<p><strong>Date:</strong> " . htmlspecialchars($row['date']) . "</p>";
                    echo "<p><strong>Time:</strong> " . htmlspecialchars($row['time']) . "</p>";
                    echo "<p><strong>Location:</strong> " . htmlspecialchars($row['location']) . "</p>";
                    echo "<p><strong>Capacity:</strong> " . htmlspecialchars($row['capacity']) . "</p>";
                    echo "<p><strong>Status:</strong> " . htmlspecialchars(isset($row['status']) ? $row['status'] : 'open') . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No upcoming events found.</p>";
            }
            ?>
        </div>
    </section>

    <section class="container" style="padding: 40px 0;">
        <h2>Manage Events</h2>
        <?php if (isset($success)) {
            echo "<p style='color:green;'>$success</p>";
        } ?>
        <?php if (isset($error)) {
            echo "<p style='color:red;'>$error</p>";
        } ?>

        <div class="event-entry-form">
            <h3>Add New Event</h3>
            <form id="add-event-form" method="POST" action="">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="event-title">Event Title:</label>
                    <input type="text" id="event-title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="event-date">Date:</label>
                    <input type="date" id="event-date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="event-time">Time:</label>
                    <input type="time" id="event-time" name="time" required>
                </div>
                <div class="form-group">
                    <label for="event-location">Location:</label>
                    <input type="text" id="event-location" name="location" required>
                </div>
                <div class="form-group">
                    <label for="event-capacity">Capacity:</label>
                    <input type="number" id="event-capacity" name="capacity" min="1" required>
                </div>
                <div class="form-group">
                    <label for="event-status">Status:</label>
                    <select id="event-status" name="status" required>
                        <option value="open">Open</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <button type="submit" class="booking-btn">Add Event</button>
            </form>
        </div>

        <?php if ($edit_event) { ?>
            <div class="event-entry-form" style="margin-top: 20px;">
                <h3>Edit Event #<?php echo htmlspecialchars($edit_event['id']); ?></h3>
                <form id="edit-event-form" method="POST" action="">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_event['id']); ?>">
                    <div class="form-group">
                        <label for="edit-event-title">Event Title:</label>
                        <input type="text" id="edit-event-title" name="title" required
                            value="<?php echo htmlspecialchars($edit_event['title']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="edit-event-date">Date:</label>
                        <input type="date" id="edit-event-date" name="date" required
                            value="<?php echo htmlspecialchars($edit_event['date']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="edit-event-time">Time:</label>
                        <input type="time" id="edit-event-time" name="time" required
                            value="<?php echo htmlspecialchars($edit_event['time']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="edit-event-location">Location:</label>
                        <input type="text" id="edit-event-location" name="location" required
                            value="<?php echo htmlspecialchars($edit_event['location']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="edit-event-capacity">Capacity:</label>
                        <input type="number" id="edit-event-capacity" name="capacity" min="1" required
                            value="<?php echo htmlspecialchars($edit_event['capacity']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="edit-event-status">Status:</label>
                        <select id="edit-event-status" name="status" required>
                            <option value="open" <?php echo ($edit_event['status'] === 'open') ? 'selected' : ''; ?>>Open
                            </option>
                            <option value="closed" <?php echo ($edit_event['status'] === 'closed') ? 'selected' : ''; ?>>
                                Closed</option>
                        </select>
                    </div>
                    <button type="submit" class="booking-btn">Update Event</button>
                </form>
            </div>
        <?php } ?>

        <div class="event-entry-table" style="margin-top: 40px;">
            <h3>Event Management Dashboard</h3>
            <table id="events-management-table" class="management-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $all_events = getAllEvents($conn);
                    if ($all_events->num_rows > 0) {
                        while ($row = $all_events->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['time']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['capacity']) . "</td>";
                            echo "<td>" . htmlspecialchars(isset($row['status']) ? $row['status'] : 'open') . "</td>";
                            echo "<td>";
                            echo "<a href='events.php?action=edit&id=" . htmlspecialchars($row['id']) . "' class='edit-btn' style='margin-right:8px;'>Edit</a>";
                            echo "<a href='events.php?action=delete&id=" . htmlspecialchars($row['id']) . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this event?\");'>Delete</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No events found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Grand Palace Hotel</h3>
                    <p>Your luxury destination in the heart of the city</p>
                    <div class="social-links"><a href="#"><i class="fab fa-facebook"></i></a><a href="#"><i
                                class="fab fa-twitter"></i></a><a href="#"><i class="fab fa-instagram"></i></a><a
                            href="#"><i class="fab fa-linkedin"></i></a></div>
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

    <script src="script1.js"></script>
</body>

</html>