<?php
session_start();

// Admin credentials
$valid_admin = [
    "username" => "admin",
    "password" => "12345"
];

// Student credentials (in real application, this should be in a database)
$valid_students = [
    [
        "username" => "student1",
        "password" => "student123"
    ]
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';
    $role = $_POST["role"] ?? '';

    if ($role === "admin") {
        if ($username === $valid_admin["username"] && $password === $valid_admin["password"]) {
            $_SESSION["user"] = $username;
            $_SESSION["role"] = "admin";
            header("Location: admin_dashboard.php");
            exit();
        }
    } else if ($role === "student") {
        foreach ($valid_students as $student) {
            if ($username === $student["username"] && $password === $student["password"]) {
                $_SESSION["user"] = $username;
                $_SESSION["role"] = "student";
                header("Location: student_dashboard.php");
                exit();
            }
        }
    }
    
    // If no match found
    header("Location: index.html?error=1");
    exit();
}
?>

