<?php
include "db.php";

// Retrieve POST data
$saleNr = $_POST['saleNr'];

// Check if sale number exists in sale_info table
$sql = "SELECT COUNT(*) as count FROM `sale_info` WHERE `sale_nr` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $saleNr);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Return true if sale number exists, otherwise false
$exists = ($row['count'] > 0);

echo json_encode(['exists' => $exists]);

$stmt->close();
$conn->close();
?>
