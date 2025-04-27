<?php
session_start();
require_once 'db_connection.php';

$error = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';
    $role = $_POST["role"] ?? '';

    // Validate login credentials
    if (!empty($username) && !empty($password) && !empty($role)) {
        if ($role === "admin") {
            $stmt = $conn->prepare("SELECT id, name, password FROM admins WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $admin = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $admin['password'])) {
                    // Set session variables
                    $_SESSION["user_id"] = $admin['id'];
                    $_SESSION["user"] = $admin['name'];
                    $_SESSION["role"] = "admin";
                    
                    // Redirect to dashboard
                    header("Location: admin_dashboard.php");
                    exit();
                }
            }
        } else if ($role === "student") {
            $stmt = $conn->prepare("SELECT id, name, password FROM students WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $student = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $student['password'])) {
                    // Set session variables
                    $_SESSION["user_id"] = $student['id'];
                    $_SESSION["user"] = $student['name'];
                    $_SESSION["role"] = "student";
                    
                    // Redirect to dashboard
                    header("Location: student_dashboard.php");
                    exit();
                }
            }
        }
        
        // If we reach here, authentication failed
        $error = true;
    } else {
        $error = true;
    }
    
    // If error, redirect back to index with error parameter
    if ($error) {
        header("Location: index.php?error=1");
        exit();
    }
}
?>

