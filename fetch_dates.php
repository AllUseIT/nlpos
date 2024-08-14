<?php
// Include database connection
include "./API/db.php";

// Get the parameters from the AJAX request
$selectedBranch = isset($_GET['branch']) ? $_GET['branch'] : '';
$fromDate = isset($_GET['fromDate']) ? $_GET['fromDate'] : date('Y-m-d');
$toDate = isset($_GET['toDate']) ? $_GET['toDate'] : date('Y-m-d');

// Sanitize input to prevent SQL injection
$selectedBranch = mysqli_real_escape_string($conn, $selectedBranch);
$fromDate = mysqli_real_escape_string($conn, $fromDate);
$toDate = mysqli_real_escape_string($conn, $toDate);

// Check if "All Branches" is selected and adjust the SQL query accordingly
if ($selectedBranch === 'All Branches' || $selectedBranch === '') {
    // Query without the branch filter to combine all branches
    $sql = "SELECT 
                SUM(info_total) AS total_sales, 
                SUM(amount_given) AS total_amount_given, 
                SUM(change_given) AS total_change_given,
                SUM(CASE WHEN info_pay_method = 'Card' THEN info_total ELSE 0 END) AS card_payments,
                SUM(CASE WHEN info_pay_method = 'EFT' THEN info_total ELSE 0 END) AS eft_payments,
                SUM(CASE WHEN info_pay_method = 'Cash' THEN info_total ELSE 0 END) AS cash_payments
            FROM sale_info 
            WHERE info_date_time BETWEEN '$fromDate 00:00:00' AND '$toDate 23:59:59'";
} else {
    // Query with the branch filter for a specific branch
    $sql = "SELECT 
                SUM(info_total) AS total_sales, 
                SUM(amount_given) AS total_amount_given, 
                SUM(change_given) AS total_change_given,
                SUM(CASE WHEN info_pay_method = 'Card' THEN info_total ELSE 0 END) AS card_payments,
                SUM(CASE WHEN info_pay_method = 'EFT' THEN info_total ELSE 0 END) AS eft_payments,
                SUM(CASE WHEN info_pay_method = 'Cash' THEN info_total ELSE 0 END) AS cash_payments
            FROM sale_info 
            WHERE info_branch = '$selectedBranch' 
            AND info_date_time BETWEEN '$fromDate 00:00:00' AND '$toDate 23:59:59'";
}

// Execute the query
$result = mysqli_query($conn, $sql);

if ($result) {
    // Fetch the result
    $row = mysqli_fetch_assoc($result);

    // Handle cases where the query returns no data
    if (!$row) {
        $row = [
            'total_sales' => 0,
            'total_amount_given' => 0,
            'total_change_given' => 0,
            'card_payments' => 0,
            'eft_payments' => 0,
            'cash_payments' => 0,
        ];
    }

    // Format amounts to two decimal places
    $response = [
        'total_sales' => number_format((float)$row['total_sales'], 2, '.', ''),
        'total_amount_given' => number_format((float)$row['total_amount_given'], 2, '.', ''),
        'total_change_given' => number_format((float)$row['total_change_given'], 2, '.', ''),
        'card_payments' => number_format((float)$row['card_payments'], 2, '.', ''),
        'eft_payments' => number_format((float)$row['eft_payments'], 2, '.', ''),
        'cash_payments' => number_format((float)$row['cash_payments'], 2, '.', ''),
    ];

    // Close the connection
    mysqli_close($conn);

    // Return response as JSON
    echo json_encode($response);
} else {
    // If query fails, log the error and return error message
    error_log("Database query failed: " . mysqli_error($conn));

    // Close the connection
    mysqli_close($conn);

    echo json_encode(['error' => 'Failed to fetch data']);
}
?>
