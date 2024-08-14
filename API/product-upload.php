<?php
// Check if file was uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {
    $file = $_FILES['fileToUpload']['tmp_name'];

    // Validate file type
    $file_type = pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION);
    if ($file_type !== 'csv') {
        die(json_encode(array("error" => true, "error_info" => ["Only CSV files are allowed."])));
    }

    // Read the CSV file
    $handle = fopen($file, "r");
    if ($handle !== FALSE) {
        include "db.php"; // Include your database connection

        // Prepare statements for each table
        $stmt_products = $conn->prepare("INSERT INTO products (description, category, main_price, dis_price, p3_price, barcode, stock, barcode_2) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_montana = $conn->prepare("INSERT INTO montana (description, category, main_price, dis_price, p3_price, barcode, stock, barcode_2) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_daspoort = $conn->prepare("INSERT INTO daspoort (description, category, main_price, dis_price, p3_price, barcode, stock, barcode_2) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_menlyn = $conn->prepare("INSERT INTO menlyn (description, category, main_price, dis_price, p3_price, barcode, stock, barcode_2) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_zambezi = $conn->prepare("INSERT INTO zambezi (description, category, main_price, dis_price, p3_price, barcode, stock, barcode_2) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_centurion = $conn->prepare("INSERT INTO centurion (description, category, main_price, dis_price, p3_price, barcode, stock, barcode_2) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        // Check if prepared statements were successful
        if (!$stmt_products || !$stmt_montana || !$stmt_daspoort || !$stmt_menlyn || !$stmt_zambezi || !$stmt_centurion) {
            die(json_encode(array("error" => true, "error_info" => ["Error preparing statement: " . $conn->error])));
        }

        // Bind parameters for each table
        $stmt_products->bind_param("ssdddsis", $description, $category, $main_price, $dis_price, $p3_price, $barcode, $stock, $barcode_2);
        $stmt_montana->bind_param("ssdddsis", $description, $category, $main_price, $dis_price, $p3_price, $barcode, $stock, $barcode_2);
        $stmt_daspoort->bind_param("ssdddsis", $description, $category, $main_price, $dis_price, $p3_price, $barcode, $stock, $barcode_2);
        $stmt_menlyn->bind_param("ssdddsis", $description, $category, $main_price, $dis_price, $p3_price, $barcode, $stock, $barcode_2);
        $stmt_zambezi->bind_param("ssdddsis", $description, $category, $main_price, $dis_price, $p3_price, $barcode, $stock, $barcode_2);
        $stmt_centurion->bind_param("ssdddsis", $description, $category, $main_price, $dis_price, $p3_price, $barcode, $stock, $barcode_2);

        // Array to track duplicate barcode_2 values
        $duplicate_errors = array();
        $error_occurred = false; // Flag to track if any error occurred
        $upload_results = array(); // Array to track upload results

        // Read and insert each line of the CSV file
        $current_row = 0;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $current_row++;

            // Check if the number of columns in the CSV row matches expectations
            if (count($data) < 8) {
                $duplicate_errors[] = "Skipped row {$current_row}: Insufficient columns.";
                $error_occurred = true; // Set flag to true
                continue; // Skip this row if it doesn't have enough columns
            }

            // Assign variables from CSV data
            $description = isset($data[0]) ? $data[0] : '';
            $category = isset($data[1]) ? $data[1] : '';
            $main_price = isset($data[2]) ? floatval(str_replace(',', '', $data[2])) : 0;
            $dis_price = isset($data[3]) ? floatval(str_replace(',', '', $data[3])) : 0;
            $p3_price = isset($data[4]) ? floatval(str_replace(',', '', $data[4])) : 0;
            $barcode = isset($data[5]) ? $data[5] : '';
            $stock = isset($data[6]) ? intval($data[6]) : 0;
            $barcode_2 = isset($data[7]) ? $data[7] : '';

            // Check if essential fields are not empty
            if (empty($description) || empty($category)) {
                $duplicate_errors[] = "Skipped row {$current_row}: Description or Category is empty.";
                $error_occurred = true; // Set flag to true
                continue; // Skip this row if essential fields are missing
            }

            // Check for duplicates in the database based on barcode_2
            $stmt_check_duplicate = $conn->prepare("SELECT id FROM products WHERE barcode_2 = ?");
            $stmt_check_duplicate->bind_param("s", $barcode_2);
            $stmt_check_duplicate->execute();
            $stmt_check_duplicate->store_result();
            if ($stmt_check_duplicate->num_rows > 0) {
                $duplicate_errors[] = "Skipped row {$current_row}: Duplicate entry found for barcode_2 '{$barcode_2}'.";
                $error_occurred = true; // Set flag to true
                continue; // Skip this row if barcode_2 already exists
            }

            // Execute the prepared statements for each table
            if ($stmt_products->execute() && $stmt_montana->execute() && $stmt_daspoort->execute() &&
                $stmt_menlyn->execute() && $stmt_zambezi->execute() && $stmt_centurion->execute()) {
                $upload_results[] = array("status" => "success", "message" => "Inserted row {$current_row} successfully.", "data" => $data);
            } else {
                $duplicate_errors[] = "Error executing statement for row {$current_row}: " . $conn->error;
                $error_occurred = true; // Set flag to true
            }

            // Flush the output buffer
            ob_flush();
            flush();
        }

        // Close statements and connection
        $stmt_products->close();
        $stmt_montana->close();
        $stmt_daspoort->close();
        $stmt_menlyn->close();
        $stmt_zambezi->close();
        $stmt_centurion->close();
        $conn->close();

        // Output JSON response with error and success information
        if ($error_occurred || !empty($duplicate_errors)) {
            echo json_encode(array("error" => true, "error_info" => $duplicate_errors, "upload_results" => $upload_results));
        } else {
            echo json_encode(array("error" => false, "upload_results" => $upload_results));
        }
    } else {
        echo json_encode(array("error" => true, "error_info" => ["Error opening file."]));
    }
} else {
    echo json_encode(array("error" => true, "error_info" => ["No file selected."]));
}
?>
