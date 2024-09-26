<?php
// Include database connection
include('../config/config.php');
include('../includes/header.php'); 

session_start();

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background-color: #343a40;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .card {
            margin-bottom: 20px;
        }

        .logout-button {
            display: block;
            width: 150px;
            padding: 10px;
            background-color: #dc3545;
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
            font-size: 16px;
        }

        .logout-button:hover {
            background-color: #c82333;
        }
        .welcome-message {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    

    <div class="dashboard-container">
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
        <div class="row">
            <div class="col-md-12 text-center">
                <a href="../leave/review_leave_requests.php" class="btn btn-warning mt-3">Review Pending Leave Requests</a>
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-md-12 text-center">
                <a href="../leave/leave_request_logs.php" class="btn btn-info btn-logs">View Leave Request Logs</a>
            </div>
        </div>

        <div class="col-md-12 text-center">
            <a href="../profile/profile.php" class="btn btn-warning mt-3">Go to Profile</a>
        </div>

        
        <a href="http://localhost/leave_management/auth/logout.php" class="logout-button">Logout</a>
    </div>

   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
