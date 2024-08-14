<?php
include "db.php";

$response = [];

// Start session
session_start();

if(isset($_POST['barcode'])) {
    $barcode = $_POST['barcode'];

    // Check if branch is set in the session
    if(isset($_SESSION['branch'])) {
        $branch = $_SESSION['branch'];

        // Prepare SQL statement with the dynamically determined table name
        $stmt = $conn->prepare("SELECT `description`, `category`, `p3_price`, `barcode`, `stock` FROM $branch WHERE barcode = ?");
        $stmt->bind_param("s", $barcode);
        $stmt->execute();
        $result = $stmt->get_result();

        if($row = $result->fetch_assoc()) {
            $response = [
                'description' => $row['description'],
                'category' => $row['category'],
                'p3_price' => $row['p3_price'],
                'barcode' => $row['barcode']
            ];
        } else {
            $response = ['error' => 'Product not found in' . $branch];
        }
    } else {
        $response = ['error' => 'Branch not set in session'];
    }
} else {
    $response = ['error' => 'Barcode not provided'];
}

echo json_encode($response);
?>
