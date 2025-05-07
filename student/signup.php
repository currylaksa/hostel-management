<?php
session_start();
require_once '../shared/includes/db_connection.php';

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form data
    $name = $_POST['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $student_id = $_POST['student_id'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $faculty = $_POST['faculty'] ?? '';
    $program = $_POST['program'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate form data
    if (empty($name)) $errors[] = "Name is required";
    if (empty($username)) $errors[] = "Username is required";
    if (empty($student_id)) $errors[] = "Student ID is required";
    if (empty($email)) $errors[] = "Email is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (empty($password)) $errors[] = "Password is required";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match";
    
    // Check if username already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM students WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Username already exists";
        }
    }
    
    // Check if student ID already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Student ID already exists";
        }
    }
    
    // Check if email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Email already exists";
        }
    }
    
    // If no errors, register student
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO students (name, username, student_id, email, phone, faculty, program, password, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("ssssssss", $name, $username, $student_id, $email, $phone, $faculty, $program, $hashed_password);
        
        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Sign Up - MMU Hostel Management System</title>
    <link rel="stylesheet" href="../shared/css/style.css">
    <link rel="stylesheet" href="css/signup.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="text-center">Student Sign Up</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <h4><i class="fas fa-check-circle mr-2"></i>Registration Successful!</h4>
                                <p>Your account has been created and is pending approval by the administration.</p>
                                <p>You will be able to login once your account is approved.</p>
                                <div class="text-center mt-3">
                                    <a href="login.php" class="btn btn-outline-success">Go to Login</a>
                                    <a href="../index.php" class="btn btn-outline-secondary ml-2">Back to Home</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <form action="signup.php" method="POST">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="name">Full Name</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            </div>
                                            <input type="text" name="name" id="name" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="username">Username</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user-graduate"></i></span>
                                            </div>
                                            <input type="text" name="username" id="username" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="student_id">Student ID</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                            </div>
                                            <input type="text" name="student_id" id="student_id" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="email">Email</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            </div>
                                            <input type="email" name="email" id="email" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="phone">Phone Number</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            </div>
                                            <input type="text" name="phone" id="phone" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="faculty">Faculty</label>
                                        <select name="faculty" id="faculty" class="form-control">
                                            <option value="" selected disabled>Select Faculty</option>
                                            <option value="Faculty of Computing and Informatics">Faculty of Computing and Informatics</option>
                                            <option value="Faculty of Engineering">Faculty of Engineering</option>
                                            <option value="Faculty of Management">Faculty of Management</option>
                                            <option value="Faculty of Creative Multimedia">Faculty of Creative Multimedia</option>
                                            <option value="Faculty of Applied Communication">Faculty of Applied Communication</option>
                                            <option value="Faculty of Information Science & Technology">Faculty of Information Science & Technology</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="program">Program (Course)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                                        </div>
                                        <input type="text" name="program" id="program" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="password">Password</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            </div>
                                            <input type="password" name="password" id="password" class="form-control" required>
                                        </div>
                                        <div class="password-strength"></div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="confirm_password">Confirm Password</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            </div>
                                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="terms" name="terms" required>
                                        <label class="custom-control-label" for="terms">I agree to the <a href="#">Terms and Conditions</a></label>
                                    </div>
                                </div>
                                
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-success btn-lg">Sign Up</button>
                                </div>
                                
                                <div class="text-center">
                                    <p>Already have an account? <a href="login.php">Login</a></p>
                                    <p><a href="../index.php">Back to Home</a></p>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../shared/js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.querySelector('.password-strength');
            
            // Remove all classes
            strengthBar.classList.remove('weak', 'medium', 'strong', 'very-strong');
            
            if (password.length > 0) {
                let strength = 0;
                
                // Check password length
                if (password.length >= 8) strength += 1;
                
                // Check for mixed case
                if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;
                
                // Check for numbers
                if (password.match(/\d/)) strength += 1;
                
                // Check for special characters
                if (password.match(/[^a-zA-Z\d]/)) strength += 1;
                
                // Update strength bar
                switch (strength) {
                    case 1:
                        strengthBar.classList.add('weak');
                        break;
                    case 2:
                        strengthBar.classList.add('medium');
                        break;
                    case 3:
                        strengthBar.classList.add('strong');
                        break;
                    case 4:
                        strengthBar.classList.add('very-strong');
                        break;
                }
            }
        });
    </script>
</body>
</html>