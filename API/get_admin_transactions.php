<?php
// Include database connection
include 'db.php';

// Get sale number from the request
$sale_number = $_GET['sale_number'];

// Prepare the SQL statement
$sql = "SELECT * FROM transactions WHERE sale_nr = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sale_number);
$stmt->execute();
$result = $stmt->get_result();

// Output transaction rows
while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['qty']}</td>
            <td>{$row['description']}</td>
            <td>\${$row['price']}</td>
            <td>{$row['category']}</td>
            <td>{$row['barcode']}</td>
            <td>{$row['sale_nr']}</td>
          </tr>";
}

// Close connection
$stmt->close();
$conn->close();
?>
