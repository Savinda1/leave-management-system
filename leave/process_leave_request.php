<?php
session_start();
include('../config/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $leave_id = $_POST['leave_id'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        $query = "UPDATE leave_requests SET status = 'approved' WHERE id = ?";
        $_SESSION['toast_message'] = 'approved';
    } elseif ($action == 'reject') {
        $query = "UPDATE leave_requests SET status = 'rejected' WHERE id = ?";
        $_SESSION['toast_message'] = 'rejected';
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $leave_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    // Redirect back to the pending requests page
    header('Location: review_leave_requests.php');
    exit();
}
?>
