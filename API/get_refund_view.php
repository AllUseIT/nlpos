<?php
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sale_nr"])) {
    $saleNr = $_POST["sale_nr"];
    
    // Fetch transaction data including product ID
    $sqlTransactions = "SELECT * FROM transactions WHERE sale_nr = '$saleNr'";
    $resultTransactions = mysqli_query($conn, $sqlTransactions);

    if (!$resultTransactions) {
        echo "Error: " . mysqli_error($conn);
    } else {
        if (mysqli_num_rows($resultTransactions) > 0) {
            while ($row = mysqli_fetch_assoc($resultTransactions)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['qty'] . "</td>";
                echo "<td class='description'>" . $row['description'] . "</td>";
                echo "<td>" . "R" . $row['price'] . "</td>";
                echo "<td class='category'>" . $row['category'] . "</td>";
                echo "<td class='barcode'>" . $row['barcode'] . "</td>";
                echo "<td class='sale-nr'>" . $row['sale_nr'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "No transactions found for Sale $saleNr";
        }
        
        // Fetch sale_info data
        $sqlSaleInfo = "SELECT * FROM sale_info WHERE sale_nr = '$saleNr'";
        $resultSaleInfo = mysqli_query($conn, $sqlSaleInfo);
        if ($resultSaleInfo && mysqli_num_rows($resultSaleInfo) > 0) {
            $rowSaleInfo = mysqli_fetch_assoc($resultSaleInfo);
            // Display sale_info data
            echo "<script>";
            echo "document.querySelector('.transaction-popup-bottom-left-span:nth-child(1)').textContent = 'Payment Method: " . $rowSaleInfo['info_pay_method'] . "';";
            echo "document.querySelector('.transaction-popup-bottom-left-span:nth-child(2)').textContent = 'Total Price: R" . $rowSaleInfo['info_total'] . "';";
            echo "document.querySelector('.transaction-popup-bottom-left-span:nth-child(3)').textContent = 'Total Amount Given: R" . $rowSaleInfo['amount_given'] . "';";
            echo "document.querySelector('.transaction-popup-bottom-left-span:nth-child(4)').textContent = 'Total Change Given: R" . $rowSaleInfo['change_given'] . "';";
            echo "document.querySelector('.transaction-popup-bottom-right-span:nth-child(1)').textContent = 'Username: " . $rowSaleInfo['info_username'] . "';";
            echo "document.querySelector('.transaction-popup-bottom-right-span:nth-child(2)').textContent = 'Branch: " . $rowSaleInfo['info_branch'] . "';";
            echo "</script>";
        } else {
            echo "Sale info not found for Sale $saleNr";
        }
        
        mysqli_close($conn); // Close the connection after all queries are executed
    }
} else {
    echo "Invalid request.";
}
?>
