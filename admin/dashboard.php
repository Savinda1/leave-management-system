<?php
session_start();
// Include database connection
include('../config/config.php');
include('../includes/header.php');
include('../includes/sidebar.php');




// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ./signin.php');
    exit();
}
// Fetch the user's name from the session
$user_name = $_SESSION['name'];

// Determine the greeting based on the current time
$hour = date("H");
if ($hour < 12) {
    $greeting = "Good Morning";
} elseif ($hour < 15) {
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}

// Fetch leave requests from the database
$pending_requests = 0;
$approved_requests = 0;
$rejected_requests = 0;

$pending_query = "SELECT COUNT(*) AS count FROM leave_requests WHERE status = 'pending'";
$approved_query = "SELECT COUNT(*) AS count FROM leave_requests WHERE status = 'approved'";
$rejected_query = "SELECT COUNT(*) AS count FROM leave_requests WHERE status = 'rejected'";

$pending_result = $conn->query($pending_query);
$approved_result = $conn->query($approved_query);
$rejected_result = $conn->query($rejected_query);

if ($pending_result->num_rows > 0) {
    $pending_data = $pending_result->fetch_assoc();
    $pending_requests = $pending_data['count'];
}

if ($approved_result->num_rows > 0) {
    $approved_data = $approved_result->fetch_assoc();
    $approved_requests = $approved_data['count'];
}

if ($rejected_result->num_rows > 0) {
    $rejected_data = $rejected_result->fetch_assoc();
    $rejected_requests = $rejected_data['count'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Leave Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .content-container {
            margin-left: 260px;
            padding: 20px;
        }

        .card {
            margin-bottom: 20px;
        }

        .welcome-message {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>



    <!-- Content -->
    <div class="content-container">
        <div class="container">
            <div class="row">
                <!-- Displaying welcome message -->
                <div class="welcome-message">
                    <?php echo $greeting . ', ' . htmlspecialchars($user_name) . '! Welcome to the Admin Dashboard'; ?>
                </div>

                <!-- Pending Leave Requests Card -->
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">Pending Leave Requests</div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($pending_requests); ?></h5>
                            <p class="card-text">Number of pending leave requests.</p>
                        </div>
                    </div>
                </div>

                <!-- Approved Leave Requests Card -->
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Approved Leave Requests</div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($approved_requests); ?></h5>
                            <p class="card-text">Number of completed leave requests.</p>
                        </div>
                    </div>
                </div>

                <!-- Rejected Leave Requests Card -->
                <div class="col-md-4">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-header">Rejected Leave Requests</div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($rejected_requests); ?></h5>
                            <p class="card-text">Number of rejected leave requests.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>