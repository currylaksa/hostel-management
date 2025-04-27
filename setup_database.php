<?php
// Database setup script
// Run this file once to set up the database and tables

$host = "localhost";
$username = "root"; // Change as needed
$password = ""; // Change as needed

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected to MySQL server successfully.<br>";

// Read SQL file
$sql = file_get_contents('database_setup.sql');

// Execute multi query
if ($conn->multi_query($sql)) {
    echo "Database and tables created successfully!<br>";
    
    // Create upload directory for profile pictures if it doesn't exist
    if (!file_exists('uploads/profile_pics/')) {
        if (mkdir('uploads/profile_pics/', 0777, true)) {
            echo "Uploads directory created successfully.<br>";
        } else {
            echo "Failed to create uploads directory.<br>";
        }
    }
    
    echo "<p>Your database is now set up! You can now <a href='index.html'>login to the system</a>.</p>";
} else {
    echo "Error creating database: " . $conn->error;
}

$conn->close();
?>