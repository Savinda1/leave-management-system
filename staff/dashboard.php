<?php
// Include database connection
include('../config/config.php');
session_start();

// Check if the user is logged in as staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: ../auth/signin.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch pending, approved, and rejected leave requests
$pending_query = "SELECT COUNT(*) AS count FROM leave_requests WHERE user_id = ? AND status = 'pending'";
$approved_query = "SELECT COUNT(*) AS count FROM leave_requests WHERE user_id = ? AND status = 'completed'";
$rejected_query = "SELECT COUNT(*) AS count FROM leave_requests WHERE user_id = ? AND status = 'rejected'";

$stmt = $conn->prepare($pending_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$pending_result = $stmt->get_result()->fetch_assoc()['count'];

$stmt = $conn->prepare($approved_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$approved_result = $stmt->get_result()->fetch_assoc()['count'];

$stmt = $conn->prepare($rejected_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$rejected_result = $stmt->get_result()->fetch_assoc()['count'];

$stmt->close();

// Handle leave application
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $leave_type = $_POST['leave_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $insert_query = "INSERT INTO leave_requests (user_id, leave_type, start_date, end_date, status) VALUES (?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param('isss', $user_id, $leave_type, $start_date, $end_date);

    if ($stmt->execute()) {
        $success_message = "Leave request submitted successfully!";
    } else {
        $error_message = "Failed to submit leave request.";
    }
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
        }

        .dashboard-container {
            width: 80%;
            margin: 0 auto;
            padding: 30px;
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
    </style>
</head>
<body>

    <header>
        <h1>Staff Dashboard</h1>
    </header>

    <div class="dashboard-container">
        <div class="container">
            <div class="row">
                <!-- Pending Leave Requests Card -->
                <div class="col-md-4">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-header">Pending Leave Requests</div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($pending_result); ?></h5>
                            <p class="card-text">Your leave requests awaiting approval.</p>
                        </div>
                    </div>
                </div>

                <!-- Approved Leave Requests Card -->
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Approved Leave Requests</div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($approved_result); ?></h5>
                            <p class="card-text">Your approved leave requests.</p>
                        </div>
                    </div>
                </div>

                <!-- Rejected Leave Requests Card -->
                <div class="col-md-4">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-header">Rejected Leave Requests</div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($rejected_result); ?></h5>
                            <p class="card-text">Your leave requests that were rejected.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Apply for Leave Section -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-header">Apply for Leave</div>
                        <div class="card-body">
                            <?php if (isset($success_message)): ?>
                                <div class="alert alert-success"><?php echo $success_message; ?></div>
                            <?php endif; ?>
                            <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger"><?php echo $error_message; ?></div>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="leave_type" class="form-label">Leave Type</label>
                                    <select class="form-select" id="leave_type" name="leave_type" required>
                                        <option value="">Select Leave Type</option>
                                        <option value="Sick Leave">Sick Leave</option>
                                        <option value="Casual Leave">Casual Leave</option>
                                        <option value="Annual Leave">Annual Leave</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Leave Request</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Leaves Section -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-header">Upcoming Leaves</div>
                        <div class="card-body">
                            <p class="card-text">No upcoming leaves scheduled.</p>
                            <!-- You can fetch and display upcoming leaves here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logout button -->
            <a href="http://localhost/leave_management/auth/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <!-- Bootstrap JS (optional for interactive components) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
