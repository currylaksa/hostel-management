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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Registration - MMU Hostel Management System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h3 class="text-center">Visitor Registration</h3>
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
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <p>Registration successful! Your visit has been recorded.</p>
                                <p>Thank you for registering as a visitor!</p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!$success): ?>
                        <form action="visitor_registration.php" method="POST">
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
                                        <label for="ic_number">IC Number</label>
                                        <input type="text" name="ic_number" id="ic_number" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_no">Contact Number</label>
                                        <input type="text" name="contact_no" id="contact_no" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="car_plate">Car Plate Number (Optional)</label>
                                        <input type="text" name="car_plate" id="car_plate" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="room_number">Room Number to Visit</label>
                                        <input type="text" name="room_number" id="room_number" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="visit_date">Date of Visit</label>
                                        <input type="date" name="visit_date" id="visit_date" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="time_in">Time In</label>
                                        <input type="time" name="time_in" id="time_in" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="time_out">Expected Time Out (Optional)</label>
                                        <input type="time" name="time_out" id="time_out" class="form-control">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-warning btn-lg">Register Visit</button>
                            </div>
                            
                            <div class="text-center">
                                <p><a href="index.html">Back to Home</a></p>
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
</body>
</html>