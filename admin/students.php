<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

require_once '../shared/includes/db_connection.php';

// Get all students
$students_query = "SELECT * FROM students ORDER BY student_id ASC";
$students_result = $conn->query($students_query);
$students = [];
if ($students_result && $students_result->num_rows > 0) {
    while ($row = $students_result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Set page title and additional CSS files
$pageTitle = "Student Information - MMU Hostel Management";
$additionalCSS = ["css/dashboard.css"];

// Include header
require_once '../shared/includes/header.php';

// Include admin sidebar
require_once '../shared/includes/sidebar-admin.php';
?>

<!-- Main Content -->
<div class="main-content">
    <div class="header">
        <h1>Student Information</h1>
        <div class="user-info">
            <?php 
            if (isset($_SESSION["profile_image"]) && !empty($_SESSION["profile_image"])) {
                if (strpos($_SESSION["profile_image"], '../uploads/profile_pictures/') === 0) {
                    echo '<img src="' . $_SESSION["profile_image"] . '" alt="Admin Profile">';
                } else {
                    echo '<img src="../uploads/profile_pictures/' . $_SESSION["profile_image"] . '" alt="Admin Profile">';
                }
            } else {
                echo '<img src="../uploads/profile_pictures/default_admin.png" alt="Admin Profile">';
            }
            ?>
            <span class="user-name"><?php echo $_SESSION["fullname"] ?? $_SESSION["user"]; ?></span>
            <a href="../logout.php" class="logout-btn">
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
        <!-- Finance information content -->
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
                <!-- Finance table content -->
                <!-- This would be populated from your database -->
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
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Example data, would be populated dynamically -->
                            <tr>
                                <td>1191301382</td>
                                <td>Amir Bin Razak</td>
                                <td>2025A</td>
                                <td>3,400.00</td>
                                <td>3,400.00</td>
                                <td>0.00</td>
                                <td><span class="status status-paid">Paid</span></td>
                                <td class="action-buttons">
                                    <a href="#"><i class="fas fa-eye"></i></a>
                                    <a href="#"><i class="fas fa-receipt"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>1191303539</td>
                                <td>Liu Wei Ming</td>
                                <td>2025A</td>
                                <td>3,400.00</td>
                                <td>1,700.00</td>
                                <td>1,700.00</td>
                                <td><span class="status status-pending">Pending</span></td>
                                <td class="action-buttons">
                                    <a href="#"><i class="fas fa-eye"></i></a>
                                    <a href="#"><i class="fas fa-receipt"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>1191302789</td>
                                <td>Sarah Abdullah</td>
                                <td>2025A</td>
                                <td>3,400.00</td>
                                <td>0.00</td>
                                <td>3,400.00</td>
                                <td><span class="status status-overdue">Overdue</span></td>
                                <td class="action-buttons">
                                    <a href="#"><i class="fas fa-eye"></i></a>
                                    <a href="#"><i class="fas fa-receipt"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Details Modal -->
<div id="student-details-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="student-details-content">
            <!-- Student details will be loaded here via AJAX -->
        </div>
    </div>
</div>

<?php
// Add specific JavaScript for this page
$additionalJS = ["js/students.js"];

// Include footer
require_once '../shared/includes/footer.php';
?>

<!-- Add page-specific JavaScript -->
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
        
        // Start from row 1 to skip header row
        for (let i = 1; i < rows.length; i++) {
            const studentId = rows[i].cells[0].textContent.toLowerCase();
            const studentName = rows[i].cells[1].textContent.toLowerCase();
            
            rows[i].style.display = (studentId.includes(searchTerm) || studentName.includes(searchTerm)) ? '' : 'none';
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