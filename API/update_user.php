<?php

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are present
    if (isset($_POST["id"]) && isset($_POST["username"]) && isset($_POST["name"]) && isset($_POST["surname"]) && isset($_POST["password"]) && isset($_POST["job"]) && isset($_POST["branch"]) && isset($_POST["active"])) {
        
        // Include your database connection file here
        include 'db.php';

        // Prepare and bind the update statement
        $stmt = $conn->prepare("UPDATE users SET username=?, name=?, surname=?, password=?, job=?, branch=?, active=? WHERE id=?");
        $stmt->bind_param("sssssssi", $username, $name, $surname, $hashed_password, $job, $branch, $active, $id);

        // Set parameters and execute
        $id = $_POST["id"];
        $username = $_POST["username"];
        $name = $_POST["name"];
        $surname = $_POST["surname"];
        $password = $_POST["password"];
        $hashed_password = hash("sha512", $password);  // Hash the password using SHA-512
        $job = $_POST["job"];
        $branch = $_POST["branch"];
        $active = $_POST["active"];

        if ($stmt->execute()) {
            echo ("User updated successfully.");
        } else {
            echo json_encode("Failed to update user. Refresh the page and try again.");
        }

        // Close statement and database connection
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode("Incomplete data received.");
    }
} else {
    echo json_encode("Invalid request method. Please contact Admin");
}
?>
