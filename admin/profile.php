<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit();
}

// Reset form data if returning to the page or after form submission
if (isset($_GET['reset']) && $_GET['reset'] == 'true') {
    // Clear any form data in session
    unset($_SESSION['form_data']);
    unset($_SESSION['form_submitted']);
    // Clear any potential leftover form fields
    $_SESSION['form_reset'] = true;
    
    // Remove the reset parameter from URL after processing
    $redirect_url = strtok($_SERVER["REQUEST_URI"], '?');
    header("Location: $redirect_url");
    exit();
}

// Handle profile update redirection
if (isset($_SESSION['form_submitted']) && $_SESSION['form_submitted'] === true) {
    // Clear the flag
    unset($_SESSION['form_submitted']);
    
    // Add a success message
    $_SESSION["profile_message"] = "Profile updated successfully!";
    $_SESSION["profile_message_type"] = "success";
    
    // Redirect to reset the page and prevent form resubmission
    header("Location: profile.php?reset=true");
    exit();
}

require_once "../shared/includes/db_connection.php";

// Handle form submission first
$message = "";
$messageType = "";

// Always fetch the latest data from the database first
$username = $_SESSION["user"];
$sql = "SELECT * FROM admins WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$name = "";
$email = "";
$phone = "";
$office_number = "";
$profile_picture = "";
$admin = null;

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    $name = $admin["name"];
    $email = $admin["email"];
    $phone = $admin["contact_no"];
    $office_number = $admin["office_number"];
    $profile_picture = $admin["profile_pic"];
    
    // Update session with the name from database
    $_SESSION["fullname"] = $name;
}

