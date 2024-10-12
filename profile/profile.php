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
            echo "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
            exit();
        }

        // Define where to upload the file
        $upload_dir = '../uploads/profile_pics/';
        if (move_uploaded_file($file_tmp, $upload_dir . $file_new_name)) {

            chmod($upload_dir . $file_new_name, 0644);

            $update_picture_query = "UPDATE users SET profile_picture = '$file_new_name' WHERE id = $user_id";
            if ($conn->query($update_picture_query)) {

                $_SESSION['profile_picture'] = $file_new_name;
            }
        } else {
            echo "Failed to upload the profile picture.";
        }
    }

    // Update the name and email
    $update_query = "UPDATE users SET name = '$name', email = '$email' WHERE id = $user_id";
    if ($conn->query($update_query)) {
        
        $showToast = true; 
    } else {
        echo "Error updating profile.";
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
        .card {
            max-width: 700px;
            margin: 0 auto;
        }

        .card img {
            max-width: 100%;
            max-height: 300px;
            object-fit: cover;
        }

        .form-control {
            margin-bottom: 15px;
        }
    </style>
</head>
<header class="d-flex justify-content-between align-items-center p-3 bg-dark text-white">
        <h1 class="ms-3"><?php
                            if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff') {
                                echo "Update Profile";
                            }
                            ?></h1>


        <div class="d-flex align-items-center me-3">
            <img src="../uploads/profile_pics/<?php echo htmlspecialchars($profile_picture); ?>"
                alt="Profile Picture"
                style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; margin-right: 10px;">
            <span><?php echo htmlspecialchars($user_name); ?></span>
        </div>
    </header>

    <?php include('../includes/sidebar.php');?>
<body>
    <div class="container mt-5">
        <h2>Profile</h2>

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
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        
        <!-- Toast Notification -->
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div id="profileUpdateToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        Profile updated successfully!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>



        

       
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($showToast): ?>
                var toastElement = document.getElementById('profileUpdateToast');
                var toast = new bootstrap.Toast(toastElement);
                toast.show();
            <?php endif; ?>
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>