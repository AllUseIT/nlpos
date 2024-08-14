<?php
include "db.php"; 

// Retrieve POST data
$saleInfoData = $_POST['saleInfoData'];
$transactionsData = $_POST['transactionsData'];
$amountGiven = $_POST['amountGiven']; // New POST data for amount given
$changeGiven = $_POST['changeGiven']; // New POST data for change given

// Get the current branch from session
session_start();
$branch = $_SESSION['branch'];

// Insert into sale_info table
$sqlSaleInfo = "INSERT INTO `sale_info`(`info_username`, `info_branch`, `info_total`, `info_date_time`, `info_pay_method`, `sale_nr`, `amount_given`, `change_given`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmtSaleInfo = $conn->prepare($sqlSaleInfo);
$stmtSaleInfo->bind_param("ssdsssdd", $saleInfoData['info_username'], $branch, $saleInfoData['info_total'], $saleInfoData['info_date_time'], $saleInfoData['info_pay_method'], $saleInfoData['sale_nr'], $amountGiven, $changeGiven);
$stmtSaleInfo->execute();

// Insert into transactions table
$sqlTransactions = "INSERT INTO `transactions`(`qty`, `description`, `price`, `category`, `barcode`, `sale_nr`) VALUES (?, ?, ?, ?, ?, ?)";
$stmtTransactions = $conn->prepare($sqlTransactions);

foreach ($transactionsData as $transaction) {
    $stmtTransactions->bind_param("isdssi", $transaction['qty'], $transaction['description'], $transaction['price'], $transaction['category'], $transaction['barcode'], $transaction['sale_nr']);
    $stmtTransactions->execute();

    // Update the stock of the product in the current branch
    $sqlUpdateStock = "UPDATE `$branch` SET `stock` = `stock` - ? WHERE `barcode` = ?";
    $stmtUpdateStock = $conn->prepare($sqlUpdateStock);
    $stmtUpdateStock->bind_param("is", $transaction['qty'], $transaction['barcode']);
    $stmtUpdateStock->execute();
}

// Check if all queries were successful
if ($stmtSaleInfo && $stmtTransactions && $stmtUpdateStock) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

$stmtSaleInfo->close();
$stmtTransactions->close();
$stmtUpdateStock->close();
$conn->close();
?>
