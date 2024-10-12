<?php
session_start();
include('../config/config.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access."]);
    exit();
}

$user_id = $_SESSION['role'] == 'admin' ? 0 : $_SESSION['user_id']; 


error_log("User ID: " . $user_id);


$query = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Failed to prepare the statement: " . $conn->error]);
    exit();
}

$stmt->bind_param("i", $user_id);

// Execute the statement
if ($stmt->execute()) {
    // Check how many rows were affected
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => $stmt->affected_rows . " notifications marked as read."]);
    } else {
        echo json_encode(["success" => false, "message" => "No unread notifications found."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Failed to execute the statement: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
