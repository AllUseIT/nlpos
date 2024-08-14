<?php
include "db.php";

session_start(); // Start session if not already started

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productId = $_POST["product_id"];
    $refundQuantity = $_POST["refund_quantity"];
    
    // Fetch data from transactions table based on product ID
    $sqlFetchTransactionData = "SELECT * FROM transactions WHERE id = '$productId'";
    $resultFetchTransactionData = mysqli_query($conn, $sqlFetchTransactionData);
    
    if ($resultFetchTransactionData && mysqli_num_rows($resultFetchTransactionData) > 0) {
        $row = mysqli_fetch_assoc($resultFetchTransactionData);
        
        // Extract necessary data from the transaction row
        $description = $row['description'];
        $category = $row['category'];
        $barcode = $row['barcode'];
        $saleNr = $row['sale_nr'];
        
        // Fetch current branch and username
        $currentUsername = $_SESSION["username"]; // Assuming username is stored in session after login
        $sqlFetchUserInfo = "SELECT branch FROM users WHERE username = '$currentUsername'";
        $resultFetchUserInfo = mysqli_query($conn, $sqlFetchUserInfo);
        
        if ($resultFetchUserInfo && mysqli_num_rows($resultFetchUserInfo) > 0) {
            $userInfo = mysqli_fetch_assoc($resultFetchUserInfo);
            $currentBranch = $userInfo['branch'];
            
            // Fetch price of the product
            $sqlFetchProductPrice = "SELECT price FROM transactions WHERE id = '$productId'";
            $resultFetchProductPrice = mysqli_query($conn, $sqlFetchProductPrice);
            
            if ($resultFetchProductPrice && mysqli_num_rows($resultFetchProductPrice) > 0) {
                $productPriceRow = mysqli_fetch_assoc($resultFetchProductPrice);
                $productPrice = $productPriceRow['price'];
                
                // Calculate refund total
                $refundTotal = $productPrice * $refundQuantity;
                
                // Get current date and time
                $currentDateTime = date("Y-m-d H:i:s");
                
                // Insert refund data into refunds table including current date and time
                $sqlInsertRefund = "INSERT INTO refunds (refund_username, refund_branch, refund_description, refund_category, refund_barcode, refund_sale_nr, refund_quantity, refund_price, refund_total, refund_date) VALUES ('$currentUsername', '$currentBranch', '$description', '$category', '$barcode', '$saleNr', '$refundQuantity', '$productPrice', '$refundTotal', '$currentDateTime')";
                
                if (mysqli_query($conn, $sqlInsertRefund)) {
                    // Update the corresponding branch table based on the current branch
                    $branchTable = strtolower($currentBranch);
                    $sqlUpdateBranchTable = "UPDATE $branchTable SET stock = stock + '$refundQuantity' WHERE barcode = '$barcode'";
                    
                    if (mysqli_query($conn, $sqlUpdateBranchTable)) {
                        echo json_encode(array("success" => true));
                    } else {
                        echo json_encode(array("success" => false, "message" => "Error updating branch table: " . mysqli_error($conn)));
                    }
                } else {
                    echo json_encode(array("success" => false, "message" => "Error inserting refund: " . mysqli_error($conn)));
                }
            } else {
                echo json_encode(array("success" => false, "message" => "Failed to fetch product price."));
            }
        } else {
            echo json_encode(array("success" => false, "message" => "Failed to fetch current user's branch."));
        }
    } else {
        echo json_encode(array("success" => false, "message" => "Transaction data not found for product ID: $productId"));
    }

    mysqli_close($conn); // Close the connection
} else {
    echo json_encode(array("success" => false, "message" => "Invalid request."));
}
?>
