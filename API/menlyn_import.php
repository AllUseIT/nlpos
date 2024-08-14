<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['fileToUpload'])) {
        // Validate file type
        $file_type = pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION);
        if ($file_type !== 'csv') {
            echo json_encode([
                'error' => true,
                'error_info' => ['Only CSV files are allowed.']
            ]);
            exit; // Stop further execution
        }

        $file = $_FILES['fileToUpload']['tmp_name'];
        $handle = fopen($file, 'r');
        $row = 0;

        $response = [
            'error' => false,
            'error_info' => [],
            'upload_results' => []
        ];

        if ($handle !== FALSE) {
            include 'db.php'; // Assuming db.php contains the database connection

            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $row++;

                // Check if the number of columns in the CSV row matches expectations
                if (count($data) < 2) { // Adjust this as per your CSV structure
                    $response['error'] = true;
                    $response['error_info'][] = "Skipped row {$row}: Insufficient columns.";
                    continue; // Skip this row if it doesn't have enough columns
                }

                // Assign variables from CSV data
                $barcode = isset($data[0]) ? $data[0] : '';
                $stock = isset($data[1]) ? intval($data[1]) : 0;

                // Validate data (add more validation as needed)
                if (empty($barcode) || $stock <= 0) {
                    $response['error'] = true;
                    $response['error_info'][] = "Invalid data in row {$row}.";
                    continue; // Skip this row if data is invalid
                }

                // Update stock in the montana table
                $sql = "UPDATE menlyn SET stock = ? WHERE barcode = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    $response['error'] = true;
                    $response['error_info'][] = 'Database error: ' . $conn->error;
                    break; // Exit the loop if there's a database error
                }
                $stmt->bind_param('is', $stock, $barcode);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $response['upload_results'][] = [
                            'status' => 'success',
                            'message' => "Stock updated for barcode: {$barcode}",
                            'data' => [$barcode, $stock]
                        ];
                    } else {
                        $response['error'] = true;
                        $response['error_info'][] = "No rows updated for barcode: {$barcode}";
                    }
                } else {
                    $response['error'] = true;
                    $response['error_info'][] = "Error updating stock for barcode: {$barcode}";
                }
                $stmt->close();
            }
            fclose($handle);
            $conn->close();
        } else {
            $response['error'] = true;
            $response['error_info'][] = 'Error opening file.';
        }

        // Output JSON response with error and success information
        echo json_encode($response);
    } else {
        echo json_encode([
            'error' => true,
            'error_info' => ['No file uploaded.']
        ]);
    }
} else {
    echo json_encode([
        'error' => true,
        'error_info' => ['Invalid request method.']
    ]);
}
?>
