<?php

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

include "db.php";

// Check if the table exists
$table_check_sql = "SHOW TABLES LIKE 'sale_info'";
$table_check_result = $conn->query($table_check_sql);

if ($table_check_result->num_rows == 0) {
    die("Table 'sale_info' does not exist in the database.");
}

// Query to fetch sales data
$sql = "SELECT
            id,
            info_username AS username,
            info_branch AS branch,
            info_total AS total,
            info_date_time AS sale_date,
            info_pay_method AS method,
            sale_nr AS sale_number
        FROM sale_info
        ORDER BY info_date_time DESC";

$result = $conn->query($sql);

if ($result === false) {
    die("SQL error: " . $conn->error);
}

if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row["username"]) . "</td>
                <td>" . htmlspecialchars($row["branch"]) . "</td>
                <td>" . htmlspecialchars($row["sale_date"]) . "</td>
                <td>$" . htmlspecialchars(number_format($row["total"], 2)) . "</td>
                <td>" . htmlspecialchars($row["method"]) . "</td>
                <td>" . htmlspecialchars($row["sale_number"]) . "</td>
                <td><button class='view-transactions' data-sale-number='" . htmlspecialchars($row["sale_number"]) . "'>View</button></td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No sales found</td></tr>";
}

$conn->close();
?>
