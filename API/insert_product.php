<?php
include_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve POST data and sanitize
    $description = substr(trim($_POST["description"] ?? ""), 0, 255); // Truncate description to 255 characters
    $category = trim($_POST["category"] ?? "");
    $main_price = floatval($_POST["main_price"] ?? 0.0);
    $dis_price = floatval($_POST["dis_price"] ?? 0.0);
    $p3_price = floatval($_POST["p3_price"] ?? 0.0);
    $barcode = trim($_POST["barcode"] ?? "");
    $stock = 0; // Stock should always be 0

    // Ensure barcode_2 matches barcode
    $barcode_2 = $barcode;

    // Prepare SQL statement for main products table
    $sql_products = "INSERT INTO `products`(`description`, `category`, `main_price`, `dis_price`, `p3_price`, `barcode`, `barcode_2`, `stock`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_products = $conn->prepare($sql_products);

    // Bind parameters for main products table
    $stmt_products->bind_param("ssdddssi", $description, $category, $main_price, $dis_price, $p3_price, $barcode, $barcode_2, $stock);

    // Execute SQL statement for main products table
    if ($stmt_products->execute()) {
        echo "Product created successfully\n";
    } else {
        echo "Error: " . $stmt_products->error . "<br>";
        $stmt_products->close();
        $conn->close();
        exit; // Exit if main products table insert fails
    }

    // Insert into branch-specific tables
    $branch_tables = ['montana', 'zambezi', 'menlyn', 'centurion', 'daspoort'];

    foreach ($branch_tables as $branch) {
        $sql_branch = "INSERT INTO `$branch`(`description`, `category`, `main_price`, `dis_price`, `p3_price`, `barcode`, `barcode_2`, `stock`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_branch = $conn->prepare($sql_branch);

        // Bind parameters for branch-specific tables
        $stmt_branch->bind_param("ssdddssi", $description, $category, $main_price, $dis_price, $p3_price, $barcode, $barcode_2, $stock);

        // Execute SQL statement for branch-specific tables
        if ($stmt_branch->execute()) {
            echo "Inserted into $branch successfully\n";
        } else {
            echo "Error inserting into $branch: " . $stmt_branch->error . "<br>";
        }

        $stmt_branch->close();
    }

    // Close main products table statement and database connection
    $stmt_products->close();
    $conn->close();
}
?>