$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add a log to ensure we're getting POST data
    error_log("POST data received: " . json_encode($_POST));
    
    // Update profile information
    if (isset($_POST["update_profile"])) {
        // Sanitize input data
        $name = trim($_POST["name"]);
        $email = trim($_POST["email"]);
        $phone = trim($_POST["contact_no"]);
        $office_number = trim($_POST["office_number"]);
        $username = $_SESSION["user"];
        
        // Basic validation
        $is_valid = true;
        
        if(empty($name)) {
            $_SESSION["profile_message"] = "Name cannot be empty";
            $_SESSION["profile_message_type"] = "error";
            $is_valid = false;
        }
        
        if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["profile_message"] = "Please provide a valid email address";
            $_SESSION["profile_message_type"] = "error";
            $is_valid = false;
        }
        
        if(empty($phone)) {
            $_SESSION["profile_message"] = "Phone number cannot be empty";
            $_SESSION["profile_message_type"] = "error";
            $is_valid = false;
        }
        
        if(!$is_valid) {
            header("Location: profile.php");
            exit();
        }
        
        // First check if the email already exists for another user
        $email_check_sql = "SELECT * FROM admins WHERE email = ? AND username != ?";
        $email_check_stmt = $conn->prepare($email_check_sql);
        $email_check_stmt->bind_param("ss", $email, $username);
        $email_check_stmt->execute();
        $email_check_result = $email_check_stmt->get_result();
        
        if ($email_check_result->num_rows > 0) {
            $_SESSION["profile_message"] = "Email address already in use by another admin. Please use a different email.";
            $_SESSION["profile_message_type"] = "error";
        } else {
            // Now check if admin record exists
            $check_sql = "SELECT * FROM admins WHERE username = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("s", $username);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
              if ($check_result->num_rows > 0) {
                // Update existing record
                $update_sql = "UPDATE admins SET name = ?, email = ?, contact_no = ?, office_number = ? WHERE username = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("sssss", $name, $email, $phone, $office_number, $username);
                
                if ($update_stmt->execute()) {
                    // Update session with the new name
                    $_SESSION["fullname"] = $name;
                    $_SESSION["profile_image"] = $admin["profile_pic"]; // Ensure profile image is consistent
                    
                    // Set flag to indicate successful submission
                    $_SESSION["form_submitted"] = true;
                    
                    // Add detailed message for debugging
                    $_SESSION["profile_message"] = "Profile updated successfully! Name: $name, Email: $email, Phone: $phone";
                    $_SESSION["profile_message_type"] = "success";
                    
                    // Log successful update
                    error_log("Admin profile updated successfully for user: $username");
                } else {
                    $_SESSION["profile_message"] = "Error updating profile: " . $conn->error;
                    $_SESSION["profile_message_type"] = "error";
                    error_log("Error updating admin profile for user $username: " . $conn->error);
                }
                
                // Clear statement
                $update_stmt->close();
            } else {
                // Insert new record since it doesn't exist
                $insert_sql = "INSERT INTO admins (name, email, contact_no, office_number, username) 
                              VALUES (?, ?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("sssss", $name, $email, $phone, $office_number, $username);
                
                if ($insert_stmt->execute()) {
                    // Update session with the new name
                    $_SESSION["fullname"] = $name;
                    
                    // Set flag to indicate successful submission
                    $_SESSION["form_submitted"] = true;
                    
                    $_SESSION["profile_message"] = "Profile created successfully! Name: $name, Email: $email";
                    $_SESSION["profile_message_type"] = "success";
                } else {
                    $_SESSION["profile_message"] = "Error creating profile: " . $conn->error;
                    $_SESSION["profile_message_type"] = "error";
                }
                
                // Clear statement
                $insert_stmt->close();
            }
            
            $check_stmt->close();
              // Redirect after form submission to prevent resubmission and apply the reset
            header("Location: profile.php?reset=true");
            exit();
        }
        
        $email_check_stmt->close();
    }
    
    // Handle profile picture upload
    if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
        $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png"];
        $filename = $_FILES["profile_picture"]["name"];
        $filetype = $_FILES["profile_picture"]["type"];
        $filesize = $_FILES["profile_picture"]["size"];
        $username = $_SESSION["user"];
        
        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            $_SESSION["profile_message"] = "Error: Please select a valid file format (JPG, JPEG, PNG).";
            $_SESSION["profile_message_type"] = "error";
        } else {
            // Verify file size - 5MB maximum
            $maxsize = 5 * 1024 * 1024;
            if ($filesize > $maxsize) {
                $_SESSION["profile_message"] = "Error: File size is larger than the allowed limit (5MB).";
                $_SESSION["profile_message_type"] = "error";
            } else {
                // Verify MIME type
                if (in_array($filetype, $allowed)) {                    // Generate unique filename
                    $new_filename = "admin_" . $username . "_" . time() . "." . $ext;
                    $upload_dir = "uploads/profile_pictures/";
                    
                    // Create directory if it doesn't exist - make the path absolute
                    $absolute_upload_dir = __DIR__ . "/" . $upload_dir;
                    if (!file_exists($absolute_upload_dir)) {
                        mkdir($absolute_upload_dir, 0777, true);
                    }
                      // Move the file
                    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $absolute_upload_dir . $new_filename)) {
                        // Update database with new profile picture path
                        $profile_pic_sql = "UPDATE admins SET profile_pic = ? WHERE username = ?";
                        $profile_pic_stmt = $conn->prepare($profile_pic_sql);
                        $profile_pic_path = $upload_dir . $new_filename; // Store the relative path in database
                        $profile_pic_stmt->bind_param("ss", $profile_pic_path, $username);
                        
                        if ($profile_pic_stmt->execute()) {
                            $_SESSION["profile_message"] = "Profile picture updated successfully!";
                            $_SESSION["profile_message_type"] = "success";
                            // Set the profile image in session with the full path
                            $_SESSION["profile_image"] = $profile_pic_path;
                        } else {
                            $_SESSION["profile_message"] = "Error updating profile picture in database: " . $conn->error;
                            $_SESSION["profile_message_type"] = "error";
                        }
                    } else {
                        $_SESSION["profile_message"] = "Error uploading file.";
                        $_SESSION["profile_message_type"] = "error";
                    }
                } else {
                    $_SESSION["profile_message"] = "Error: There was a problem with the uploaded file.";
                    $_SESSION["profile_message_type"] = "error";
                }
            }
        }
          // Redirect after profile picture upload to avoid resubmission
        header("Location: profile.php?reset=true");
        exit();
    }
      // Handle password change
    if (isset($_POST["change_password"])) {
        $current_password = $_POST["current_password"];
        $new_password = $_POST["new_password"];
        $confirm_password = $_POST["confirm_password"];
        $username = $_SESSION["user"];
        
        // Basic validation
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $_SESSION["profile_message"] = "All password fields are required!";
            $_SESSION["profile_message_type"] = "error";
            header("Location: profile.php?reset=true");
            exit();
        }
        
        if ($new_password !== $confirm_password) {
            $_SESSION["profile_message"] = "New password and confirmation do not match!";
            $_SESSION["profile_message_type"] = "error";
            header("Location: profile.php?reset=true");
            exit();
        }
        
        // Verify the current password
        $password_sql = "SELECT password FROM admins WHERE username = ?";
        $password_stmt = $conn->prepare($password_sql);
        $password_stmt->bind_param("s", $username);
        $password_stmt->execute();
        $password_result = $password_stmt->get_result();
        
        if ($password_result->num_rows > 0) {
            $user_data = $password_result->fetch_assoc();
            $stored_password = $user_data["password"];
            
            // Verify current password
            if (password_verify($current_password, $stored_password)) {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update the password
                $update_password_sql = "UPDATE admins SET password = ? WHERE username = ?";
                $update_password_stmt = $conn->prepare($update_password_sql);
                $update_password_stmt->bind_param("ss", $hashed_password, $username);
                
                if ($update_password_stmt->execute()) {
                    $_SESSION["profile_message"] = "Password changed successfully!";
                    $_SESSION["profile_message_type"] = "success";
                    error_log("Password changed successfully for admin: $username");
                } else {
                    $_SESSION["profile_message"] = "Error updating password: " . $conn->error;
                    $_SESSION["profile_message_type"] = "error";
                    error_log("Error updating password for admin $username: " . $conn->error);
                }
                
                $update_password_stmt->close();
            } else {
                $_SESSION["profile_message"] = "Current password is incorrect!";
                $_SESSION["profile_message_type"] = "error";
            }
        } else {
            $_SESSION["profile_message"] = "User not found!";
            $_SESSION["profile_message_type"] = "error";
        }
        
        $password_stmt->close();
          // Redirect after password change to avoid resubmission
        header("Location: profile.php?reset=true");
        exit();
    }
}

