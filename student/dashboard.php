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

// Include database connection
require_once '../shared/includes/db_connection.php';

// Get student_id from username
$student_username = $_SESSION["user"];
$stmt_student_id = $conn->prepare("SELECT id FROM students WHERE username = ?"); // Changed student_id to id
$stmt_student_id->bind_param("s", $student_username);
$stmt_student_id->execute();
$result_student_id = $stmt_student_id->get_result();
$student_id = null;
if ($result_student_id->num_rows > 0) {
    $student_data = $result_student_id->fetch_assoc();
    $student_id = $student_data['id']; // Changed student_id to id
}
$stmt_student_id->close();

// Initialize variables for dashboard data
$room_info = null;
$roommate_name = "N/A";
$billing_info = null;
$maintenance_requests = [];
$announcements = [];
$internet_usage = null;

if ($student_id) {
    // 1. Fetch Room Information
    $stmt_room = $conn->prepare("
        SELECT 
            r.room_number, 
            hb.block_name, 
            r.type AS room_type,  -- Changed r.room_type to r.type and aliased as room_type
            hr.check_in_date, 
            hr.contract_end_date
        FROM hostel_registrations hr
        JOIN rooms r ON hr.room_id = r.id -- Assuming room_id in hostel_registrations maps to id in rooms
        JOIN hostel_blocks hb ON r.block_id = hb.id -- Assuming block_id in rooms maps to id in hostel_blocks
        WHERE hr.student_id = ? AND hr.status = 'Active'
        ORDER BY hr.check_in_date DESC
        LIMIT 1
    ");
    if ($stmt_room) {
        $stmt_room->bind_param("i", $student_id);
        $stmt_room->execute();
        $result_room = $stmt_room->get_result();
        if ($result_room->num_rows > 0) {
            $room_info = $result_room->fetch_assoc();
            
            // Fetch roommate (if any, for twin sharing or more)
            if (isset($room_info['room_type']) && strtolower($room_info['room_type']) !== 'single') { // Check if room_type is set
                // Ensure room_id for the current student is correctly fetched for the subquery
                // The subquery should use the specific room_id of the current student's active registration
                $current_student_room_id = null;
                $stmt_current_room = $conn->prepare("SELECT room_id FROM hostel_registrations WHERE student_id = ? AND status = 'Active' ORDER BY check_in_date DESC LIMIT 1");
                if ($stmt_current_room) {
                    $stmt_current_room->bind_param("i", $student_id);
                    $stmt_current_room->execute();
                    $result_current_room = $stmt_current_room->get_result();
                    if ($result_current_room->num_rows > 0) {
                        $current_student_room_id = $result_current_room->fetch_assoc()['room_id'];
                    }
                    $stmt_current_room->close();
                }

                if ($current_student_room_id) {
                    $stmt_roommate = $conn->prepare("
                        SELECT s.name AS full_name -- Changed s.full_name to s.name
                        FROM hostel_registrations hr_other
                        JOIN students s ON hr_other.student_id = s.id -- Changed s.student_id to s.id
                        WHERE hr_other.room_id = ? 
                        AND hr_other.student_id != ? AND hr_other.status = 'Active'
                        LIMIT 1
                    ");
                    if ($stmt_roommate) {
                        $stmt_roommate->bind_param("ii", $current_student_room_id, $student_id);
                        $stmt_roommate->execute();
                        $result_roommate = $stmt_roommate->get_result();
                        if ($result_roommate->num_rows > 0) {
                            $roommate_data = $result_roommate->fetch_assoc();
                            $roommate_name = htmlspecialchars($roommate_data['full_name']);
                        }
                        $stmt_roommate->close();
                    } else {
                        error_log("Error preparing roommate statement: " . $conn->error);
                    }
                }
            }
        }
        $stmt_room->close();
    } else {
        // Handle statement preparation error for room info
        error_log("Error preparing room info statement: " . $conn->error);
    }

    // 2. Fetch Billing Information (latest unpaid or most recent paid)
    $stmt_billing = $conn->prepare("
        SELECT amount, due_date, status, description 
        FROM billing 
        WHERE student_id = ?
        ORDER BY 
            CASE status
                WHEN 'Unpaid' THEN 1
                WHEN 'Pending' THEN 2
                WHEN 'Paid' THEN 3
                ELSE 4
            END, 
            due_date DESC
        LIMIT 1
    ");
    if ($stmt_billing) {
        $stmt_billing->bind_param("i", $student_id);
        $stmt_billing->execute();
        $result_billing = $stmt_billing->get_result();
        if ($result_billing->num_rows > 0) {
            $billing_info = $result_billing->fetch_assoc();
        }
        $stmt_billing->close();
    } else {
        error_log("Error preparing billing statement: " . $conn->error);
    }


    // 3. Fetch Maintenance Requests (last 2)
    $stmt_maintenance = $conn->prepare("
        SELECT description, status 
        FROM service_requests 
        WHERE student_id = ? 
        ORDER BY date_requested DESC 
        LIMIT 2
    ");
    if ($stmt_maintenance) {
        $stmt_maintenance->bind_param("i", $student_id);
        $stmt_maintenance->execute();
        $result_maintenance = $stmt_maintenance->get_result();
        while ($row = $result_maintenance->fetch_assoc()) {
            $maintenance_requests[] = $row;
        }
        $stmt_maintenance->close();
    } else {
        error_log("Error preparing maintenance statement: " . $conn->error);
    }

    // 4. Fetch Announcements (last 3)
    $stmt_announcements = $conn->prepare("
        SELECT title, content, date_posted 
        FROM announcements 
        ORDER BY date_posted DESC 
        LIMIT 3
    ");
    if ($stmt_announcements) {
        $stmt_announcements->execute();
        $result_announcements = $stmt_announcements->get_result();
        while ($row = $result_announcements->fetch_assoc()) {
            $announcements[] = $row;
        }
        $stmt_announcements->close();
    } else {
        error_log("Error preparing announcements statement: " . $conn->error);
    }

    // 5. Fetch Internet Usage
    $stmt_internet = $conn->prepare("
        SELECT 
            ip.plan_name, 
            sis.data_used_gb, 
            ip.data_allowance_gb, 
            sis.end_date AS reset_date
        FROM student_internet_subscriptions sis
        JOIN internet_plans ip ON sis.plan_id = ip.plan_id
        WHERE sis.student_id = ? AND sis.end_date >= CURDATE()
        ORDER BY sis.start_date DESC
        LIMIT 1
    ");
    if ($stmt_internet) {
        $stmt_internet->bind_param("i", $student_id);
        $stmt_internet->execute();
        $result_internet = $stmt_internet->get_result();
        if ($result_internet->num_rows > 0) {
            $internet_usage = $result_internet->fetch_assoc();
        }
        $stmt_internet->close();
    } else {
        error_log("Error preparing internet usage statement: " . $conn->error);
    }
} // end if ($student_id)

?>

<!-- Main Content -->
<div class="main-content">
    <div class="header">
        <h1>Student Dashboard</h1>
        <div class="user-info">
            <?php 
            if (isset($_SESSION["profile_image"]) && !empty($_SESSION["profile_image"])) {
                echo '<img src="../uploads/profile_pictures/' . $_SESSION["profile_image"] . '" alt="Student Profile">';
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
                <?php if ($room_info): ?>
                    <p><strong>Room Number:</strong> <?php echo htmlspecialchars($room_info['room_number']); ?></p>
                    <p><strong>Hostel Block:</strong> <?php echo htmlspecialchars($room_info['block_name']); ?></p>
                    <p><strong>Room Type:</strong> <?php echo htmlspecialchars($room_info['room_type']); ?></p>
                    <p><strong>Roommate:</strong> <?php echo $roommate_name; // Already htmlspecialchar'd if found ?></p>
                    <p><strong>Check-in Date:</strong> <?php echo htmlspecialchars(date("F j, Y", strtotime($room_info['check_in_date']))); ?></p>
                    <p><strong>Contract End:</strong> <?php echo htmlspecialchars(date("F j, Y", strtotime($room_info['contract_end_date']))); ?></p>
                <?php else: ?>
                    <p>No active room registration found.</p>
                <?php endif; ?>
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
                <?php if ($billing_info): ?>
                    <p><strong>Current Status:</strong> 
                        <span style="color: <?php echo (strtolower($billing_info['status']) === 'paid') ? 'green' : ((strtolower($billing_info['status']) === 'unpaid') ? 'red' : 'orange'); ?>;">
                            <?php echo htmlspecialchars(ucfirst($billing_info['status'])); ?>
                        </span>
                    </p>
                    <?php if (strtolower($billing_info['status']) !== 'paid' && isset($billing_info['due_date'])): ?>
                        <p><strong>Next Payment Due:</strong> <?php echo htmlspecialchars(date("F j, Y", strtotime($billing_info['due_date']))); ?></p>
                    <?php elseif (isset($billing_info['due_date'])): ?>
                         <p><strong>Last Bill Date:</strong> <?php echo htmlspecialchars(date("F j, Y", strtotime($billing_info['due_date']))); ?></p>
                    <?php endif; ?>
                    <div class="bill-amount">RM <?php echo htmlspecialchars(number_format($billing_info['amount'], 2)); ?></div>
                    <p style="font-size: 0.9em; color: #666; margin-top: 5px;"><?php echo htmlspecialchars($billing_info['description']); ?></p>
                <?php else: ?>
                    <p>No billing information available.</p>
                <?php endif; ?>
                <div class="quick-actions">
                    <button class="action-btn" onclick="window.location.href='billing.php'">Pay Bill / View</button>
                    <button class="action-btn" onclick="window.location.href='billing.php#history'">Payment History</button>
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
                <?php if (!empty($maintenance_requests)): ?>
                    <ul class="maintenance-list">
                        <?php foreach ($maintenance_requests as $request): ?>
                            <li>
                                <div><?php echo htmlspecialchars(substr($request['description'], 0, 50)) . (strlen($request['description']) > 50 ? '...' : ''); ?></div>
                                <div class="maintenance-status <?php echo strtolower(str_replace(' ', '-', htmlspecialchars($request['status']))); ?>">
                                    <?php echo htmlspecialchars($request['status']); ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No recent maintenance requests.</p>
                <?php endif; ?>
                <div class="quick-actions" style="margin-top: 10px;">
                    <button class="action-btn" onclick="window.location.href='requests.php?action=new'">New Request</button>
                    <button class="action-btn" onclick="window.location.href='requests.php'">View All</button>
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
                <?php if (!empty($announcements)): ?>
                    <ul class="announcement-list">
                        <?php foreach ($announcements as $announcement): ?>
                            <li>
                                <div title="<?php echo htmlspecialchars($announcement['content']); ?>"><strong><?php echo htmlspecialchars($announcement['title']); ?></strong></div>
                                <div class="announcement-date">Posted: <?php echo htmlspecialchars(date("M j, Y", strtotime($announcement['date_posted']))); ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No recent announcements.</p>
                <?php endif; ?>
                 <div class="quick-actions" style="margin-top: 10px;">
                    <button class="action-btn" onclick="window.location.href='announcements.php'">View All Announcements</button>
                </div>
            </div>
        </div>

        <!-- Campus Services -->
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
                <?php if ($internet_usage): 
                    $data_used = floatval($internet_usage['data_used_gb']);
                    $data_allowance = floatval($internet_usage['data_allowance_gb']);
                    $usage_percentage = ($data_allowance > 0) ? ($data_used / $data_allowance) * 100 : 0;
                    $usage_percentage = min(100, max(0, $usage_percentage)); // Cap between 0 and 100
                ?>
                    <p><strong>Current Plan:</strong> <?php echo htmlspecialchars($internet_usage['plan_name']); ?></p>
                    <p><strong>Data Used:</strong> <?php echo htmlspecialchars(number_format($data_used, 1)); ?> GB / <?php echo htmlspecialchars(number_format($data_allowance, 0)); ?> GB</p>
                    <div style="background: #eee; height: 10px; border-radius: 5px; margin: 10px 0;">
                        <div style="background: linear-gradient(to right, #6e8efb, #a777e3); width: <?php echo $usage_percentage; ?>%; height: 100%; border-radius: 5px;"></div>
                    </div>
                    <p><strong>Next Reset Date:</strong> <?php echo htmlspecialchars(date("F j, Y", strtotime($internet_usage['reset_date']))); ?></p>
                <?php else: ?>
                    <p>No active internet plan found.</p>
                <?php endif; ?>
                <button class="action-btn" style="width: 100%; margin-top: 10px;" onclick="window.location.href='billing.php#internet-plans'">Upgrade Plan</button> <!-- Assuming internet plans are part of billing or a dedicated page -->
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once '../shared/includes/footer.php';

// Close the database connection
if (isset($conn)) {
    $conn->close();
}
?>