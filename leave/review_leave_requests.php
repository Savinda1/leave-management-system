<?php
session_start();
include('../config/config.php');
$profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default.jpg';
$user_name = $_SESSION['name'];
?>
<header class="d-flex justify-content-between align-items-center p-3 bg-dark text-white">
    <h1 class="ms-3">Review Leave Request Logs</h1>
    <div class="d-flex align-items-center me-3">
        <img src="../uploads/profile_pics/<?php echo htmlspecialchars($profile_picture); ?>"
            alt="Profile Picture"
            style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; margin-right: 10px;">
        <span><?php echo htmlspecialchars($user_name); ?></span>
    </div>
</header>

<?php
include('../includes/sidebar.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/signin.php');
    exit();
}



// Fetch pending leave requests using prepared statements
$stmt = $conn->prepare("SELECT leave_requests.id, users.name AS name, leave_requests.leave_type, leave_requests.start_date, leave_requests.end_date, leave_requests.reason 
                         FROM leave_requests 
                         JOIN users ON leave_requests.user_id = users.id 
                         WHERE leave_requests.status = ?");
$status = 'pending';
$stmt->bind_param("s", $status);
$stmt->execute();
$result = $stmt->get_result();

// Toast message check
$toastMessage = isset($_SESSION['toast_message']) ? $_SESSION['toast_message'] : null;
unset($_SESSION['toast_message']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Leave Requests - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            margin-left: 260px;
            padding: 30px;
        }

        .card {
            margin-bottom: 20px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-buttons form {
            display: inline;
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1055;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                margin-left: 0;
                padding: 15px;
            }
        }
    </style>
</head>


<body>



    <div class="container">
        <h1 class="text-center">Pending Leave Requests</h1>

        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                <p class="card-text">
                                    <strong>Leave Type:</strong> <?php echo htmlspecialchars($row['leave_type']); ?><br>
                                    <strong>Start Date:</strong> <?php echo htmlspecialchars($row['start_date']); ?><br>
                                    <strong>End Date:</strong> <?php echo htmlspecialchars($row['end_date']); ?><br>
                                    <strong>Reason:</strong> <?php echo htmlspecialchars($row['reason']); ?><br>
                                </p>
                                <div class="action-buttons">
                                    <!-- Approve Request -->
                                    <form action="process_leave_request.php" method="post" onsubmit="return confirm('Are you sure you want to approve this leave request?');">
                                        <input type="hidden" name="leave_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button class="btn btn-success" type="submit">Approve</button>
                                    </form>
                                    <!-- Reject Request -->
                                    <form action="process_leave_request.php" method="post" onsubmit="return confirm('Are you sure you want to reject this leave request?');">
                                        <input type="hidden" name="leave_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button class="btn btn-danger" type="submit">Reject</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p><b>No pending leave requests found.</b></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="approveToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">Leave request approved!</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>

        <div id="rejectToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">Leave request rejected!</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show the appropriate toast message based on the session variable
            <?php if ($toastMessage === 'approved'): ?>
                var approveToast = new bootstrap.Toast(document.getElementById('approveToast'));
                approveToast.show();
            <?php elseif ($toastMessage === 'rejected'): ?>
                var rejectToast = new bootstrap.Toast(document.getElementById('rejectToast'));
                rejectToast.show();
            <?php endif; ?>
        });
    </script>

</body>

</html>