// Check if there's a message in session and display it, then clear it
$message = "";
$messageType = "";
if (isset($_SESSION["profile_message"])) {
    $message = $_SESSION["profile_message"];
    $messageType = $_SESSION["profile_message_type"];
    
    // Clear the message from session after displaying it once
    unset($_SESSION["profile_message"]);
    unset($_SESSION["profile_message_type"]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMU Hostel Management - Admin Profile</title>    <link rel="stylesheet" href="../shared/css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>MMU Hostel</h2>
                <p>Admin Portal</p>
            </div>            <div class="sidebar-menu">
                <a href="dashboard.php" class="menu-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                
                <div class="menu-category">Student Management</div>
                <a href="#" class="menu-item">
                    <i class="fas fa-user-graduate"></i> Students
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-clipboard-list"></i> Applications
                </a>
                
                <div class="menu-category">Accommodation</div>
                <a href="#" class="menu-item">
                    <i class="fas fa-building"></i> Hostel Blocks
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-door-open"></i> Rooms
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-bed"></i> Room Allocation
                </a>
                
                <div class="menu-category">Operations</div>
                <a href="#" class="menu-item">
                    <i class="fas fa-file-invoice-dollar"></i> Billing
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-hand-holding-usd"></i> Payments
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-tools"></i> Maintenance
                </a>
                
                <div class="menu-category">Communication</div>
                <a href="#" class="menu-item">
                    <i class="fas fa-bullhorn"></i> Announcements
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-envelope"></i> Messages
                </a>
                  <div class="menu-category">Admin</div>
                <a href="profile.php?reset=true" class="menu-item active">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-user-shield"></i> Staff
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Admin Profile</h1>
                <div class="user-info">
                    <?php                    // Get the profile image path from the database or use a default
                    if (isset($admin['profile_pic']) && !empty($admin['profile_pic'])) {
                        $profile_image = $admin['profile_pic'];
                        // Make sure the path is correct
                        if (!file_exists($profile_image) && file_exists("../" . $profile_image)) {
                            $profile_image = "../" . $profile_image;
                        }
                    } else {
                        // Use a default profile image that definitely exists
                        if (file_exists("uploads/profile_pictures/default_admin.png")) {
                            $profile_image = "uploads/profile_pictures/default_admin.png";
                        } else if (file_exists("../uploads/profile_pictures/default_admin.png")) {
                            $profile_image = "../uploads/profile_pictures/default_admin.png";
                        } else {
                            // If no default image exists, use a placeholder
                            $profile_image = "https://via.placeholder.com/150";
                        }
                    }
                    
                    // Update the session variable to ensure consistency across pages
                    $_SESSION["profile_image"] = $profile_image;
                    ?>
                    <img src="<?php echo $profile_image; ?>" alt="Admin Profile">
                    <span class="user-name"><?php echo $_SESSION["fullname"] ?? $_SESSION["user"]; ?></span>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>

            <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>" id="alert-message">
                <?php echo $message; ?>
                <button type="button" class="close-btn" onclick="closeAlert()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php endif; ?>
            
            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-picture-container">
                        <img src="<?php echo $profile_image; ?>" alt="Profile Picture" class="profile-picture">
                        <div class="profile-picture-edit" id="changeProfilePicture">
                            <i class="fas fa-camera"></i>
                        </div>
                        <form id="profilePictureForm" action="" method="post" enctype="multipart/form-data">
                            <input type="file" name="profile_picture" id="profilePictureInput" accept="image/*">
                            <input type="submit" value="Upload">
                        </form>
                    </div>
                    <div class="profile-info">
                    <h2><?php echo isset($admin['name']) ? $admin['name'] : $_SESSION["user"]; ?></h2>
                    <p><i class="fas fa-user-shield"></i> <?php echo ucfirst($_SESSION["role"]); ?></p>
                    <p><i class="fas fa-envelope"></i> <?php echo isset($admin['email']) && !empty($admin['email']) ? $admin['email'] : 'No email set'; ?></p>
                    <p><i class="fas fa-phone"></i> <?php echo isset($admin['contact_no']) && !empty($admin['contact_no']) ? $admin['contact_no'] : 'No phone set'; ?></p>
                    <p><i class="fas fa-building"></i> Office: <?php echo isset($admin['office_number']) && !empty($admin['office_number']) ? $admin['office_number'] : 'No office set'; ?></p>
                </div>
                </div>

                <div class="profile-tabs">
                    <div class="profile-tab active" data-tab="edit-profile">
                        <i class="fas fa-user-edit"></i> Edit Profile
                    </div>
                    <div class="profile-tab" data-tab="security">
                        <i class="fas fa-lock"></i> Security
                    </div>
                </div>

                <div class="tab-content active" id="edit-profile">
                    <div class="form-section-header">
                        <h3>Update your profile information below</h3>
                        <p>Fill in only the fields you want to update and click "Save Changes" when you're done.</p>
                    </div>                    <form action="" method="post">
                        <div class="form-section">
                            <h3><i class="fas fa-user"></i> Personal Information</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" placeholder="Enter your full name" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" placeholder="Enter your email address" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3><i class="fas fa-phone"></i> Contact Information</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="contact_no" value="<?php echo $phone; ?>" placeholder="Enter your phone number" required>
                                </div>
                                <div class="form-group">
                                    <label for="office_number">Office Number</label>
                                    <input type="text" class="form-control" id="office_number" name="office_number" value="<?php echo $office_number; ?>" placeholder="Enter your office number">
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3><i class="fas fa-user-shield"></i> Account Information</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="admin_username">Username</label>
                                    <input type="text" class="form-control" id="admin_username" name="username" value="<?php echo $_SESSION["user"]; ?>" placeholder="Enter your username" readonly>
                                    <small class="form-text text-muted">Username cannot be changed</small>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                            <input type="hidden" name="update_profile" value="1">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Profile Changes
                            </button>
                        </div>
                    </form>
                </div>                <div class="tab-content" id="security">
                    <div class="form-section">
                        <h3><i class="fas fa-key"></i> Change Password</h3>
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    <small class="form-text text-muted">Password should be at least 8 characters</small>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                            <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                                <input type="hidden" name="change_password" value="1">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key"></i> Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabs = document.querySelectorAll('.profile-tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Clear form values if switching tabs to prevent data persistence
                    const activeTabId = document.querySelector('.profile-tab.active').getAttribute('data-tab');
                    const clickedTabId = this.getAttribute('data-tab');
                    
                    // Only reset forms if we're actually changing tabs
                    if (activeTabId !== clickedTabId) {
                        // Reset forms in the tab we're leaving
                        const activeForms = document.querySelectorAll(`#${activeTabId} form`);
                        activeForms.forEach(form => {
                            form.reset();
                        });
                        
                        // Also clear password fields for security
                        const passwordFields = document.querySelectorAll(`#${activeTabId} input[type="password"]`);
                        passwordFields.forEach(field => {
                            field.value = '';
                        });
                    }
                    
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    const tabContentId = this.getAttribute('data-tab');
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    document.getElementById(tabContentId).classList.add('active');
                });
            });
            
            // Add form validation for profile update
            const profileForm = document.querySelector('#edit-profile form');
            if (profileForm) {
                profileForm.addEventListener('submit', function(e) {
                    const nameField = document.getElementById('name');
                    const emailField = document.getElementById('email');
                    const phoneField = document.getElementById('phone');
                    
                    let isValid = true;
                    
                    // Validate name
                    if (!nameField.value.trim()) {
                        alert('Please enter your full name');
                        nameField.focus();
                        isValid = false;
                        e.preventDefault();
                        return;
                    }
                    
                    // Validate email
                    if (!emailField.value.trim() || !emailField.value.includes('@')) {
                        alert('Please enter a valid email address');
                        emailField.focus();
                        isValid = false;
                        e.preventDefault();
                        return;
                    }
                    
                    // Validate phone
                    if (!phoneField.value.trim()) {
                        alert('Please enter your phone number');
                        phoneField.focus();
                        isValid = false;
                        e.preventDefault();
                        return;
                    }
                    
                    // Disable the submit button to prevent double submission
                    if (isValid) {
                        const submitButton = this.querySelector('button[type="submit"]');
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                    }
                });
            }
            
            // Add form validation for password change
            const passwordForm = document.querySelector('#security form');
            if (passwordForm) {
                passwordForm.addEventListener('submit', function(e) {
                    const currentPassword = document.getElementById('current_password');
                    const newPassword = document.getElementById('new_password');
                    const confirmPassword = document.getElementById('confirm_password');
                    
                    // Validate current password
                    if (!currentPassword.value.trim()) {
                        alert('Please enter your current password');
                        currentPassword.focus();
                        e.preventDefault();
                        return;
                    }
                    
                    // Validate new password
                    if (!newPassword.value.trim()) {
                        alert('Please enter your new password');
                        newPassword.focus();
                        e.preventDefault();
                        return;
                    }
                    
                    // Check password length
                    if (newPassword.value.length < 8) {
                        alert('Password should be at least 8 characters');
                        newPassword.focus();
                        e.preventDefault();
                        return;
                    }
                    
                    // Validate confirmation
                    if (newPassword.value !== confirmPassword.value) {
                        alert('New password and confirmation do not match');
                        confirmPassword.focus();
                        e.preventDefault();
                        return;
                    }
                    
                    // Disable the submit button to prevent double submission
                    const submitButton = this.querySelector('button[type="submit"]');
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                });
            }
            
            // Profile picture upload
            const changeProfilePictureBtn = document.getElementById('changeProfilePicture');
            const profilePictureInput = document.getElementById('profilePictureInput');
            
            changeProfilePictureBtn.addEventListener('click', function() {
                profilePictureInput.click();
            });
            
            profilePictureInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    // Auto-submit the form when a file is selected
                    document.getElementById('profilePictureForm').submit();
                }
            });
            
            // Auto-hide alert messages after 5 seconds
            const alertMessage = document.getElementById('alert-message');
            if (alertMessage) {
                setTimeout(function() {
                    alertMessage.style.opacity = '0';
                    setTimeout(function() {
                        alertMessage.style.display = 'none';
                    }, 500);
                }, 5000);
            }
            
            // Function to close alert messages
            window.closeAlert = function() {
                const alert = document.getElementById('alert-message');
                if (alert) {
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                }
            }
            
            // Clear form fields on page refresh or reload
            <?php if (isset($_SESSION['form_reset']) && $_SESSION['form_reset']): ?>
            // Reset all form fields
            document.querySelectorAll('form').forEach(form => {
                form.reset();
            });
            <?php 
                // Clear the reset flag
                $_SESSION['form_reset'] = false;
            endif; 
            ?>
            
            // Add form submit event listeners to prevent double submission
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    // Disable submit buttons after click to prevent double submission
                    const submitButtons = this.querySelectorAll('button[type="submit"]');
                    submitButtons.forEach(button => {
                        button.disabled = true;
                    });
                });
            });
        });
    </script>
</body>
</html>