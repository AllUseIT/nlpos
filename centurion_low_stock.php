<?php 
include "./API/db.php"; 

// Fetch all products from the database
$query = "SELECT * FROM `centurion` WHERE stock <= 10;";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="./styles/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centurion Low Stock</title>
</head>
<body>
    <div class="main-users">
        <div>
            <h2 class="login-h2">Centurion Low Stock</h2>
        </div>
        <div style="justify-content: center; display: flex;">
            <input class="products-search-bar" type="text" id="searchInput" placeholder="Search Product">
        </div>
        <div style="justify-content: center;display: flex;overflow-y: auto;height: auto;max-height: 49vh;">
            <table class="main-table" id="product-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Barcode</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Loop through each product and display its information in a row
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['description'] . "</td>";
                        echo "<td>" . $row['category'] . "</td>";
                        echo "<td>" . $row['barcode'] . "</td>";
                        echo "<td style='background-color:#ff0000ad; font-weight: 800;'>" . $row['stock'] . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Search functionality
        $(document).ready(function() {
            $("#searchInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#product-table tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
</body>
</html>
