<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "student") {
    header("Location: ../index.php");
    exit();
}

require_once '../shared/includes/db_connection.php';

// Set page title and additional CSS files
$pageTitle = "MMU Hostel Management - Student Profile";
$additionalCSS = ["css/profile.css"];

// Fetch student information from database
$username = $_SESSION["user"];
$student = null;
$errors = [];
$success = false;

// Get student data
$stmt = $conn->prepare("SELECT * FROM students WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    $errors[] = "Student information not found.";
}

// Handle form submission for updating profile
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form data
    $name = $_POST['name'] ?? '';
    $contact_no = $_POST['contact_no'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Basic validation
    if (empty($name)) $errors[] = "Name is required";
    if (empty($contact_no)) $errors[] = "Contact Number is required";
    if (empty($email)) $errors[] = "Email is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (empty($address)) $errors[] = "Address is required";
    
    // Check if email already exists (but not for current user)
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM students WHERE email = ? AND username != ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Email already exists";
        }
    }
      // Profile image functionality removed as the column doesn't exist in database
    
    // If password change is requested
    if (!empty($current_password)) {
        // Verify current password
        if (!password_verify($current_password, $student["password"])) {
            $errors[] = "Current password is incorrect";
        }
        
        // Validate new password
        if (empty($new_password)) {
            $errors[] = "New password is required";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match";
        }
    }
      // Update student information
    if (empty($errors)) {
        if (!empty($current_password) && !empty($new_password)) {
            // Update with password change
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE students SET name = ?, contact_no = ?, email = ?, address = ?, password = ? WHERE username = ?");
            $stmt->bind_param("ssssss", $name, $contact_no, $email, $address, $hashed_password, $username);
        } else {
            // Update without password change
            $stmt = $conn->prepare("UPDATE students SET name = ?, contact_no = ?, email = ?, address = ? WHERE username = ?");
            $stmt->bind_param("sssss", $name, $contact_no, $email, $address, $username);
        }
        
        if ($stmt->execute()) {
            $success = true;
            // Update session variables
            $_SESSION["fullname"] = $name;
            
            // Refetch student data to reflect changes
            $stmt = $conn->prepare("SELECT * FROM students WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $student = $result->fetch_assoc();
            }
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
    }
}

// Include header
require_once '../shared/includes/header.php';

// Include student sidebar
require_once '../shared/includes/sidebar-student.php';
?>

<!-- Main Content -->
<div class="main-content">    <div class="header">
        <h1>My Profile</h1>
        <div class="user-info">
            <img src="../uploads/profile_pictures/default_student.png" alt="Student Profile">
            <span class="user-name"><?php echo $_SESSION["fullname"] ?? $_SESSION["user"]; ?></span>
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    
    <!-- Profile Form -->
    <div class="profile-container">
        <?php if ($success): ?>
            <div class="alert alert-success">
                Profile updated successfully!
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                <!-- Profile image container removed as the functionality is not supported by the database -->
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="name">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $student['name'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="ic_number">IC/Passport Number</label>
                            <input type="text" class="form-control" id="ic_number" value="<?php echo $student['ic_number'] ?? ''; ?>" readonly>
                            <small class="form-text text-muted">IC/Passport number cannot be changed</small>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="gender">Gender</label>
                            <input type="text" class="form-control" id="gender" value="<?php echo $student['gender'] ?? ''; ?>" readonly>
                            <small class="form-text text-muted">Gender cannot be changed</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="dob">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" value="<?php echo $student['dob'] ?? ''; ?>" readonly>
                            <small class="form-text text-muted">Date of birth cannot be changed</small>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="course">Course</label>
                            <input type="text" class="form-control" id="course" value="<?php echo $student['course'] ?? ''; ?>" readonly>
                            <small class="form-text text-muted">Course cannot be changed</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="citizenship">Citizenship</label>
                            <input type="text" class="form-control" id="citizenship" value="<?php echo $student['citizenship'] ?? ''; ?>" readonly>
                            <small class="form-text text-muted">Citizenship cannot be changed</small>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="contact_no">Contact Number</label>
                            <input type="text" class="form-control" id="contact_no" name="contact_no" value="<?php echo $student['contact_no'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $student['email'] ?? ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required><?php echo $student['address'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" value="<?php echo $student['username'] ?? ''; ?>" readonly>
                        <small class="form-text text-muted">Username cannot be changed</small>
                    </div>
                    
                    <hr>
                    <h4>Change Password</h4>
                    <p class="text-muted">Leave blank if you don't want to change your password</p>
                    
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="new_password">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Image preview script removed as profile image functionality is not supported -->

<?php
// Include footer
require_once '../shared/includes/footer.php';
?>
