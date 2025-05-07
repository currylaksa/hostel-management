<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.html");
    exit();
}

require_once 'db_connection.php';

// Get all students
$students_query = "SELECT * FROM students ORDER BY student_id ASC";
$students_result = $conn->query($students_query);
$students = [];
if ($students_result && $students_result->num_rows > 0) {
    while ($row = $students_result->fetch_assoc()) {
        $students[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information - MMU Hostel Management</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="admin_students.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>MMU Hostel</h2>
                <p>Admin Portal</p>
            </div>
            <div class="sidebar-menu">
                <a href="admin_dashboard.php" class="menu-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                
                <div class="menu-category">Student Management</div>
                <a href="admin_students.php" class="menu-item active">
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
                <a href="admin_profile.php" class="menu-item">
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
                <h1>Student Information</h1>
                <div class="user-info">
                    <?php 
                    if (isset($_SESSION["profile_image"]) && !empty($_SESSION["profile_image"])) {
                        if (strpos($_SESSION["profile_image"], 'uploads/profile_pictures/') === 0) {
                            echo '<img src="' . $_SESSION["profile_image"] . '" alt="Admin Profile">';
                        } else {
                            echo '<img src="uploads/profile_pictures/' . $_SESSION["profile_image"] . '" alt="Admin Profile">';
                        }
                    } else {
                        echo '<img src="uploads/profile_pictures/default_admin.png" alt="Admin Profile">';
                    }
                    ?>
                    <span class="user-name"><?php echo $_SESSION["fullname"] ?? $_SESSION["user"]; ?></span>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="tab-navigation">
                <button class="tab-button active" data-tab="student-list">All Students</button>
                <button class="tab-button" data-tab="finance-info">Finance Information</button>
            </div>

            <!-- Student List Tab (Default Active) -->
            <div class="tab-content active" id="student-list">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title-area">
                            <div class="card-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h2 class="card-title">All Student List</h2>
                        </div>
                        <div class="card-actions">
                            <div class="search-container">
                                <input type="text" id="student-search" placeholder="Search students...">
                                <i class="fas fa-search"></i>
                            </div>
                            <button class="btn-export"><i class="fas fa-file-export"></i> Export</button>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table class="data-table" id="students-table">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Full Name</th>
                                        <th>Faculty</th>
                                        <th>Program</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Room</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($students) > 0): ?>
                                        <?php foreach ($students as $student): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                                <td><?php echo htmlspecialchars($student['faculty'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($student['program'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                                <td><?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($student['room_id'] ?? 'Not Assigned'); ?></td>
                                                <td>
                                                    <?php if ($student['status'] == 'active'): ?>
                                                        <span class="status status-active">Active</span>
                                                    <?php elseif ($student['status'] == 'pending'): ?>
                                                        <span class="status status-pending">Pending</span>
                                                    <?php else: ?>
                                                        <span class="status status-inactive">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="action-buttons">
                                                    <a href="javascript:void(0)" onclick="viewStudentDetails(<?php echo $student['id']; ?>)" title="View Details"><i class="fas fa-eye"></i></a>
                                                    <a href="javascript:void(0)" onclick="editStudent(<?php echo $student['id']; ?>)" title="Edit Student"><i class="fas fa-edit"></i></a>
                                                    <a href="javascript:void(0)" onclick="viewFinance(<?php echo $student['id']; ?>)" title="View Finance"><i class="fas fa-file-invoice-dollar"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center">No students found in the database</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Finance Info Tab -->
            <div class="tab-content" id="finance-info">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title-area">
                            <div class="card-icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <h2 class="card-title">Student Financial Overview</h2>
                        </div>
                        <div class="card-actions">
                            <div class="search-container">
                                <input type="text" id="finance-search" placeholder="Search by Student ID...">
                                <i class="fas fa-search"></i>
                            </div>
                            <button class="btn-export"><i class="fas fa-file-export"></i> Export</button>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table class="data-table" id="finance-table">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Semester</th>
                                        <th>Total Fee (RM)</th>
                                        <th>Paid Amount (RM)</th>
                                        <th>Balance (RM)</th>
                                        <th>Due Date</th>
                                        <th>Payment Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Sample data, replace with actual database records -->
                                    <tr>
                                        <td>1191301382</td>
                                        <td>Amir Bin Razak</td>
                                        <td>3 (2025)</td>
                                        <td>2,800.00</td>
                                        <td>2,800.00</td>
                                        <td>0.00</td>
                                        <td>15 May 2025</td>
                                        <td><span class="status status-paid">Paid</span></td>
                                        <td class="action-buttons">
                                            <a href="#" title="View Payment History"><i class="fas fa-history"></i></a>
                                            <a href="#" title="Record Payment"><i class="fas fa-plus-circle"></i></a>
                                            <a href="#" title="Print Receipt"><i class="fas fa-print"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>1191302476</td>
                                        <td>Nurul Huda</td>
                                        <td>3 (2025)</td>
                                        <td>2,800.00</td>
                                        <td>1,400.00</td>
                                        <td>1,400.00</td>
                                        <td>15 May 2025</td>
                                        <td><span class="status status-partial">Partial</span></td>
                                        <td class="action-buttons">
                                            <a href="#" title="View Payment History"><i class="fas fa-history"></i></a>
                                            <a href="#" title="Record Payment"><i class="fas fa-plus-circle"></i></a>
                                            <a href="#" title="Print Receipt"><i class="fas fa-print"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>1191303539</td>
                                        <td>Liu Wei Ming</td>
                                        <td>3 (2025)</td>
                                        <td>2,800.00</td>
                                        <td>0.00</td>
                                        <td>2,800.00</td>
                                        <td>15 May 2025</td>
                                        <td><span class="status status-unpaid">Unpaid</span></td>
                                        <td class="action-buttons">
                                            <a href="#" title="View Payment History"><i class="fas fa-history"></i></a>
                                            <a href="#" title="Record Payment"><i class="fas fa-plus-circle"></i></a>
                                            <a href="#" title="Send Reminder"><i class="fas fa-paper-plane"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Details Modal -->
            <div class="modal" id="student-details-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Student Details</h2>
                        <span class="close">&times;</span>
                    </div>
                    <div class="modal-body" id="student-details-content">
                        <!-- Content will be loaded dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Tab switching functionality
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all tabs and content
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Show corresponding content
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Search functionality for student list
        document.getElementById('student-search').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const table = document.getElementById('students-table');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) { // Start at 1 to skip header row
                const row = rows[i];
                let matchFound = false;
                
                // Check each cell in the row
                for (let j = 0; j < row.cells.length - 1; j++) { // Skip actions column
                    const cellText = row.cells[j].textContent.toLowerCase();
                    if (cellText.includes(searchTerm)) {
                        matchFound = true;
                        break;
                    }
                }
                
                row.style.display = matchFound ? '' : 'none';
            }
        });

        // Search functionality for finance table
        document.getElementById('finance-search').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const table = document.getElementById('finance-table');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) { // Start at 1 to skip header row
                const row = rows[i];
                const studentId = row.cells[0].textContent.toLowerCase();
                const studentName = row.cells[1].textContent.toLowerCase();
                
                row.style.display = (studentId.includes(searchTerm) || studentName.includes(searchTerm)) ? '' : 'none';
            }
        });

        // Modal functionality
        const modal = document.getElementById('student-details-modal');
        const closeBtn = document.querySelector('.close');
        
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Function to view student details
        function viewStudentDetails(studentId) {
            // AJAX call to get student details
            $.ajax({
                url: 'get_student_details.php',
                type: 'GET',
                data: { id: studentId },
                success: function(response) {
                    document.getElementById('student-details-content').innerHTML = response;
                    modal.style.display = "block";
                },
                error: function() {
                    alert('Error fetching student details');
                }
            });
        }

        // Function to redirect to edit student page
        function editStudent(studentId) {
            window.location.href = 'edit_student.php?id=' + studentId;
        }

        // Function to switch to finance tab and filter for specific student
        function viewFinance(studentId) {
            // Switch to finance tab
            document.querySelector('[data-tab="finance-info"]').click();
            
            // Get the student ID from the first column in the students table
            const studentTable = document.getElementById('students-table');
            const rows = studentTable.getElementsByTagName('tr');
            let studentIdValue = '';
            
            for (let i = 1; i < rows.length; i++) {
                if (rows[i].cells[0].textContent) {
                    studentIdValue = rows[i].cells[0].textContent;
                    break;
                }
            }
            
            // Set the search term in the finance search box
            const searchBox = document.getElementById('finance-search');
            searchBox.value = studentIdValue;
            
            // Trigger the search event
            searchBox.dispatchEvent(new Event('keyup'));
        }
    </script>
</body>
</html>