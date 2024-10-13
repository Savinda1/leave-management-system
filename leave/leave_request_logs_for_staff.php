<?php
// Include database connection
session_start();
include('../config/config.php');
$profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default.jpg';
$user_name = $_SESSION['name'];
?>

<header class="d-flex justify-content-between align-items-center p-3 bg-dark text-white">
    <h1 class="ms-3">Leave Request Logs</h1>
    <div class="d-flex align-items-center me-3">
        <img src="../uploads/profile_pics/<?php echo htmlspecialchars($profile_picture); ?>"
            alt="Profile Picture"
            style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; margin-right: 10px;">
        <span><?php echo htmlspecialchars($user_name); ?></span>
    </div>
</header>

<?php

include('../includes/sidebar.php');


// Check if the user is logged in and is staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: ../auth/signin.php');
    exit();
}

// Initialize filter variables
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Build the base query
$query = "SELECT leave_requests.id, u.name, leave_requests.leave_type, leave_requests.start_date, leave_requests.end_date, leave_requests.reason, leave_requests.status, leave_requests.created_at 
          FROM leave_requests 
          JOIN users u ON leave_requests.user_id = u.id 
          WHERE u.id = ?";


if (!empty($status_filter)) {
    $query .= " AND leave_requests.status = ?";
}


$query .= " ORDER BY leave_requests.created_at DESC";

// Prepare the statement
$stmt = $conn->prepare($query);

// Check if the status filter is present, and bind parameters accordingly
if (!empty($status_filter)) {
    $stmt->bind_param('is', $_SESSION['user_id'], $status_filter); // 'i' for user_id (integer), 's' for status (string)
} else {
    $stmt->bind_param('i', $_SESSION['user_id']); // Only user_id needs to be bound
}

 
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>

        body{
            font-family: 'Arial', sans-serif;
        }
        .table-container {
            margin-top: 20px;
            max-width:1210px;
        }
        .container {
            margin-left: 260px; /* Ensure it matches your sidebar width */
            padding: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        

        <!-- Filter Form -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <select name="status" class="form-control">
                        <option value="">Filter by status</option>
                        <option value="pending" <?php if ($status_filter == 'pending') echo 'selected'; ?>>Pending</option>
                        <option value="approved" <?php if ($status_filter == 'approved') echo 'selected'; ?>>Approved</option>
                        <option value="rejected" <?php if ($status_filter == 'rejected') echo 'selected'; ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <div class="table-container">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Request Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['leave_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td><?php echo htmlspecialchars($row['reason']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            </tr>
                        <?php }
                    } else {
                        echo "<tr><td colspan='7'>No leave requests found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    

    <br><br>
</body>
</html>