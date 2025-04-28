<?php
session_start();
require_once 'db_connection.php';

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form data
    $name = $_POST['name'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $ic_number = $_POST['ic_number'] ?? '';
    $contact_no = $_POST['contact_no'] ?? '';
    $car_plate = $_POST['car_plate'] ?? '';
    $visit_date = $_POST['visit_date'] ?? '';
    $time_in = $_POST['time_in'] ?? '';
    $time_out = $_POST['time_out'] ?? null;
    $room_number = $_POST['room_number'] ?? '';
    
    // Validate form data
    if (empty($name)) $errors[] = "Name is required";
    if (empty($gender)) $errors[] = "Gender is required";
    if (empty($ic_number)) $errors[] = "IC number is required";
    if (empty($contact_no)) $errors[] = "Contact number is required";
    if (empty($visit_date)) $errors[] = "Visit date is required";
    if (empty($time_in)) $errors[] = "Time in is required";
    if (empty($room_number)) $errors[] = "Room number is required";
    
    // If no errors, insert into database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO visitors (name, gender, ic_number, contact_no, car_plate, visit_date, time_in, time_out, room_number) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $name, $gender, $ic_number, $contact_no, $car_plate, $visit_date, $time_in, $time_out, $room_number);
        
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Visitor Registration - MMU Hostel Management System</title>
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
                    <div class="card-header bg-warning text-dark text-center">
                        <h3><i class="fas fa-user-friends mr-2"></i>Visitor Registration</h3>
                        <p class="mb-0">Register your visit to MMU Hostel</p>
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
                            <div class="alert alert-success fade-in">
                                <h4><i class="fas fa-check-circle mr-2"></i>Registration Successful!</h4>
                                <p>Your visit has been recorded. Please check in at the hostel reception upon arrival.</p>
                                <p class="mb-0">Thank you for registering as a visitor!</p>
                                <hr>
                                <div class="text-center mt-3">
                                    <a href="index.php" class="btn btn-outline-success"><i class="fas fa-home mr-2"></i>Return to Home</a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!$success): ?>
                        <!-- Progress indicator -->
                        <div class="progress mb-4" style="height: 6px;">
                            <div class="progress-bar bg-warning" id="form-progress" role="progressbar" style="width: 0%"></div>
                        </div>
                        
                        <form action="visitor_registration.php" method="POST" class="needs-validation" novalidate id="signup-form">
                            <!-- Visitor Information Section -->
                            <div class="form-section visitor-section collapsible-section active" data-section="1">
                                <h4 class="section-header">
                                    <i class="fas fa-user mr-2"></i>Visitor Information
                                    <span class="float-right toggle-icon"><i class="fas fa-chevron-down"></i></span>
                                </h4>
                                <div class="section-content">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name" class="required-field">Full Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                    </div>
                                                    <input type="text" name="name" id="name" class="form-control" required>
                                                </div>
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
                                                <label for="ic_number" class="required-field">IC Number / Passport</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                    </div>
                                                    <input type="text" name="ic_number" id="ic_number" class="form-control" required>
                                                </div>
                                                <small class="form-text">For identification purposes</small>
                                            </div>
                                        </div>
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
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="car_plate">Car Plate Number (Optional)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-car"></i></span>
                                            </div>
                                            <input type="text" name="car_plate" id="car_plate" class="form-control">
                                        </div>
                                        <small class="form-text">For vehicle entry permission</small>
                                    </div>
                                    
                                    <div class="text-right mt-3">
                                        <button type="button" class="btn btn-outline-warning next-section" data-next="2">
                                            Next <i class="fas fa-arrow-right ml-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Visit Details Section -->
                            <div class="form-section visitor-section collapsible-section" data-section="2">
                                <h4 class="section-header">
                                    <i class="fas fa-calendar-alt mr-2"></i>Visit Details
                                    <span class="float-right toggle-icon"><i class="fas fa-chevron-right"></i></span>
                                </h4>
                                <div class="section-content">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="visit_purpose" class="required-field">Purpose of Visit</label>
                                                <select name="visit_purpose" id="visit_purpose" class="form-control" required>
                                                    <option value="" disabled selected>Select Purpose</option>
                                                    <option value="Family Visit">Family Visit</option>
                                                    <option value="Friend Visit">Friend Visit</option>
                                                    <option value="Official Visit">Official Visit</option>
                                                    <option value="Delivery">Delivery</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="room_number" class="required-field">Room Number to Visit</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-door-open"></i></span>
                                                    </div>
                                                    <input type="text" name="room_number" id="room_number" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="visit_date" class="required-field">Date of Visit</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                    </div>
                                                    <input type="date" name="visit_date" id="visit_date" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="time_in" class="required-field">Time In</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                                    </div>
                                                    <input type="time" name="time_in" id="time_in" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="time_out">Expected Time Out</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                                    </div>
                                                    <input type="time" name="time_out" id="time_out" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mt-3">
                                        <button type="button" class="btn btn-outline-secondary prev-section" data-prev="1">
                                            <i class="fas fa-arrow-left mr-1"></i> Previous
                                        </button>
                                        <button type="button" class="btn btn-outline-warning next-section" data-next="3">
                                            Next <i class="fas fa-arrow-right ml-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Terms and Policy Section -->
                            <div class="form-section visitor-section collapsible-section" data-section="3">
                                <h4 class="section-header">
                                    <i class="fas fa-clipboard-list mr-2"></i>Terms & Policies
                                    <span class="float-right toggle-icon"><i class="fas fa-chevron-right"></i></span>
                                </h4>
                                <div class="section-content">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <p class="mb-2"><i class="fas fa-info-circle text-warning mr-2"></i><strong>Visitor Policies:</strong></p>
                                            <ul class="mb-0">
                                                <li>Visitors must present identification at the security desk</li>
                                                <li>Visiting hours are from 9 AM to 9 PM daily</li>
                                                <li>Visitors must be accompanied by the resident they are visiting</li>
                                                <li>Visitors are not allowed to stay overnight without prior approval</li>
                                                <li>MMU Hostel reserves the right to deny entry</li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group mt-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="accept_policy" name="accept_policy" required>
                                            <label class="custom-control-label" for="accept_policy">I have read and agree to follow the visitor policies.</label>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mt-4">
                                        <button type="button" class="btn btn-outline-secondary prev-section" data-prev="2">
                                            <i class="fas fa-arrow-left mr-1"></i> Previous
                                        </button>
                                        <button type="submit" class="btn btn-warning btn-signup btn-visitor">
                                            <i class="fas fa-clipboard-check mr-2"></i>Register Visit
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-nav-links">
                                <p><a href="index.php"><i class="fas fa-home mr-1"></i>Back to Home</a></p>
                            </div>
                        </form>
                        <?php endif; ?>
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