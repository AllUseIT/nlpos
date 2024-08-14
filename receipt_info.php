<?php
include "./API/db.php";


// Check if the user is not logged in
if(!isset($_SESSION['username'])) {
    echo "User not logged in.";
    exit();
}

$username = $_SESSION['username'];
$branch = $_SESSION['branch'];

// Fetch latest sale information for the current user session
$saleQuery = "SELECT * FROM `sale_info` WHERE info_username = '$username' ORDER BY info_date_time DESC LIMIT 1";
$saleResult = $conn->query($saleQuery);
$saleRow = $saleResult->fetch_assoc();

if (!$saleRow) {
    echo "No recent sale found for the current user session.";
}

// Fetch transactions for the latest sale
$saleNr = $saleRow['sale_nr'];
$transactionQuery = "SELECT * FROM transactions WHERE sale_nr = '$saleNr'";
$transactionResult = $conn->query($transactionQuery);

// Initialize total variable
$total = 0;

// Receipt content
$receiptContent = '<div>'; 
$receiptContent .= "<h2>Newlands<br></h2>";
$receiptContent .= "-------------------------<br>";
$receiptContent .= "       Receipt<br>";
$receiptContent .= "-------------------------<br>";
$receiptContent .= "Sale Number: " . $saleRow['sale_nr'] . "<br>";
$receiptContent .= "Date/Time: " . $saleRow['info_date_time'] . "<br>";
$receiptContent .= "Username: " . $saleRow['info_username'] . "<br>";
$receiptContent .= "Branch: " . $saleRow['info_branch'] . "<br>";
$receiptContent .= "-------------------------<br>";
$receiptContent .= "Transactions:<br>";
$receiptContent .= "-------------------------<br>";

while ($transactionRow = $transactionResult->fetch_assoc()) {
    $receiptContent .= "Description: " . $transactionRow['description'] . "<br>";
    $receiptContent .= "Price: R" . $transactionRow['price'] . "<br>";
    $receiptContent .= "Barcode: " . $transactionRow['barcode'] . "<br>";
    $receiptContent .= "Category: " . $transactionRow['category'] . "<br>";
    $receiptContent .= "Quantity: " . $transactionRow['qty'] . "<br>"; 
    $receiptContent .= "-------------------------<br>";

    // Calculate total
    $total += $transactionRow['price'];
}

// Fetch total amount and payment method from sale_info table
$totalAmount = $saleRow['info_total'];
$paymentMethod = $saleRow['info_pay_method'];

$receiptContent .= "-------------------------<br>";
$receiptContent .= "Total Tax: 15%<br>";
$receiptContent .= "Payment Method: " . $paymentMethod . "<br>"; // Payment Method
$receiptContent .= "Amount Given: R" . $saleRow['amount_given'] . "<br>"; // Amount Given
$receiptContent .= "Change Given: R" . $saleRow['change_given'] . "<br>"; // Change Given
$receiptContent .= "Total Amount: R" . $totalAmount . "<br>";  // Use total amount from sale_info table
$receiptContent .= "-------------------------<br>";
$receiptContent .= '</div>';

echo $receiptContent;

?>
