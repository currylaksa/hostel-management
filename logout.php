<?php
session_start();

// Destroy the session
session_destroy();

// Redirect to the homepage
header("Location: index.php");
exit();
?>