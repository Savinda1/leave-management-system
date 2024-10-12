<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    .sidebar {
    height: 100vh;
    width: 250px;
    position: fixed;
    background-color: #343a40;
    color: white;
    padding-top: 20px;
}

.sidebar a {
    text-decoration: none;
    font-size: 18px;
    color: white;
    display: block;
    padding: 10px;
    margin: 5px 0;
    border-bottom: 1px solid #495057;
}

.sidebar a i {
    margin-right: 20px;
    margin-left: 10px;
}

.sidebar a:hover {
    background-color: #495057;
    border-radius: 4px;
}

.sidebar .logout-container {
    display: flex;
    justify-content: flex-start; /* This will align the button to the right */
    padding-left: 20px; /* Add some padding to move it further right */
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
    margin-left: 20px; /* This will move the button to the right */
}


.logout-button:hover {
    background-color: #c82333;
}

</style>
<?php
if ($_SESSION['role'] === 'admin') {
    // Admin-specific navigation
?>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center">Admin</h4>
        <a href="../admin/dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="../leave/review_leave_requests.php"><i class="fa-solid fa-list-check"></i> Pending Requests</a>
        <a href="../leave/leave_request_logs.php"><i class="fa-solid fa-clock-rotate-left"></i> View Leave Logs</a>
        <a href="../profile/profile.php"><i class="fa-solid fa-user"></i> Go to Profile</a><br>
        <div class="logout-container">
        <a href="../auth/logout.php" class="logout-button"><i class="fa-solid fa-sign-out-alt" style="margin-right: 10px;"></i> Logout</a>
        </div>
        
    </div>
<?php
} else {
    // Staff-specific navigation
?>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center">Staff</h4>
        <a href="../staff/dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="../leave/apply_for_leave.php"><i class="fa-solid fa-plus-circle"></i> Apply For Leave</a>
        <a href="../leave/leave_request_logs_for_staff.php"><i class="fa-solid fa-clock-rotate-left"></i> View Leave Logs</a>
        <a href="../profile/profile.php"><i class="fa-solid fa-user"></i> Go to Profile</a><br>
        <div class="logout-container">
        <a href="../auth/logout.php" class="logout-button"><i class="fa-solid fa-sign-out-alt" style="margin-right: 10px;"></i> Logout</a>
        </div>    
    </div>
<?php
}
?>