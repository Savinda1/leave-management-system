<?php
include('../config/config.php');

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/signin.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$showToast = false; 
$toastMessage = ''; // Variable to hold toast message
$toastType = ''; // Variable to hold toast type (success or error)

$profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default.jpg';
$user_name = $_SESSION['name'];

// Handle profile update form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);

    // Profile picture upload handling
    if (!empty($_FILES['profile_picture']['name'])) {
        $file_name = $_FILES['profile_picture']['name'];
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $file_new_name = $user_id . '.' . $file_ext;

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_ext, $allowed_extensions)) {
            $toastMessage = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
            $toastType = 'error';
        } else {
            $upload_dir = '../uploads/profile_pics/';
            if (move_uploaded_file($file_tmp, $upload_dir . $file_new_name)) {
                chmod($upload_dir . $file_new_name, 0644);
                $update_picture_query = "UPDATE users SET profile_picture = '$file_new_name' WHERE id = $user_id";
                if ($conn->query($update_picture_query)) {
                    $_SESSION['profile_picture'] = $file_new_name;
                    $toastMessage = "Profile picture updated successfully!";
                    $toastType = 'success';
                } else {
                    $toastMessage = "Error updating profile picture.";
                    $toastType = 'error';
                }
            } else {
                $toastMessage = "Failed to upload the profile picture.";
                $toastType = 'error';
            }
        }
    }

    // Update the name and email
    $update_query = "UPDATE users SET name = '$name', email = '$email' WHERE id = $user_id";
    if ($conn->query($update_query)) {
        $toastMessage = "Profile updated successfully!";
        $toastType = 'success';
    } else {
        $toastMessage = "Error updating profile.";
        $toastType = 'error';
    }

    // Handle password update
    if (isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['confirm_new_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_new_password = $_POST['confirm_new_password'];

        $user_query = "SELECT password FROM users WHERE id = $user_id";
        $user_result = $conn->query($user_query);
        $user_data = $user_result->fetch_assoc();

        if (password_verify($current_password, $user_data['password'])) {
            if ($new_password === $confirm_new_password) {
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

                $update_password_query = "UPDATE users SET password = '$hashed_new_password' WHERE id = $user_id";
                if ($conn->query($update_password_query)) {
                    $toastMessage = "Password updated successfully!";
                    $toastType = 'success';
                } else {
                    $toastMessage = "Error updating password.";
                    $toastType = 'error';
                }
            } else {
                $toastMessage = "New passwords do not match.";
                $toastType = 'error';
            }
        } else {
            $toastMessage = "Current password is incorrect.";
            $toastType = 'error';
        }
    }
}

// Fetch user details from the database
$query = "SELECT name, email, profile_picture FROM users WHERE id = $user_id";
$result = $conn->query($query);
$user = $result->fetch_assoc();

// If profile picture is updated in the session, use it for display
if (isset($_SESSION['profile_picture'])) {
    $user['profile_picture'] = $_SESSION['profile_picture'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        body {
            overflow: hidden; 
            font-family: 'Arial', sans-serif;
        }

        .container {
            height: 60vh; 
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center; 
            padding: 20px; 
            margin-top: 60px;
        }

        .card {
            width: 100%;
            max-width: 600px; 
            margin: 0 auto;
            flex-grow: 1; 
            border-radius: 10px; 
            border: 1px solid #dee2e6; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
            margin-bottom: 20px; 
            display: flex;
            flex-direction: column;
        }

        .card img {
            max-width: 100%;
            max-height: 200px; 
            object-fit: cover;
        }

        .card-body {
            overflow-y: auto; 
            max-height: 400px; 
        }

        .form-control {
            margin-bottom: 15px;
        }

        
        .toast-container {
            position: fixed;
            top: 20px; 
            right: 20px; 
        }
    </style>
</head>

<header class="d-flex justify-content-between align-items-center p-3 bg-dark text-white">
    <h1 class="ms-3">Update Profile</h1>
    <div class="d-flex align-items-center me-3">
        <img src="../uploads/profile_pics/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; margin-right: 10px;">
        <span><?php echo htmlspecialchars($user_name); ?></span>
    </div>
</header>

<?php include('../includes/sidebar.php'); ?>

<body>
    <div class="container">
        <div class="card mb-3">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="../uploads/profile_pics/<?php echo htmlspecialchars($user['profile_picture']); ?>" class="img-fluid rounded-start" alt="Profile Picture">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title">Update Profile</h5>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">Profile Picture</label>
                                <input type="file" name="profile_picture" class="form-control">
                            </div>

                            <!-- New Password Update Section -->
                            <h5 class="card-title">Change Password</h5>
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_new_password" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div class="toast-container">
            <div id="profileUpdateToast" class="toast <?php echo ($toastType == 'success') ? 'bg-success text-white' : 'bg-danger text-white'; ?>" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto">Notification</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <?php if (!empty($toastMessage)): ?>
                        <?php echo htmlspecialchars($toastMessage); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>



        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Show toast if there's a message
                <?php if (!empty($toastMessage)): ?>
                    var toastElement = document.getElementById('profileUpdateToast');
                    var toast = new bootstrap.Toast(toastElement);
                    toast.show();
                <?php endif; ?>
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </div>
</body>

</html>
