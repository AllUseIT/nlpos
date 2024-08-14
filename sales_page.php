<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        .table-container {
            max-height: 80vh; /* Set the maximum height for scrolling */
            overflow: auto; /* Enable scrolling */
        }
        .sales-popup {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            position: sticky;
            top: 0;
        }
        .refund-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .refund-popup-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .close-btn {
            align-self: flex-end;
            cursor: pointer;
            font-size: 20px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div id="sales-popup" class="sales-popup">
        <div style="margin-bottom: 10px; position: sticky; top: 0px; background-color: white; width: 100%;">
            <h2 style="margin-top: 15px;" class="login-h2">Branch Sales</h2>
            <div style="display: flex; justify-content: center;">
                <input type="text" id="searchSaleInput" placeholder="Search Sale" style="margin-bottom: 10px; width: 300px; height: 30px;">
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Branch</th>
                            <th>Date/Time</th>
                            <th>Total</th>
                            <th>Method</th>
                            <th>Sale Number</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="salesTableBody">
                        <!-- Sales data will be populated here by get_sales.php -->
                        <?php include './API/get_sales.php'; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="main-transaction-popup-2" id="transactions-popup" style="display: none;">
        <div class="transaction-popup-2">
            <label id="user-create-close-btn" class="user-create-close-btn">X</label>
            <label style="text-align: center; justify-content: center; display: flex; padding: 20px; font-size: x-large;">Transaction for SN<span id="sale-number"></span></label>
            <div class="transaction-popup-top">
                <table class="main-table" id="transactions-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Qty</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Barcode</th>
                            <th>Sale Number</th>
                            <th>Refund</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsTableBody">
                        <!-- Transactions data will be populated here -->
                    </tbody>
                </table>
            </div>
            <!-- Add this block to display sale_info data -->
            <div class="transaction-popup-bottom">
                <div class="transaction-info-grid">
                    <div class="transaction-popup-bottom-left">
                        <span class="transaction-popup-bottom-left-span">Payment Method:</span>
                        <span class="transaction-popup-bottom-left-span">Total Price:</span>
                        <span class="transaction-popup-bottom-left-span">Total Amount Given:</span>
                        <span class="transaction-popup-bottom-left-span">Total Change Given:</span>
                    </div>
                    <div class="transaction-popup-bottom-right">
                        <span class="transaction-popup-bottom-right-span">Username:</span>
                        <span class="transaction-popup-bottom-right-span">Branch:</span>
                        <button style="display: block; position: absolute; bottom: 10px; right: 10px;">Print</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Popup/modal for refund quantity -->
    <div id="refund-popup" class="refund-popup" style="display: none;">
        <div class="refund-popup-content">
            <span class="close-btn">&times;</span>
            <h2>Refund Quantity</h2>
            <label for="refund-quantity">Enter quantity to refund:</label>
            <input type="number" id="refund-quantity" name="refund-quantity" min="1">
            <button id="confirm-refund-btn">Confirm Refund</button>
            <input type="hidden" id="refund-product-id" value="">
        </div>
    </div>

    <!-- JavaScript to handle the view sale button click and close button click -->
    <script>
        $(document).ready(function() {
            $("#searchSaleInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#salesTableBody tr").filter(function() {
                    // Check if any of the table cell text content matches the search value
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Handle refund button click (delegate the event to the document)
            $(document).on('click', '.refund-btn', function() {
                // Get the product ID, description, category, barcode, and sale number from the table row
                var productId = $(this).data('product-id');
                var description = $(this).closest('tr').find('.description').text();
                var category = $(this).closest('tr').find('.category').text();
                var barcode = $(this).closest('tr').find('.barcode').text();
                var saleNr = $(this).closest('tr').find('.sale-nr').text();
                var maxRefund = $(this).data('max-refund');

                // Show the refund popup/modal
                $('#refund-popup').show();

                // Set the maximum refund allowed in the refund popup/modal
                $('#refund-quantity').attr('max', maxRefund);

                // Store product information in data attributes of the confirm refund button
                $('#refund-product-id').val(productId);
            });

            // Close the refund popup/modal when clicking the close button
            $('.close-btn').click(function() {
                $('#refund-popup').hide();
            });

            // Handle confirm refund button click
            $('#confirm-refund-btn').click(function() {
                var refundQuantity = $('#refund-quantity').val();
                var productId = $('#refund-product-id').val();
                var description = $(this).data('description');
                var category = $(this).data('category');
                var barcode = $(this).data('barcode');
                var saleNr = $(this).data('sale-nr');

                // Perform refund logic here
                console.log("Product ID for refund: " + productId);
                console.log("Refund Quantity: " + refundQuantity);
                
                // Send AJAX request to insert refund data
                $.ajax({
                    url: './API/insert_refund.php', // Ensure that this URL is correct
                    type: 'POST',
                    data: {
                        product_id: productId,
                        description: description,
                        category: category,
                        barcode: barcode,
                        sale_nr: saleNr,
                        refund_quantity: refundQuantity
                    },
                    success: function(response) {
                        console.log(response); // Log the response from the server
                        $('#refund-popup').hide();
                        alert("Refund processed successfully!");
                        location.reload(); // Refresh the page to reflect changes
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:", status, error);
                        alert("Error processing refund.");
                    }
                });
            });

            // Handle the view sale button click (delegate the event to the document)
            $(document).on('click', '.view-sale-btn', function() {
                // Show the transactions popup/modal
                $('#transactions-popup').show();
                
                var saleNumber = $(this).data('sale-number');
                $('#sale-number').text(saleNumber);

                // Send AJAX request to get transactions data
                $.ajax({
                    url: './API/get_transactions.php',
                    type: 'POST',
                    data: {
                        sale_nr: saleNumber // Ensure that this key matches the PHP script's expectation
                    },
                    success: function(response) {
                        // Populate the transactions table with the response data
                        $('#transactionsTableBody').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:", status, error);
                        alert("Error loading transactions.");
                    }
                });
            });

            // Close the transactions popup when clicking the close button
            $('#user-create-close-btn').click(function() {
                $('#transactions-popup').hide();
            });
        });
    </script>
</body>
</html>
