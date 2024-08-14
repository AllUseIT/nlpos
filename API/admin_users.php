<?php
// Include your database connection file
include 'db.php';

// Fetch non-active users from the database
$sql = "SELECT * FROM users WHERE job = 'Admin'";
$result = mysqli_query($conn, $sql);

// Check if there are any non-active users
if (mysqli_num_rows($result) > 0) {
    // Output data of each non-active user
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['surname'] . "</td>";
        echo "<td>" . $row['branch'] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3'>No non-active users found</td></tr>";
}

// Close database connection
mysqli_close($conn);
?>
