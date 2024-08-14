<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="./styles/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .loading-screen {
            position: fixed;
            top: 51%;
            left: 56%;
            /* width: 100%; */
            /* height: 100%; */
            background-color: rgba(255, 255, 255, 0.4);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <script>
        $(document).ready(function() {
    function showLoadingScreen() {
        $(".loading-screen").show();
    }

    function hideLoadingScreen() {
        $(".loading-screen").hide();
    }

    function loadProducts() {
        showLoadingScreen();
        $.ajax({
            url: "./API/get_products.php",
            type: "GET",
            success: function(response) {
                hideLoadingScreen();
                let products = JSON.parse(response);
                let tbody = $("#product-table tbody");
                tbody.empty();

                products.forEach(function(product) {
                    let row = `
                        <tr data-barcode2="${product.barcode_2}">
                            <td class="description">${product.description}</td>
                            <td class="category">${product.category}</td>
                            <td class="main_price">${product.main_price}</td>
                            <td class="dis_price">${product.dis_price}</td>
                            <td class="p3_price">${product.p3_price}</td>
                            <td class="barcode">${product.barcode}</td>
                            <td class="barcode_2" style="display: none;">${product.barcode_2}</td>
                            <td>
                                <button class="edit-button">Edit</button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });

                // Call the function to update the total products count
                updateProductCount();
            },
            error: function(error) {
                hideLoadingScreen();
                alert("Error: " + error);
            }
        });
    }

    function updateProductCount() {
        $.ajax({
            url: "./API/count_products.php",
            type: "GET",
            success: function(response) {
                let data = JSON.parse(response);
                $("#total-products-label").text("Total Products: " + data.total);
            },
            error: function(error) {
                console.error("Error fetching product count: " + error);
            }
        });
    }

    loadProducts();

    $("#createProductBtn").click(function() {
        $("#createProductPopup").show();
    });

    $("#product-create-close-btn").click(function() {
        $("#createProductPopup").hide();
    });

    $("#product-edit-close-btn").click(function() {
        $("#editProductPopup").hide();
    });

    $("#create-product-button").click(function(e) {
        e.preventDefault();

        let description = $("input[name='description']").val();
        let category = $("input[name='category']").val();
        let mainPrice = $("input[name='main_price']").val();
        let disPrice = $("input[name='dis_price']").val();
        let p3Price = $("input[name='p3_price']").val();
        let barcode = $("input[name='barcode']").val();
        let barcode2 = $("input[name='barcode_2']").val();

        $.ajax({
            url: "./API/insert_product.php",
            type: "POST",
            data: {
                description: description,
                category: category,
                main_price: mainPrice,
                dis_price: disPrice,
                p3_price: p3Price,
                barcode: barcode,
                barcode_2: barcode2
            },
            success: function(response) {
                alert(response);
                $("#createProductPopup").hide();
                loadProducts();
            },
            error: function(error) {
                alert("Error: " + error);
            }
        });
    });

    $(document).on("click", ".edit-button", function() {
        let row = $(this).closest("tr");
        let barcode2 = row.data("barcode2");
        let description = row.find(".description").text();
        let category = row.find(".category").text();
        let mainPrice = row.find(".main_price").text();
        let disPrice = row.find(".dis_price").text();
        let p3Price = row.find(".p3_price").text();
        let barcode = row.find(".barcode").text();

        $("#editProductPopup").show();
        $("#editProductForm input[name='barcode_2']").val(barcode2);
        $("#editProductForm input[name='description']").val(description);
        $("#editProductForm input[name='category']").val(category);
        $("#editProductForm input[name='main_price']").val(mainPrice);
        $("#editProductForm input[name='dis_price']").val(disPrice);
        $("#editProductForm input[name='p3_price']").val(p3Price);
        $("#editProductForm input[name='barcode']").val(barcode);
    });

    $("#editProductForm").submit(function(e) {
        e.preventDefault();

        let id = $("#editProductForm input[name='id']").val();
        let barcode2 = $("#editProductForm input[name='barcode_2']").val();
        let newDescription = $("#editProductForm input[name='description']").val();
        let newCategory = $("#editProductForm input[name='category']").val();
        let newMainPrice = $("#editProductForm input[name='main_price']").val();
        let newDisPrice = $("#editProductForm input[name='dis_price']").val();
        let newP3Price = $("#editProductForm input[name='p3_price']").val();
        let newBarcode = $("#editProductForm input[name='barcode']").val();

        $.ajax({
            url: "./API/update_products.php",
            type: "POST",
            data: {
                barcode_2: barcode2,
                description: newDescription,
                category: newCategory,
                main_price: newMainPrice,
                dis_price: newDisPrice,
                p3_price: newP3Price,
                barcode: newBarcode
            },
            success: function(response) {
                console.log("Response:", response); // Log the response
                try {
                    let parsedResponse = JSON.parse(response);
                    alert(parsedResponse.message);
                    if (parsedResponse.status === 'success') {
                        $("#editProductPopup").hide();
                        loadProducts();
                    }
                } catch (e) {
                    console.error("Failed to parse JSON response:", e);
                    alert("An error occurred while processing the response.");
                }
            },
            error: function(error) {
                console.error("AJAX error:", error);
                alert("Error: " + error);
            }
        });
    });

    $("#delete-product-button").click(function() {
        if (confirm("Are you sure you want to delete this product?")) {
            let barcode2 = $("#editProductForm input[name='barcode_2']").val();

            $.ajax({
                url: "./API/delete_product.php",
                type: "POST",
                data: { barcode_2: barcode2 },
                success: function(response) {
                    alert(response);
                    $("#editProductPopup").hide();
                    loadProducts();
                },
                error: function(error) {
                    alert("Error: " + error);
                }
            });
        }
    });

    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#product-table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});

    </script>
</head>
<body>
    <div class="loading-screen">
        <div class="loading-spinner"></div>
    </div>

    <div class="main-users-panel">
        <div>
            <h2 class="login-h2">Products</h2>
        </div>
        <div class="main-create-user-button">
            <button style="margin: 10px;" id="createProductBtn">Create New Product</button>
            <input style="margin: 10px;" class="products-search-bar" type="text" id="searchInput" placeholder="Search Product">
            <label style="margin: 10px;margin: 10px;display: flex;right: 10px;position: absolute;" id="total-products-label">Total Products: 123</label>
        </div>
        <div class="main-product-top">
            <div>
                <div class="table-container">
                    <table class="main-table" id="product-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Main Price</th>
                                <th>Discount Price</th>
                                <th>P3 Price</th>
                                <th>Barcode</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamic content will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <div class="main-create-user-popup" id="createProductPopup" style="display:none;">
        <div class="chiled-create-user-popup">
            <div>
                <h2 style="text-align:center;">Create New Product</h2>
                <label id="product-create-close-btn" class="user-create-close-btn">X</label>
            </div>
            <div class="main-user-create-form">
                <form id="createProductForm">
                    <div class="main-user-create-info">
                        <label class="user-create-info-label">Description</label>
                        <input class="user-create-info-button" type="text" name="description">
                    </div>
                    <div class="main-user-create-info">
                        <label class="user-create-info-label">Category</label>
                        <input class="user-create-info-button" type="text" name="category">
                    </div>
                    <div class="main-user-create-info">
                        <label class="user-create-info-label">Main Price</label>
                        <input class="user-create-info-button" type="text" name="main_price">
                    </div>
                    <div class="main-user-create-info">
                        <label class="user-create-info-label">Discount Price</label>
                        <input class="user-create-info-button" type="text" name="dis_price">
                    </div>
                    <div class="main-user-create-info">
                        <label class="user-create-info-label">P3 Price</label>
                        <input class="user-create-info-button" type="text" name="p3_price">
                    </div>
                    <div class="main-user-create-info">
                        <label class="user-create-info-label">Barcode</label>
                        <input class="user-create-info-button" type="text" name="barcode">
                    </div>
                    <div style="display: none;" class="main-user-create-info">
                        <label class="user-create-info-label">Barcode 2</label>
                        <input class="user-create-info-button" type="text" name="barcode_2">
                    </div>
                    <div class="main-update-delete">
                        <button class="create-button" id="create-product-button">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="main-edit-user-popup" id="editProductPopup" style="display:none;">
        <div class="chiled-edit-user-popup">
            <div>
                <h2 style="text-align:center;">Edit Product</h2>
                <label id="product-edit-close-btn" class="user-edit-close-btn">X</label>
            </div>
            <div class="main-user-create-form">
                <form id="editProductForm">
                    <div style="display: none;" class="main-user-edit-info">
                        <label class="user-edit-info-label">Barcode 2</label>
                        <input class="user-edit-info-button" type="text" name="barcode_2" readonly>
                    </div>
                    <div class="main-user-edit-info">
                        <label class="user-edit-info-label">Description</label>
                        <input class="user-edit-info-button" type="text" name="description">
                    </div>
                    <div class="main-user-edit-info">
                        <label class="user-edit-info-label">Category</label>
                        <input class="user-edit-info-button" type="text" name="category">
                    </div>
                    <div class="main-user-edit-info">
                        <label class="user-edit-info-label">Main Price</label>
                        <input class="user-edit-info-button" type="text" name="main_price">
                    </div>
                    <div class="main-user-edit-info">
                        <label class="user-edit-info-label">Discount Price</label>
                        <input class="user-edit-info-button" type="text" name="dis_price">
                    </div>
                    <div class="main-user-edit-info">
                        <label class="user-edit-info-label">P3 Price</label>
                        <input class="user-edit-info-button" type="text" name="p3_price">
                    </div>
                    <div class="main-user-edit-info">
                        <label class="user-edit-info-label">Barcode</label>
                        <input class="user-edit-info-button" type="text" name="barcode">
                    </div>
                    <div style="display: none;" class="main-user-edit-info">
                        <label class="user-edit-info-label">Barcode 2</label>
                        <input class="user-edit-info-button" type="text" name="barcode_2">
                    </div>
                    <div class="main-update-delete">
                        <button class="create-button" id="edit-product-button">Update</button>
                        <button class="delete-button" id="delete-product-button">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
