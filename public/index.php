<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Management System</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            text-align: center;
            padding: 50px;
        }

        header {
            background-color: #343a40;
            color: white;
            padding: 20px;
        }

        header h1 {
            margin: 0;
        }

        .description {
            margin: 20px 0;
            font-size: 1.2rem;
        }

        .buttons {
            margin-top: 30px;
        }

        .buttons a {
            text-decoration: none;
            color: white;
            padding: 10px 20px;
            background-color: #007bff;
            border-radius: 5px;
            margin: 10px;
            display: inline-block;
        }

        .buttons a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <header>
        <h1>Welcome to the Leave Management System</h1>
    </header>

    <div class="container">
        <p class="description">
            Our Leave Management System is designed to streamline the process of requesting, managing, and tracking employee leave. 
            Whether you are applying for leave or managing employee requests, this platform offers an easy-to-use interface for all your leave management needs.
        </p>

        <div class="buttons">
            <a href="http://localhost/leave_management/auth/signup.php">Sign Up</a>
            <a href="http://localhost/leave_management/auth/signin.php">Sign In</a>
        </div>
    </div>

</body>
</html>
