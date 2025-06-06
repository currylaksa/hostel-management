<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

require_once '../shared/includes/db_connection.php';

// Get all students
$students_query = "SELECT * FROM students ORDER BY id ASC";
$students_result = $conn->query($students_query);
$students = [];
if ($students_result && $students_result->num_rows > 0) {
    while ($row = $students_result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Get financial information (bills, payments, and outstanding balances)
$finance_query = "
    SELECT 
        s.id as student_id,
        s.name as student_name,
        b.semester,
        b.academic_year,
        b.amount as bill_amount,
        b.due_date,
        b.status as bill_status,
        COALESCE(SUM(p.amount), 0) as paid_amount,
        b.amount - COALESCE(SUM(p.amount), 0) as balance
    FROM 
        students s
    LEFT JOIN 
        bills b ON s.id = b.student_id
    LEFT JOIN 
        payments p ON b.id = p.bill_id AND p.status = 'completed'
    GROUP BY 
        s.id, b.id
    ORDER BY 
        s.id ASC, b.due_date DESC
";
$finance_result = $conn->query($finance_query);
$finance_data = [];
if ($finance_result && $finance_result->num_rows > 0) {
    while ($row = $finance_result->fetch_assoc()) {
        $finance_data[] = $row;
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
                <div class="table-responsive">                    <table class="data-table" id="students-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Full Name</th>
                                <th>Course</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Gender</th>
                                <th>Citizenship</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($students) > 0): ?>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['id']); ?></td>
                                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                                        <td><?php echo htmlspecialchars($student['course']); ?></td>
                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        <td><?php echo htmlspecialchars($student['contact_no']); ?></td>
                                        <td><?php echo htmlspecialchars($student['gender']); ?></td>
                                        <td><?php echo htmlspecialchars($student['citizenship']); ?></td>
                                        <td class="action-buttons">
                                            <a href="javascript:void(0)" onclick="viewStudentDetails(<?php echo $student['id']; ?>)" title="View Details"><i class="fas fa-eye"></i></a>
                                            <a href="javascript:void(0)" onclick="editStudent(<?php echo $student['id']; ?>)" title="Edit Student"><i class="fas fa-edit"></i></a>
                                            <a href="javascript:void(0)" onclick="viewFinance(<?php echo $student['id']; ?>)" title="View Finance"><i class="fas fa-file-invoice-dollar"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No students found in the database</td>
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
                <!-- This would be populated from your database -->                <div class="table-responsive">
                    <table class="data-table" id="finance-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Semester</th>
                                <th>Academic Year</th>
                                <th>Due Date</th>
                                <th>Total Fee (RM)</th>
                                <th>Paid Amount (RM)</th>
                                <th>Balance (RM)</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($finance_data) > 0): ?>
                                <?php foreach ($finance_data as $finance): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($finance['student_id']); ?></td>
                                        <td><?php echo htmlspecialchars($finance['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($finance['semester'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($finance['academic_year'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($finance['due_date'] ?? 'N/A'); ?></td>
                                        <td><?php echo number_format($finance['bill_amount'] ?? 0, 2); ?></td>
                                        <td><?php echo number_format($finance['paid_amount'] ?? 0, 2); ?></td>
                                        <td><?php echo number_format($finance['balance'] ?? 0, 2); ?></td>
                                        <td>
                                            <?php 
                                            $status = $finance['bill_status'] ?? 'unknown';
                                            $statusClass = '';
                                            
                                            switch ($status) {
                                                case 'paid':
                                                    $statusClass = 'status-paid';
                                                    break;
                                                case 'partially_paid':
                                                    $statusClass = 'status-pending';
                                                    break;
                                                case 'unpaid':
                                                    $statusClass = 'status-inactive';
                                                    break;
                                                case 'overdue':
                                                    $statusClass = 'status-overdue';
                                                    break;
                                                default:
                                                    $statusClass = 'status-inactive';
                                            }
                                            ?>
                                            <span class="status <?php echo $statusClass; ?>"><?php echo ucfirst(str_replace('_', ' ', $status)); ?></span>
                                        </td>
                                        <td class="action-buttons">
                                            <a href="javascript:void(0)" onclick="viewBillDetails(<?php echo $finance['student_id']; ?>)" title="View Bill Details"><i class="fas fa-eye"></i></a>
                                            <a href="javascript:void(0)" onclick="viewPaymentReceipt(<?php echo $finance['student_id']; ?>)" title="View Receipt"><i class="fas fa-receipt"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center">No financial information found</td>
                                </tr>
                            <?php endif; ?>
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
    });    // Search functionality for student list
    document.getElementById('student-search').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const table = document.getElementById('students-table');
        const rows = table.getElementsByTagName('tr');
        
        // Start from row 1 to skip header row
        for (let i = 1; i < rows.length; i++) {
            const studentId = rows[i].cells[0].textContent.toLowerCase();
            const studentName = rows[i].cells[1].textContent.toLowerCase();
            const course = rows[i].cells[2].textContent.toLowerCase();
            const email = rows[i].cells[3].textContent.toLowerCase();
            
            const shouldShow = studentId.includes(searchTerm) || 
                             studentName.includes(searchTerm) || 
                             course.includes(searchTerm) || 
                             email.includes(searchTerm);
            
            rows[i].style.display = shouldShow ? '' : 'none';
        }
    });

    // Search functionality for finance table
    document.getElementById('finance-search').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const table = document.getElementById('finance-table');
        const rows = table.getElementsByTagName('tr');
        
        // Start from row 1 to skip header row
        for (let i = 1; i < rows.length; i++) {
            const studentId = rows[i].cells[0].textContent.toLowerCase();
            const studentName = rows[i].cells[1].textContent.toLowerCase();
            
            const shouldShow = studentId.includes(searchTerm) || studentName.includes(searchTerm);
            
            rows[i].style.display = shouldShow ? '' : 'none';
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
    }    // Function to switch to finance tab and filter for specific student
    function viewFinance(studentId) {
        // Switch to finance tab
        document.querySelector('[data-tab="finance-info"]').click();
        
        // Wait a moment for the tab to switch, then set the search
        setTimeout(() => {
            const searchBox = document.getElementById('finance-search');
            if (searchBox) {
                searchBox.value = studentId;
                
                // Trigger the search
                const event = new Event('keyup');
                searchBox.dispatchEvent(event);
                
                // Focus the search box
                searchBox.focus();
            }
        }, 100);
    }
</script>