-- Create database
CREATE DATABASE IF NOT EXISTS hostel_management;
USE hostel_management;

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    dob DATE NOT NULL,
    ic_number VARCHAR(20) UNIQUE NOT NULL,
    contact_no VARCHAR(20) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    citizenship ENUM('Malaysian', 'Others') NOT NULL,
    address TEXT NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_pic VARCHAR(255) DEFAULT NULL,
    office_number VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    dob DATE NOT NULL,
    ic_number VARCHAR(20) UNIQUE NOT NULL,
    course VARCHAR(100) NOT NULL,
    contact_no VARCHAR(20) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    citizenship ENUM('Malaysian', 'Others') NOT NULL,
    address TEXT NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_pic VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Emergency contacts for students
CREATE TABLE IF NOT EXISTS emergency_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    ic_number VARCHAR(20) NOT NULL,
    relationship VARCHAR(50) NOT NULL,
    contact_no VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Visitors log
CREATE TABLE IF NOT EXISTS visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    ic_number VARCHAR(20) NOT NULL,
    contact_no VARCHAR(20) NOT NULL,
    car_plate VARCHAR(20) DEFAULT NULL,
    visit_date DATE NOT NULL,
    time_in TIME NOT NULL,
    time_out TIME DEFAULT NULL,
    room_number VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Hostel Blocks table
CREATE TABLE IF NOT EXISTS hostel_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    block_name VARCHAR(50) UNIQUE NOT NULL, -- e.g., 'Block A', 'Block B'
    gender_restriction ENUM('Male', 'Female', 'Mixed', 'None') NOT NULL DEFAULT 'None', -- e.g., Male only, Female only
    nationality_restriction ENUM('Local', 'International', 'Mixed', 'None') NOT NULL DEFAULT 'None', -- e.g., Local students only
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rooms table
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    block_id INT NOT NULL,
    room_number VARCHAR(10) NOT NULL, -- e.g., '101', 'A-203'
    type ENUM('Single', 'Double', 'Triple', 'Quad') NOT NULL, -- Type of room
    capacity INT NOT NULL DEFAULT 1, -- Number of students the room can accommodate
    price DECIMAL(10, 2) NOT NULL, -- Price per month or semester
    features TEXT, -- Description of room features (e.g., 'Air-conditioned, Attached bathroom, Wi-Fi')
    availability_status ENUM('Available', 'Occupied', 'Pending Confirmation', 'Under Maintenance', 'Reserved') NOT NULL DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (block_id) REFERENCES hostel_blocks(id) ON DELETE CASCADE, -- Link to hostel_blocks table
    UNIQUE KEY `unique_room_in_block` (`block_id`, `room_number`) -- Ensures room number is unique within a block
);

-- Hostel Registrations table
CREATE TABLE IF NOT EXISTS hostel_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    room_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- When the student applied
    requested_check_in_date DATE, -- Student's preferred check-in date
    approved_check_in_date DATE DEFAULT NULL, -- Actual check-in date upon approval
    approved_check_out_date DATE DEFAULT NULL, -- Expected check-out date (e.g., end of semester)
    status ENUM('Pending', 'Approved', 'Rejected', 'Cancelled by Student', 'Checked In', 'Checked Out', 'Payment Due', 'Expired') NOT NULL DEFAULT 'Pending',
    payment_status ENUM('Unpaid', 'Paid', 'Partially Paid', 'Refunded', 'Waived') NOT NULL DEFAULT 'Unpaid',
    total_amount DECIMAL(10, 2) DEFAULT NULL, -- Total amount for the stay period
    paid_amount DECIMAL(10, 2) DEFAULT 0.00, -- Amount paid by the student
    notes TEXT DEFAULT NULL, -- Any notes from student or admin regarding the registration
    admin_id INT DEFAULT NULL, -- Admin who processed the registration
    processed_at TIMESTAMP NULL DEFAULT NULL, -- When the admin processed the request
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE RESTRICT, -- Prevent deleting a room if active registrations exist
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL -- If an admin account is deleted, keep the record but nullify admin_id
);