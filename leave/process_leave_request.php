<?php
session_start();
include('../config/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $leave_id = $_POST['leave_id'];
    $action = $_POST['action'];

    // Update the leave request status
    if ($action == 'approve') {
        $query = "UPDATE leave_requests SET status = 'approved' WHERE id = ?";
        $_SESSION['toast_message'] = 'approved';
    } elseif ($action == 'reject') {
        $query = "UPDATE leave_requests SET status = 'rejected' WHERE id = ?";
        $_SESSION['toast_message'] = 'rejected';
    }

    // Prepare and execute the status update
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $leave_id);
    $stmt->execute();
    $stmt->close();

    // Fetch the user_id of the staff who made the request
    $query = "SELECT user_id FROM leave_requests WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $leave_id);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    // Add notification for the staff member
    if ($action == 'approve') {
        $message = 'Your leave request has been approved.';
    } elseif ($action == 'reject') {
        $message = 'Your leave request has been rejected.';
    }

    // Insert notification for staff with is_read and created_at fields
    $is_read = 0; // Notification is unread by default
    $created_at = date("Y-m-d H:i:s"); // Timestamp for when the notification is created

    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $user_id, $message, $is_read, $created_at);
    $stmt->execute();
    $stmt->close();

    // Close the database connection
    $conn->close();

    // Redirect back to the pending requests page
    header('Location: review_leave_requests.php');
    exit();
}
?>
