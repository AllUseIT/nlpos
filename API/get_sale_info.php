<?php
// Include database connection
include 'db.php';

// Get sale number from the request
$sale_number = $_GET['sale_number'];

// Prepare the SQL statement
$sql = "SELECT * FROM sale_info WHERE sale_nr = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sale_number);
$stmt->execute();
$result = $stmt->get_result();

// Fetch sale info
$sale_info = $result->fetch_assoc();

// Close connection
$stmt->close();
$conn->close();

// Output sale info as JSON
header('Content-Type: application/json');
echo json_encode($sale_info);
?>
