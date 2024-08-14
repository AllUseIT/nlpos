<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection details
$servername = "localhost"; // Change if your database server is different
$username = "noirscen_shaun"; // Your database username
$password = "C@nd3r3l231272"; // Your database password
$dbname = "noirscen_test_db"; // Your database name

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for a connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    $onlineStatus = "Online";
}
?>
