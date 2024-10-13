<?php
session_start();
// Include database connection
include('../config/config.php');
include('../includes/header.php');
include('../includes/sidebar.php');


// Check if the user is logged in as staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: ../auth/signin.php');
    exit();
}

$user_id = $_SESSION['user_id'];
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

// Fetch leave counts
$queries = [
    'pending' => "SELECT COUNT(*) AS count FROM leave_requests WHERE user_id = ? AND status = 'pending'",
    'approved' => "SELECT COUNT(*) AS count FROM leave_requests WHERE user_id = ? AND status = 'approved'",
    'rejected' => "SELECT COUNT(*) AS count FROM leave_requests WHERE user_id = ? AND status = 'rejected'"
];

$counts = [];
foreach ($queries as $key => $query) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $counts[$key] = $stmt->get_result()->fetch_assoc()['count'];
    $stmt->close();
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Leave Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .dashboard-container {
            margin-left: 260px; 
            padding: 30px;
        }

        .card {
            margin-bottom: 20px;
        }

        .welcome-message {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        
        @media (max-width: 768px) {
            .dashboard-container {
                margin-left: 0; 
                padding: 15px;
            }
        }
    </style>
</head>

<body>

    <div class="dashboard-container">
        <div class="container">
            <div class="row">
                <div class="welcome-message">
                    <?php echo htmlspecialchars($greeting) . ', ' . htmlspecialchars($user_name) . '! Welcome to the Staff Dashboard'; ?>
                </div>

                <div class="col-md-4">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-header">Pending Leave Requests</div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($counts['pending']); ?></h5>
                            <p class="card-text">Your leave requests awaiting approval.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Approved Leave Requests</div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($counts['approved']); ?></h5>
                            <p class="card-text">Your approved leave requests.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-header">Rejected Leave Requests</div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($counts['rejected']); ?></h5>
                            <p class="card-text">Your leave requests that were rejected.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>