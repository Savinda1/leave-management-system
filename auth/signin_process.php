<?php
session_start();
include '../config/config.php'; // Include database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user exists in the database
    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // If user exists, verify the password
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password, $role);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Password is correct; set session variables
            $_SESSION['user_id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;

            // Redirect to dashboard based on role
            if ($role == 'admin') {
                echo "Welcome administration";
                // header("Location: admin/dashboard.php");
            } else {
                echo "Welcome staff";
                // header("Location: staff/dashboard.php");
            }
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No user found with this email.";
    }

    $stmt->close();
    $conn->close();
}
?>
