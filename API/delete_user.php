<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["id"])) {
        include 'db.php';

        // Prepare and execute the delete statement
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i", $_POST["id"]);

        if ($stmt->execute()) {
            echo "User deleted successfully.";
        } else {
            echo "Failed to delete user: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "No user ID provided.";
    }
} else {
    echo "Invalid request method.";
}
?>
