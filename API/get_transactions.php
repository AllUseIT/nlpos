<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db.php";

// Handle POST requests only
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and validate sale number from POST request
    $sale_nr = filter_input(INPUT_POST, 'sale_nr', FILTER_VALIDATE_INT);

    if (!$sale_nr) {
        echo json_encode(['error' => 'Invalid sale number']);
        exit;
    }

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("SELECT * FROM transactions WHERE sale_nr = ?");
    if (!$stmt) {
        echo json_encode(['error' => 'SQL prepare failed: ' . $conn->error]);
        exit;
    }

    // Bind the sale number parameter
    $stmt->bind_param("i", $sale_nr);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        // Check if there are results
        if ($result->num_rows > 0) {
            $transactions = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($transactions);
        } else {
            echo json_encode(['error' => 'No transactions found']);
        }
    } else {
        echo json_encode(['error' => 'Execution failed: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
?>
