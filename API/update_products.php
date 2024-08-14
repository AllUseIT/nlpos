<?php
// Include database connection
include 'db.php';

// Function to execute update query for each table
function updateTable($conn, $table, $description, $category, $main_price, $dis_price, $p3_price, $barcode, $barcode_2) {
    $stmt = $conn->prepare("UPDATE $table SET description=?, category=?, main_price=?, dis_price=?, p3_price=?, barcode=? WHERE barcode_2=?");
    if ($stmt) {
        $stmt->bind_param("ssdddss", $description, $category, $main_price, $dis_price, $p3_price, $barcode, $barcode_2);
        if ($stmt->execute()) {
            $result = $stmt->affected_rows;
        } else {
            $result = "Error executing update query: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $result = "Error preparing update statement: " . $conn->error;
    }
    return $result;
}

// Check if POST data is received
if (
    isset($_POST['description']) && 
    isset($_POST['category']) && 
    isset($_POST['main_price']) && 
    isset($_POST['dis_price']) && 
    isset($_POST['p3_price']) && 
    isset($_POST['barcode']) && 
    isset($_POST['barcode_2'])
) {
    // Sanitize and validate POST data
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $main_price = floatval($_POST['main_price']);
    $dis_price = floatval($_POST['dis_price']);
    $p3_price = floatval($_POST['p3_price']);
    $barcode = mysqli_real_escape_string($conn, $_POST['barcode']);
    $barcode_2 = mysqli_real_escape_string($conn, $_POST['barcode_2']);

    // Tables to update
    $tables = ['products', 'montana', 'menlyn', 'zambezi', 'centurion', 'daspoort'];

    // Track results
    $results = [];

    // Update each table
    foreach ($tables as $table) {
        $result = updateTable($conn, $table, $description, $category, $main_price, $dis_price, $p3_price, $barcode, $barcode_2);
        $results[$table] = $result;
    }

    // Check if any updates were successful
    $success = false;
    foreach ($results as $result) {
        if (is_int($result) && $result > 0) {
            $success = true;
            break;
        }
    }

    if ($success) {
        $response = ['status' => 'success', 'message' => 'Product updated successfully in all branches.'];
    }
    echo json_encode($response);

    // Close database connection
    mysqli_close($conn);
} else {
    // Incomplete data received
    $response = ['status' => 'error', 'message' => 'Incomplete data received.'];
    echo json_encode($response);
}
?>
