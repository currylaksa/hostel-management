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