<?php
include "./API/db.php";

// Start the session
session_start();

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
    exit();
}

// Fetch transactions for the latest sale
$saleNr = $saleRow['sale_nr'];
$transactionQuery = "SELECT * FROM transactions WHERE sale_nr = '$saleNr'";
$transactionResult = $conn->query($transactionQuery);

// Initialize total variable
$total = 0;

// Receipt content
$receiptContent = '<pre>'; 
$receiptContent .= "-------------------------\n";
$receiptContent .= "       Receipt\n";
$receiptContent .= "-------------------------\n";
$receiptContent .= "BEE: 1279100127927\n";
$receiptContent .= "Sale Number: " . $saleRow['sale_nr'] . "\n";
$receiptContent .= "Date/Time: " . $saleRow['info_date_time'] . "\n";
$receiptContent .= "Cashier: " . $saleRow['info_username'] . "\n";
$receiptContent .= "Branch: " . $saleRow['info_branch'] . "\n";
$receiptContent .= "Amount Given: R" . $saleRow['amount_given'] . "\n"; // Amount Given
$receiptContent .= "Change Given: R" . $saleRow['change_given'] . "\n"; // Change Given
$receiptContent .= "-------------------------\n";
$receiptContent .= "Transactions:\n";
$receiptContent .= "-------------------------\n";

while ($transactionRow = $transactionResult->fetch_assoc()) {
    $receiptContent .= $transactionRow['description'] . "\n";
    $receiptContent .= $transactionRow['barcode'] . "\n";
    $receiptContent .= $transactionRow['qty'] . "\n"; 
    $receiptContent .= "R" . $transactionRow['price'] . "\n";
    $receiptContent .= "-------------------------\n";

    // Calculate total
    $total += $transactionRow['price'];
}

// Fetch total amount and payment method from sale_info table
$totalAmount = $saleRow['info_total'];
$paymentMethod = $saleRow['info_pay_method'];

$receiptContent .= "-------------------------\n";
$receiptContent .= "Vat: 15%\n";
$receiptContent .= "Total: R" . $totalAmount . "\n";  // Use total amount from sale_info table
$receiptContent .= "Payment Method: " . $paymentMethod . "\n"; // Payment Method
$receiptContent .= "-------------------------\n";
$receiptContent .= "TERMS AND CONDITIONS\n";
$receiptContent .= "-------------------------\n";
$receiptContent .= "1)Please retain proof of purchase for exchanges.\n";
$receiptContent .= "2)Tags must not be removed for exchanges.\n";
$receiptContent .= "3)Exchanges only within 7 days.\n";
$receiptContent .= "4)Strictly no Cash Refunds.\n";
$receiptContent .= "5)No Cancellation & no Exchanges on Layby Items.\n";
$receiptContent .= "6)Thank you for shopping with us.\n";
$receiptContent .= '</pre>';

echo $receiptContent;

// JavaScript to trigger print
echo '<script>window.print();</script>';
?>
