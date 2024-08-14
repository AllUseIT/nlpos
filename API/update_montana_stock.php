<?php
include "db.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the ID is provided and is a number
    if (isset($_POST["id"]) && is_numeric($_POST["id"])) {
        $id = $_POST["id"];

        // Check if addStock or totalStock is provided and is a number
        if (isset($_POST["addStock"]) && is_numeric($_POST["addStock"])) {
            $addStock = $_POST["addStock"];
            // Update the stock by adding the provided value
            $query = "UPDATE montana SET stock = stock + $addStock WHERE id = $id";
        } elseif (isset($_POST["totalStock"]) && is_numeric($_POST["totalStock"])) {
            $totalStock = $_POST["totalStock"];
            // Update the total stock with the provided value
            $query = "UPDATE montana SET stock = $totalStock WHERE id = $id";
        } else {
            // If neither addStock nor totalStock is provided or not numeric, return error
            http_response_code(400);
            echo json_encode(array("message" => "Invalid input"));
            exit;
        }

        // Execute the query
        if (mysqli_query($conn, $query)) {
            // Return success message
            echo json_encode(array("message" => "Stock updated successfully"));
        } else {
            // If query execution fails, return error
            http_response_code(500);
            echo json_encode(array("message" => "Error updating stock"));
        }
    } else {
        // If ID is not provided or not a number, return error
        http_response_code(400);
        echo json_encode(array("message" => "Invalid input"));
    }
} else {
    // If request method is not POST, return error
    http_response_code(405);
    echo json_encode(array("message" => "Method Not Allowed"));
}
?>
