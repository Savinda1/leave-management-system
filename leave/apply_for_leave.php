<?php
include('../config/config.php');

session_start();

// Check if the user is logged in as staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: ../auth/signin.php');
    exit();
}

$profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default.jpg';
$user_name = $_SESSION['name'];

// Handle leave application
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $leave_type = $_POST['leave_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];

    // Insert leave request query
    $insert_query = "INSERT INTO leave_requests (user_id, leave_type, start_date, end_date, reason, status) VALUES (?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param('issss', $user_id, $leave_type, $start_date, $end_date, $reason);

    if ($stmt->execute()) {
        // Successfully inserted leave request
        $success_message = "Leave request submitted successfully!";
        
        // Insert notification for admin
        $admin_message = "A new leave request has been submitted by " . $_SESSION['name'] . ".";
        $is_read = 0; // Mark as unread
        $created_at = date("Y-m-d H:i:s"); // Current timestamp for notification

        $stmt_notification = $conn->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (0, ?, ?, ?)");
        $stmt_notification->bind_param("sis", $admin_message, $is_read, $created_at);
        $stmt_notification->execute();
        $stmt_notification->close();

    } else {
        // If leave request insertion fails
        $error_message = "Failed to submit leave request.";
    }
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply For Leave</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        header {
            background-color: #343a40;
            padding: 10px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        header .user-info {
            display: flex;
            align-items: center;
        }

        header .user-info span {
            margin-left: 10px;
            font-size: 1.1rem;
            color: #ffffff;
        }
    </style>
</head>

<body>
    <header class="d-flex justify-content-between align-items-center p-3 bg-dark text-white">
        <h1 class="ms-3"><?php
                            if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'staff') {
                                echo "Apply for a leave";
                            }
                            ?></h1>


        <div class="d-flex align-items-center me-3">
            <img src="../uploads/profile_pics/<?php echo htmlspecialchars($profile_picture); ?>"
                alt="Profile Picture"
                style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; margin-right: 10px;">
            <span><?php echo htmlspecialchars($user_name); ?></span>
        </div>
    </header>
    <br><br>
    <div class="container">
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

                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason for Leave</label>
                                <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit Leave Request</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 text-center">
        <a href="../staff/dashboard.php" class="btn btn-warning mt-3">Go to Dashboard</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>