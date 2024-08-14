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
    </style>
</head>
<body>
    <div id="sales-popup" class="sales-popup">
        <div style="margin-bottom: 10px;position: sticky;top: 0px;background-color: white;">
            <h2 style="margin-top: 15px;" class="login-h2">Refunds</h2>
            <div style="display: flex; justify-content: center;">
                <input type="text" id="searchSaleInput" placeholder="Search Sale" style="margin-bottom: 10px; width: 300px; height: 30px;">
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <td class="table-column">Username</td>
                            <td class="table-column">Branch</td>
                            <td class="table-column">Description</td>
                            <td class="table-column">Category</td>
                            <td class="table-column">Barcode</td>
                            <td class="table-column">Sale Number</td>
                            <td class="table-column">Item Price</td>
                            <td class="table-column">Total Price</td>
                            <td class="table-column">Quantity</td>
                            <td class="table-column">Date/Time</td>
                        </tr>
                    </thead>
                    <tbody id="salesTableBody">
                        <!-- Sales data will be populated here by get_sales.php -->
                        <?php include './API/get_refunds.php'; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="main-transaction-popup-sales" id="transactions-popup" style="display: none;">
        <div class="transaction-popup">
            <label id="user-create-close-btn" class="user-create-close-btn">X</label>
            <label style="text-align: center;justify-content: center;display: flex;padding: 20px;font-size: x-large;">Transaction for SN<span id="sale-number"></span></label>
            <div class="transaction-popup-top">
                <table class="main-table" id="transactions-table">
                    <thead>
                        <tr>
                            <th class="table-column">ID</th>
                            <th class="table-column">Qty</th>
                            <th class="table-column">Description</th>
                            <th class="table-column">Price</th>
                            <th class="table-column">Category</th>
                            <th class="table-column">Barcode</th>
                            <th class="table-column">Sale Number</th>
                            <!--<th class="table-column">Refund</th>-->
                        </tr>
                    </thead>
                    <tbody id="transactionsTableBody">
                        <!-- Transactions data will be populated here -->
                        
                    </tbody>
                </table>
            </div>
            <!-- Add this block to display sale_info data -->
            <div class="transaction-popup-bottom">
                <div class="transaction-popup-bottom-left">
                    <span class="transaction-popup-bottom-left-span">Payment Method:</span>
                    <span class="transaction-popup-bottom-left-span">Total Price:</span>
                    <span class="transaction-popup-bottom-left-span">Total Amount Given:</span>
                    <span class="transaction-popup-bottom-left-span">Total Change Given:</span>
                </div>
                <div class="transaction-popup-bottom-right">
                    <span class="transaction-popup-bottom-right-span">Username:</span>
                    <span class="transaction-popup-bottom-right-span">Branch:</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Popup/modal for refund quantity 
    <div id="refund-popup" class="refund-popup" style="display: none;">
        <div class="refund-popup-content">
            <span class="close-btn">&times;</span>
            <h2>Refund Quantity</h2>
            <label for="refund-quantity">Enter quantity to refund:</label>
            <input type="number" id="refund-quantity" name="refund-quantity" min="1">
            <button id="confirm-refund-btn">Confirm Refund</button>
            <input type="hidden" id="refund-product-id" value="">
        </div>
    </div>-->


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
});

$(document).ready(function() {
    // Delegate the click event handling to the document
    $(document).on('click', '.view-sale-btn', function() {
        var saleNr = $(this).data('sale-nr');
        
        // Fetch and display transactions data for the selected sale
        fetchTransactionsData(saleNr);
        
        // You can also add code to open a modal or navigate to a new page to display the sale details
    });
});

// Function to fetch and display transactions data for the selected sale number
function fetchTransactionsData(saleNr) {
    $.ajax({
        url: './API/get_refund_view.php',
        method: 'POST',
        data: { sale_nr: saleNr },
        success: function(response) {
            // Populate transactions table with the received data
            $('#transactionsTableBody').html(response);
            
            // Display the transactions popup/modal
            $('#transactions-popup').show();
        },
        error: function(xhr, status, error) {
            console.error('Error fetching transactions data:', error);
            // Optionally, display an error message to the user
        }
    });
}

$(document).ready(function() {
    // Click event handler for the close button
    $('#user-create-close-btn').on('click', function() {
        // Hide the transactions popup/modal
        $('#transactions-popup').hide();
    });
});

</script>
</body>
</html>
