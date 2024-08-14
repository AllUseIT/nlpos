<?php
include_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $password = hash("sha512", $_POST["password"]);  // Hashing the password using SHA-512
    $job = $_POST["job"];
    $branch = $_POST["branch"];
    $active = $_POST["active"];

    $sql = "INSERT INTO `users`(`username`, `name`, `surname`, `password`, `job`, `branch`, `active`) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $username, $name, $surname, $password, $job, $branch, $active);

    if ($stmt->execute()) {
        echo "User created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
