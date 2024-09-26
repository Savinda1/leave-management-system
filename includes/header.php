
<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/signin.php');
    exit();
}

// Set default profile picture if none is uploaded
$profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default.jpg';
$user_name = $_SESSION['name'];
?>
<header class="d-flex justify-content-between align-items-center p-3 bg-dark text-white">
    <h1 class="ms-3">
        <?php echo $_SESSION['role'] == 'staff' ? "Staff - Dashboard" : "Administrator - Dashboard"; ?>
    </h1>

    <!-- Notification Icon and Dropdown -->
    <div class="dropdown me-3">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            Notifications
            <?php
            // Fetch unread notifications count
            $stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
            $user_id = $_SESSION['role'] == 'admin' ? 0 : $_SESSION['user_id']; // Admin or staff
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($unread_count);
            $stmt->fetch();
            echo "<span class='badge bg-danger'>$unread_count</span>";
            $stmt->close();
            ?>
        </button>
        <ul class="dropdown-menu" aria-labelledby="notificationDropdown">
            <?php
            // Fetch unread notifications for the user
            $stmt = $conn->prepare("SELECT id, message FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<li class='dropdown-item'>" . htmlspecialchars($row['message']) . "</li>";
                }
            } else {
                echo "<li class='dropdown-item'>No new notifications</li>";
            }

            $stmt->close();
            ?>
        </ul>
    </div>

    <!-- User Profile Picture and Name -->
    <div class="d-flex align-items-center me-3">
        <img src="../uploads/profile_pics/<?php echo htmlspecialchars($profile_picture); ?>" 
             alt="Profile Picture" 
             style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; margin-right: 10px;">
        <span><?php echo htmlspecialchars($user_name); ?></span>
    </div>
</header>
