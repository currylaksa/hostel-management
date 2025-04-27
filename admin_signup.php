<?php
session_start();
require_once 'db_connection.php';

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form data
    $name = $_POST['name'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $ic_number = $_POST['ic_number'] ?? '';
    $contact_no = $_POST['contact_no'] ?? '';
    $email = $_POST['email'] ?? '';
    $citizenship = $_POST['citizenship'] ?? '';
    $address = $_POST['address'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate form data
    if (empty($name)) $errors[] = "Name is required";
    if (empty($gender)) $errors[] = "Gender is required";
    if (empty($dob)) $errors[] = "Date of birth is required";
    if (empty($ic_number)) $errors[] = "IC number is required";
    if (empty($contact_no)) $errors[] = "Contact number is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($citizenship)) $errors[] = "Citizenship is required";
    if (empty($address)) $errors[] = "Address is required";
    if (empty($username)) $errors[] = "Username is required";
    if (empty($password)) $errors[] = "Password is required";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match";
    
    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? OR email = ? OR ic_number = ?");
    $stmt->bind_param("sss", $username, $email, $ic_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Username, email, or IC number already exists";
    }
    
    // Handle profile picture upload
    $profile_pic = null;
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['profile_pic']['name'];
        $file_tmp = $_FILES['profile_pic']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (in_array($file_ext, $allowed)) {
            $new_file_name = uniqid('admin_') . '.' . $file_ext;
            $upload_path = 'uploads/profile_pics/' . $new_file_name;
            
            // Create directory if it doesn't exist
            if (!file_exists('uploads/profile_pics/')) {
                mkdir('uploads/profile_pics/', 0777, true);
            }
            
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $profile_pic = $upload_path;
            } else {
                $errors[] = "Failed to upload profile picture";
            }
        } else {
            $errors[] = "Invalid file format. Only JPG, JPEG, PNG and GIF are allowed";
        }
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO admins (name, gender, dob, ic_number, contact_no, email, citizenship, address, username, password, profile_pic) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $name, $gender, $dob, $ic_number, $contact_no, $email, $citizenship, $address, $username, $hashed_password, $profile_pic);
        
        if ($stmt->execute()) {
            $success = true;
            // Redirect after successful registration
            header("Location: admin_login.php?registered=1");
            exit();
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
    <title>Admin Sign Up - MMU Hostel Management System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="text-center">Admin Registration</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form action="admin_signup.php" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Full Name</label>
                                        <input type="text" name="name" id="name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        <select name="gender" id="gender" class="form-control" required>
                                            <option value="" disabled selected>Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dob">Date of Birth</label>
                                        <input type="date" name="dob" id="dob" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ic_number">IC Number</label>
                                        <input type="text" name="ic_number" id="ic_number" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_no">Contact Number</label>
                                        <input type="text" name="contact_no" id="contact_no" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" id="email" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="citizenship">Citizenship</label>
                                        <select name="citizenship" id="citizenship" class="form-control" required>
                                            <option value="" disabled selected>Select Citizenship</option>
                                            <option value="Malaysian">Malaysian</option>
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="profile_pic">Profile Picture</label>
                                        <input type="file" name="profile_pic" id="profile_pic" class="form-control-file">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea name="address" id="address" class="form-control" rows="3" required></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" name="username" id="username" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" name="password" id="password" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="confirm_password">Confirm Password</label>
                                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary btn-lg">Sign Up</button>
                            </div>
                            
                            <div class="text-center">
                                <p>Already have an account? <a href="admin_login.php">Login here</a></p>
                                <p><a href="index.html">Back to Home</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>