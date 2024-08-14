<?php
include "db.php";

// Fetch latest sale information for the current user session
$saleNr = $_GET['sale_nr']; // Ensure proper validation and sanitization

// Fetch sale information
$saleQuery = "SELECT * FROM `sale_info` WHERE sale_nr = '$saleNr'";
$saleResult = $conn->query($saleQuery);
$saleRow = $saleResult->fetch_assoc();

if (!$saleRow) {
    echo "No sale found.";
    exit();
}

// Fetch transactions for the sale
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
$receiptContent .= "Amount Given: R" . $saleRow['amount_given'] . "\n";
$receiptContent .= "Change Given: R" . $saleRow['change_given'] . "\n";
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

$receiptContent .= "-------------------------\n";
$receiptContent .= "Vat: 15%\n";
$receiptContent .= "Total: R" . $total . "\n";  // Use total amount from transactions
$receiptContent .= "Payment Method: " . $saleRow['info_pay_method'] . "\n";
$receiptContent .= "-------------------------\n";
$receiptContent .= "TERMS AND CONDITIONS\n";
$receiptContent .= "-------------------------\n";
$receiptContent .= "1) Please retain proof of purchase for exchanges.\n";
$receiptContent .= "2) Tags must not be removed for exchanges.\n";
$receiptContent .= "3) Exchanges only within 7 days.\n";
$receiptContent .= "4) Strictly no Cash Refunds.\n";
$receiptContent .= "5) No Cancellation & no Exchanges on Layby Items.\n";
$receiptContent .= "6) Thank you for shopping with us.\n";
$receiptContent .= '</pre>';

echo $receiptContent;
?>
