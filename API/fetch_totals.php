<?php
include "db.php";

// Get the selected branch from the URL parameter
$selectedBranch = $_GET['branch'];

// Fetch data from sale_info table for the selected branch
$sql = "SELECT SUM(info_total) AS total_sales, 
               SUM(amount_given) AS total_amount_given, 
               SUM(change_given) AS total_change_given 
        FROM sale_info
        WHERE info_branch = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $selectedBranch);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

// Format amounts to two decimal places
$totalSales = number_format($row['total_sales'], 2);
$totalAmountGiven = number_format($row['total_amount_given'], 2);
$totalChangeGiven = number_format($row['total_change_given'], 2);

// Close the connection
mysqli_stmt_close($stmt);
mysqli_close($conn);

// Return totals as JSON response
header('Content-Type: application/json');
echo json_encode(array(
    'total_sales' => $totalSales,
    'total_amount_given' => $totalAmountGiven,
    'total_change_given' => $totalChangeGiven
));
?>
