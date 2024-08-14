<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="./styles/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
</head>
<body>
    <?php 
    include "./API/db.php"; 

    // Fetch all products from the database
    $query = "SELECT * FROM montana";
    $result = mysqli_query($conn, $query);
    ?>

    <div class="main-users" style="height: 81vh;">
        <div>
            <h2 class="login-h2">Montana Stock</h2>
        </div>
        <div style="justify-content: center; display: flex;">
            <input class="products-search-bar" type="text" id="searchInput" placeholder="Search Product">
        </div>
        <div style="justify-content: center; display: flex;justify-content: center;display: flex;overflow-y: auto;height: 68vh;margin-top: 20px;">
            <table class="main-table" id="product-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Main Price</th>
                        <th>Dis Price</th>
                        <th>P3 Price</th>
                        <th>Barcode</th>
                        <th>Stock</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Loop through each product and display its information in a row
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['description'] . "</td>";
                        echo "<td>" . $row['category'] . "</td>";
                        echo "<td>" . 'R' . $row['main_price'] . "</td>";
                        echo "<td>" . 'R' . $row['dis_price'] . "</td>";
                        echo "<td>" . 'R' . $row['p3_price'] . "</td>";
                        echo "<td>" . $row['barcode'] . "</td>";
                        echo "<td>" . $row['stock'] . "</td>";
                        echo "<td><button class='edit-button' data-id='" . $row['id'] . "'>Edit</button></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="main-stock-edit-popup" class="main-stock-edit-popup">
        <div style="display: flex;justify-content: center;align-items: center;height: -webkit-fill-available;">
            <div class="stock-edit-popup">
                <span class="stock-edit-popup-close">&times;</span>
                <div class="main-stock-edit-label">
                    <h2>Insert Stock</h2>
                </div>
                <div class="popup-section">
                    <label class="stock-edit-popup-label">Add Stock</label>
                    <input id="add-stock-input" class="stock-edit-popup-input" type="number" placeholder="Enter stock amount">
                    <button id="add-stock-button" class="stock-edit-popup-button">Add</button>
                    <label class="stock-edit-popup-note">*NOTE* This will add stock to the existing stock.</label>
                </div>
                
                <div class="popup-section">
                    <label class="stock-edit-popup-label">Total Stock</label>
                    <input id="total-stock-input" class="stock-edit-popup-input" type="number" placeholder="Enter total stock amount">
                    <button id="update-total-stock-button" class="stock-edit-popup-button">Update</button>
                    <label class="stock-edit-popup-note">*NOTE* This will replace existing stock.</label>
                </div>
            </div>
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

        // Function to show the stock popup and populate description and barcode
        function showStockPopup(id, description, barcode) {
            $("#main-stock-edit-popup").show();
            $("#edit-description-barcode").text("You are currently editing " + description + "/" + barcode);
            $("#add-stock-button").attr("data-id", id); // Add data-id attribute to the add stock button
            $("#update-total-stock-button").attr("data-id", id); // Add data-id attribute to the update total stock button
        }

        // Edit button click event
        $(".edit-button").on("click", function() {
            var id = $(this).data("id");
            var description = $(this).closest("tr").find(".table-column.description").text();
            var barcode = $(this).closest("tr").find(".table-column.barcode").text();
            showStockPopup(id, description, barcode);
        });

        // Function to handle adding stock
        $("#add-stock-button").on("click", function(event) {
            event.preventDefault(); // Prevent default form submission
            var id = $(this).attr("data-id");
            var addStock = $("#add-stock-input").val();
            $.ajax({
                type: "POST",
                url: "./API/update_montana_stock.php",
                data: { id: id, addStock: addStock },
                success: function(response) {
                    // Reload montana.php after adding stock
                    $("#main-branch-display").load("montana.php");
                    console.log("Stock added successfully");
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error("Error adding stock: " + error);
                }
            });
        });

        // Function to handle updating total stock
        $("#update-total-stock-button").on("click", function(event) {
            event.preventDefault(); // Prevent default form submission
            var id = $(this).attr("data-id");
            var totalStock = $("#total-stock-input").val();
            $.ajax({
                type: "POST",
                url: "./API/update_montana_stock.php",
                data: { id: id, totalStock: totalStock },
                success: function(response) {
                    // Reload montana.php after updating total stock
                    $("#main-branch-display").load("montana.php");
                    console.log("Total stock updated successfully");
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error("Error updating total stock: " + error);
                }
            });
        });

        // Close stock popup
        $(".stock-edit-popup-close").on("click", function() {
            $("#main-stock-edit-popup").hide();
        });
    </script>
</body>
</html>
