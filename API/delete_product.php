<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $barcode_2 = $_POST['barcode_2'];

    if (!empty($barcode_2)) {
        $tables = ['products', 'montana', 'centurion', 'zambezi', 'daspoort', 'menlyn'];
        $conn->begin_transaction();

        try {
            foreach ($tables as $table) {
                $sql = "DELETE FROM $table WHERE barcode_2 = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $barcode_2);
                if (!$stmt->execute()) {
                    throw new Exception("Error deleting from $table: " . $conn->error);
                }
                $stmt->close();
            }
            $conn->commit();
            echo "Product deleted successfully from all tables.";
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
        }
    } else {
        echo "Invalid barcode_2.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
