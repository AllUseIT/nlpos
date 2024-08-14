<?php
include "db.php";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to count the number of products
$sql = "SELECT COUNT(*) as total FROM products";
$result = $conn->query($sql);

// Fetch the result
$data = $result->fetch_assoc();

// Output the count as JSON
echo json_encode($data);

// Close the connection
$conn->close();
?>
