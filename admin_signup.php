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
    <meta name="viewport" content="width=device-width, maximum-scale=1.0">
    <title>Admin Sign Up - MMU Hostel Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="signup.css">
</head>
<body>
    <div class="container mt-3 mb-3 signup-container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <!-- Back to top button -->
                <a class="back-to-top" title="Back to top"><i class="fas fa-chevron-up"></i></a>
                
                <div class="card signup-card">
                    <div class="card-header bg-primary text-white text-center">
                        <h3><i class="fas fa-user-shield mr-2"></i>Admin Registration</h3>
                        <p class="mb-0">Join the MMU Hostel management team</p>
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
                        
                        <!-- Progress indicator -->
                        <div class="progress mb-4" style="height: 6px;">
                            <div class="progress-bar bg-primary" id="form-progress" role="progressbar" style="width: 0%"></div>
                        </div>
                        
                        <form action="admin_signup.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate id="signup-form">
                            <!-- Personal Information Section -->
                            <div class="form-section admin-section collapsible-section active" data-section="1">
                                <h4 class="section-header">
                                    <i class="fas fa-user mr-2"></i>Personal Information
                                    <span class="float-right toggle-icon"><i class="fas fa-chevron-down"></i></span>
                                </h4>
                                <div class="section-content">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="name" class="required-field">Full Name</label>
                                                        <input type="text" name="name" id="name" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="gender" class="required-field">Gender</label>
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
                                                        <label for="dob" class="required-field">Date of Birth</label>
                                                        <input type="date" name="dob" id="dob" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="ic_number" class="required-field">IC Number</label>
                                                        <input type="text" name="ic_number" id="ic_number" class="form-control" required>
                                                        <small class="form-text">Format: XXXXXX-XX-XXXX</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="profile-upload-container">
                                                <label for="profile_pic">Profile Picture</label>
                                                <div id="profile-preview-container" class="mt-2">
                                                    <img src="uploads/profile_pics/default.png" class="profile-preview" alt="Profile Preview">
                                                </div>
                                                <input type="file" name="profile_pic" id="profile_pic" class="form-control-file mt-2">
                                                <small class="form-text">Max 2MB (JPG, PNG)</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="citizenship" class="required-field">Citizenship</label>
                                                <select name="citizenship" id="citizenship" class="form-control" required>
                                                    <option value="" disabled selected>Select Citizenship</option>
                                                    <option value="Malaysian">Malaysian</option>
                                                    <option value="Others">Others</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="department" class="required-field">Department</label>
                                                <select name="department" id="department" class="form-control" required>
                                                    <option value="" disabled selected>Select Department</option>
                                                    <option value="Management">Management</option>
                                                    <option value="Maintenance">Maintenance</option>
                                                    <option value="Security">Security</option>
                                                    <option value="Administration">Administration</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="contact_no" class="required-field">Contact Number</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                    </div>
                                                    <input type="text" name="contact_no" id="contact_no" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email" class="required-field">Email</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                    </div>
                                                    <input type="email" name="email" id="email" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="address" class="required-field">Address</label>
                                        <textarea name="address" id="address" class="form-control" rows="2" required></textarea>
                                    </div>
                                    
                                    <div class="text-right mt-3">
                                        <button type="button" class="btn btn-outline-primary next-section" data-next="2">
                                            Next <i class="fas fa-arrow-right ml-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Account Details Section -->
                            <div class="form-section admin-section collapsible-section" data-section="2">
                                <h4 class="section-header">
                                    <i class="fas fa-user-lock mr-2"></i>Account Details
                                    <span class="float-right toggle-icon"><i class="fas fa-chevron-right"></i></span>
                                </h4>
                                <div class="section-content">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="username" class="required-field">Username</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                    </div>
                                                    <input type="text" name="username" id="username" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="password" class="required-field">Password</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                    </div>
                                                    <input type="password" name="password" id="password" class="form-control" required>
                                                </div>
                                                <div class="progress mt-2" style="height: 5px;">
                                                    <div id="password-strength" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="confirm_password" class="required-field">Confirm Password</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                    </div>
                                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                                                </div>
                                                <small id="password-match" class="form-text"></small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group mt-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="terms" name="terms" required>
                                            <label class="custom-control-label" for="terms">I acknowledge that I have read and agree to the terms of service and privacy policy.</label>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mt-4">
                                        <button type="button" class="btn btn-outline-secondary prev-section" data-prev="1">
                                            <i class="fas fa-arrow-left mr-1"></i> Previous
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-signup btn-admin">
                                            <i class="fas fa-user-plus mr-2"></i>Complete Registration
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-nav-links">
                                <p>Already have an account? <a href="admin_login.php">Login here</a></p>
                                <p><a href="index.php"><i class="fas fa-home mr-1"></i>Back to Home</a></p>
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
    <script src="signup.js"></script>
    <script>
        // Section navigation
        document.addEventListener('DOMContentLoaded', function() {
            // Section navigation
            const sections = document.querySelectorAll('.form-section');
            const nextButtons = document.querySelectorAll('.next-section');
            const prevButtons = document.querySelectorAll('.prev-section');
            const progressBar = document.getElementById('form-progress');
            const backToTop = document.querySelector('.back-to-top');
            
            // Show/hide back to top button
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTop.classList.add('visible');
                } else {
                    backToTop.classList.remove('visible');
                }
            });
            
            // Scroll to top when button is clicked
            backToTop.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
            
            // Toggle section content
            document.querySelectorAll('.section-header').forEach(header => {
                header.addEventListener('click', function() {
                    const section = this.closest('.collapsible-section');
                    section.classList.toggle('active');
                    
                    // Update toggle icon
                    const toggleIcon = this.querySelector('.toggle-icon i');
                    if (section.classList.contains('active')) {
                        toggleIcon.className = 'fas fa-chevron-down';
                    } else {
                        toggleIcon.className = 'fas fa-chevron-right';
                    }
                });
            });
            
            // Next button functionality
            nextButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const currentSection = parseInt(this.closest('.form-section').dataset.section);
                    const nextSection = parseInt(this.dataset.next);
                    
                    // Close current section
                    document.querySelector(`.form-section[data-section="${currentSection}"]`).classList.remove('active');
                    document.querySelector(`.form-section[data-section="${currentSection}"] .toggle-icon i`).className = 'fas fa-chevron-right';
                    
                    // Open next section
                    const nextSectionElement = document.querySelector(`.form-section[data-section="${nextSection}"]`);
                    nextSectionElement.classList.add('active');
                    nextSectionElement.querySelector('.toggle-icon i').className = 'fas fa-chevron-down';
                    
                    // Scroll to next section
                    nextSectionElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    
                    // Update progress bar
                    updateProgress(nextSection);
                });
            });
            
            // Previous button functionality
            prevButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const currentSection = parseInt(this.closest('.form-section').dataset.section);
                    const prevSection = parseInt(this.dataset.prev);
                    
                    // Close current section
                    document.querySelector(`.form-section[data-section="${currentSection}"]`).classList.remove('active');
                    document.querySelector(`.form-section[data-section="${currentSection}"] .toggle-icon i`).className = 'fas fa-chevron-right';
                    
                    // Open previous section
                    const prevSectionElement = document.querySelector(`.form-section[data-section="${prevSection}"]`);
                    prevSectionElement.classList.add('active');
                    prevSectionElement.querySelector('.toggle-icon i').className = 'fas fa-chevron-down';
                    
                    // Scroll to previous section
                    prevSectionElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    
                    // Update progress bar
                    updateProgress(prevSection);
                });
            });
            
            // Update progress bar
            function updateProgress(currentSection) {
                const totalSections = sections.length;
                const progress = ((currentSection - 1) / (totalSections - 1)) * 100;
                progressBar.style.width = `${progress}%`;
            }
            
            // Initial progress
            updateProgress(1);
        });
    </script>
</body>
</html>