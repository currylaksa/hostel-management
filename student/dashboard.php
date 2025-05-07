<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "student") {
    header("Location: ../index.php");
    exit();
}

// Set page title and additional CSS files
$pageTitle = "MMU Hostel Management - Student Dashboard";
$additionalCSS = ["css/dashboard.css"];

// Include header
require_once '../shared/includes/header.php';

// Include student sidebar
require_once '../shared/includes/sidebar-student.php';
?>

<!-- Main Content -->
<div class="main-content">
    <div class="header">
        <h1>Student Dashboard</h1>
        <div class="user-info">
            <?php 
            if (isset($_SESSION["profile_image"]) && !empty($_SESSION["profile_image"])) {
                if (strpos($_SESSION["profile_image"], '../uploads/profile_pictures/') === 0) {
                    echo '<img src="' . $_SESSION["profile_image"] . '" alt="Student Profile">';
                } else {
                    echo '<img src="../uploads/profile_pictures/' . $_SESSION["profile_image"] . '" alt="Student Profile">';
                }
            } else {
                echo '<img src="../uploads/profile_pictures/default_student.png" alt="Student Profile">';
            }
            ?>
            <span class="user-name"><?php echo $_SESSION["fullname"] ?? $_SESSION["user"]; ?></span>
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="dashboard-cards">
        <!-- Room Information -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-bed"></i>
                </div>
                <h2 class="card-title">Room Information</h2>
            </div>
            <div class="card-content">
                <p><strong>Room Number:</strong> B-203</p>
                <p><strong>Hostel Block:</strong> Cyber Heights Villa Block B</p>
                <p><strong>Room Type:</strong> Twin Sharing</p>
                <p><strong>Roommate:</strong> Ahmad Bin Abdullah</p>
                <p><strong>Check-in Date:</strong> January 15, 2025</p>
                <p><strong>Contract End:</strong> December 15, 2025</p>
            </div>
        </div>

        <!-- Billing Information -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <h2 class="card-title">Billing & Payments</h2>
            </div>
            <div class="card-content">
                <p><strong>Current Status:</strong> <span style="color: green;">Paid</span></p>
                <p><strong>Next Payment:</strong> June 1, 2025</p>
                <div class="bill-amount">RM 850.00</div>
                <div class="quick-actions">
                    <button class="action-btn">Pay Bill</button>
                    <button class="action-btn">Payment History</button>
                </div>
            </div>
        </div>

        <!-- Maintenance Requests -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-wrench"></i>
                </div>
                <h2 class="card-title">Maintenance Requests</h2>
            </div>
            <div class="card-content">
                <ul class="maintenance-list">
                    <li>
                        <div>Air conditioning repair</div>
                        <div class="maintenance-status in-progress">In Progress</div>
                    </li>
                    <li>
                        <div>Bathroom sink leaking</div>
                        <div class="maintenance-status resolved">Resolved</div>
                    </li>
                </ul>
                <div class="quick-actions" style="margin-top: 10px;">
                    <button class="action-btn">New Request</button>
                    <button class="action-btn">View All</button>
                </div>
            </div>
        </div>

        <!-- Announcements -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h2 class="card-title">Announcements</h2>
            </div>
            <div class="card-content">
                <ul class="announcement-list">
                    <li>
                        <div>Water supply maintenance on May 15, 2025</div>
                        <div class="announcement-date">Posted: May 8, 2025</div>
                    </li>
                    <li>
                        <div>Wi-Fi upgrade scheduled for next weekend</div>
                        <div class="announcement-date">Posted: May 5, 2025</div>
                    </li>
                    <li>
                        <div>Hostel community gathering - May 30</div>
                        <div class="announcement-date">Posted: May 2, 2025</div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Quick Services -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-concierge-bell"></i>
                </div>
                <h2 class="card-title">Campus Services</h2>
            </div>
            <div class="card-content">
                <div class="quick-actions" style="grid-template-columns: repeat(2, 1fr); margin-bottom: 10px;">
                    <button class="action-btn">Cafeteria Menu</button>
                    <button class="action-btn">Laundry Services</button>
                    <button class="action-btn">Shuttle Schedule</button>
                    <button class="action-btn">Sports Facilities</button>
                </div>
            </div>
        </div>

        <!-- Internet Usage -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-wifi"></i>
                </div>
                <h2 class="card-title">Internet Usage</h2>
            </div>
            <div class="card-content">
                <p><strong>Current Plan:</strong> Premium Student Package</p>
                <p><strong>Data Used:</strong> 45.2 GB / 100 GB</p>
                <div style="background: #eee; height: 10px; border-radius: 5px; margin: 10px 0;">
                    <div style="background: linear-gradient(to right, #6e8efb, #a777e3); width: 45%; height: 100%; border-radius: 5px;"></div>
                </div>
                <p><strong>Reset Date:</strong> June 1, 2025</p>
                <button class="action-btn" style="width: 100%; margin-top: 10px;">Upgrade Plan</button>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once '../shared/includes/footer.php';
?>