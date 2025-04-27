<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMU Hostel Management System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .role-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .role-card {
            flex: 1;
            margin: 0 10px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .error-message {
            display: none;
            color: red;
            margin-top: 10px;
        }
        .error-message.visible {
            display: block;
        }
        .success-message {
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h1>MMU Hostel Management System</h1>
            <p>Please select your role to continue</p>
        </div>
        
        <div class="role-container">
            <div class="role-card bg-light">
                <h3>Admin</h3>
                <p>Hostel administration and management</p>
                <a href="admin_login.php" class="btn btn-primary btn-block">Login</a>
                <a href="admin_signup.php" class="btn btn-outline-primary btn-block">Sign Up</a>
            </div>
            
            <div class="role-card bg-light">
                <h3>Student</h3>
                <p>Hostel resident access</p>
                <a href="student_login.php" class="btn btn-success btn-block">Login</a>
                <a href="student_signup.php" class="btn btn-outline-success btn-block">Sign Up</a>
            </div>
            
            <div class="role-card bg-light">
                <h3>Visitor</h3>
                <p>Register for a visit</p>
                <a href="visitor_registration.php" class="btn btn-warning btn-block">Register Visit</a>
            </div>
        </div>
        
        <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger mt-3">
            Login failed. Invalid credentials.
        </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['registered'])): ?>
        <div class="alert alert-success mt-3">
            Registration successful! You can now log in.
        </div>
        <?php endif; ?>
        
        <div class="text-center mt-5">
            <p>© 2025 MMU Hostel Management System</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>