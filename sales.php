<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>


        .transaction-info-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .transaction-popup-bottom-left,
        .transaction-popup-bottom-right {
            flex: 1;
        }

        .transaction-popup-bottom-left-span,
        .transaction-popup-bottom-right-span {
            display: block;
            margin: 5px 0;
        }

        #print-area {
            display: none; /* Hidden by default */
        }

        /* Additional styling for better UX */
        tr:hover {
            background-color: #f1f1f1;
        }

        .sales-popup {
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            border-radius: 5px;
        }

        .transaction-popup-2 {
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            padding: 20px;
            width: 90%;
            margin: 20px auto;
            max-height: 80vh;
            overflow-y: auto;
        }

        .refund-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            padding: 20px;
            width: 300px;
            z-index: 1000;
        }

        .refund-popup-content {
            text-align: center;
        }

        .close-btn {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
            font-size: 20px;
            color: #333;
        }

        #searchSaleInput {
            margin-bottom: 20px;
            padding: 5px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* Print specific styles */
        @media print {
            #print-area {
                display: block;
                height: min-content;
            }

            /* Hide everything else */
            body * {
                visibility: hidden;
            }
            #print-area, #print-area * {
                visibility: visible;
            }
            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                overflow: hidden; /* Hide overflow to fit content within one page */
                page-break-inside: avoid; /* Avoid page breaks inside this element */
            }
            /* Optional: Control page breaks within #print-area */
            #print-area table {
                page-break-inside: auto;
            }

            #print-area thead {
                display: table-header-group;
            }

            #print-area tbody {
                display: table-row-group;
            }

            #print-area tfoot {
                display: table-footer-group;
            }
        }
    </style>
</head>
<body>
    <div id="sales-popup" class="sales-popup">
        <div style="margin-bottom: 10px; position: sticky; top: 0px; background-color: white; width: 100%;">
            <h2 style="margin-top: 15px;" class="login-h2">Sales</h2>
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
                        <?php include './API/get_admin_sales.php'; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="main-transaction-popup-2" id="transactions-popup" style="display: none;">
        <div class="transaction-popup-2">
            <label id="user-create-close-btn" class="user-create-close-btn">X</label>
            <label style="text-align: center; justify-content: center; display: flex; padding: 20px; font-size: x-large;">
                Transaction for SN<span id="sale-number"></span>
            </label>
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
                        </tr>
                    </thead>
                    <tbody id="transactionsTableBody">
                        <!-- Transactions data will be populated here -->
                    </tbody>
                </table>
            </div>
            <!-- Block to display sale_info data -->
            <div class="transaction-popup-bottom">
                <div class="transaction-info-grid">
                    <div class="transaction-popup-bottom-left">
                        <span id="payment-method" class="transaction-popup-bottom-left-span">Payment Method: </span>
                        <span id="total-price" class="transaction-popup-bottom-left-span">Total Price: </span>
                        <span id="amount-given" class="transaction-popup-bottom-left-span">Total Amount Given: </span>
                        <span id="change-given" class="transaction-popup-bottom-left-span">Total Change Given: </span>
                    </div>
                    <div class="transaction-popup-bottom-right">
                        <span id="username" class="transaction-popup-bottom-right-span">Username: </span>
                        <span id="branch" class="transaction-popup-bottom-right-span">Branch: </span>
                        <button id="print-button" style="display: block; position: absolute; bottom: 10px; right: 10px;">Print</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="print-area"></div> <!-- Area for print content -->

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

    <script>
        $(document).ready(function() {
            // Search functionality
            $('#searchSaleInput').on('input', function() {
                var value = $(this).val().toLowerCase();
                $('#salesTableBody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // View transactions button click event
            $(document).on('click', '.view-transactions', function() {
                var saleNumber = $(this).data('sale-number');
                $('#sale-number').text(saleNumber);

                // Fetch transaction details
                $.ajax({
                    url: './API/get_admin_transactions.php',
                    type: 'GET',
                    data: { sale_number: saleNumber },
                    success: function(response) {
                        console.log('Transactions Response:', response); // Debugging
                        $('#transactionsTableBody').html(response);
                        $('#transactions-popup').show();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching transactions:', error);
                    }
                });

                // Fetch sale info details
                $.ajax({
                    url: './API/get_sale_info.php',
                    type: 'GET',
                    dataType: 'json',
                    data: { sale_number: saleNumber },
                    success: function(data) {
                        if (data) {
                            $('#payment-method').text('Payment Method: ' + data.info_pay_method);
                            $('#total-price').text('Total Price: $' + parseFloat(data.info_total).toFixed(2));
                            $('#amount-given').text('Total Amount Given: $' + parseFloat(data.amount_given).toFixed(2));
                            $('#change-given').text('Total Change Given: $' + parseFloat(data.change_given).toFixed(2));
                            $('#username').text('Username: ' + data.info_username);
                            $('#branch').text('Branch: ' + data.info_branch);
                        } else {
                            console.error('No sale info found for the given sale number.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching sale info:', error);
                    }
                });
            });

            // Close transaction popup
            $('#user-create-close-btn').click(function() {
                $('#transactions-popup').hide();
            });

            // Confirm refund button click event
            $('#confirm-refund-btn').click(function() {
                var refundQuantity = $('#refund-quantity').val();
                var productId = $('#refund-product-id').val();

                $.ajax({
                    url: './API/refund_transaction.php',
                    type: 'POST',
                    data: {
                        product_id: productId,
                        refund_quantity: refundQuantity
                    },
                    success: function(response) {
                        alert(response);
                        $('#refund-popup').hide();
                        location.reload(); // Reload the page to update the sales data
                    }
                });
            });

            // Open refund popup
            $(document).on('click', '.refund-btn', function() {
                var productId = $(this).data('product-id');
                $('#refund-product-id').val(productId);
                $('#refund-popup').show();
            });

            // Close refund popup
            $('.close-btn').click(function() {
                $('#refund-popup').hide();
            });

            $(document).ready(function() {
                $('#print-button').click(function() {
                    var saleNumber = $('#sale-number').text();
                    $.ajax({
                        url: './API/after_receipt.php',
                        type: 'GET',
                        data: { sale_nr: saleNumber },
                        success: function(response) {
                            $('#print-area').html(response);

                            // Optional: Adjust content to fit one page
                            var printArea = document.getElementById('print-area');
                            printArea.style.Height = '50vh'; // Limit height to viewport height

                            setTimeout(function() {
                                window.print();
                                $('#print-area').empty(); // Clear content after printing
                            }, 500); // Adjust delay as needed
                        },
                        error: function() {
                            alert('Error fetching receipt.');
                        }
                    });
                });
            });


        });
    </script>
</body>
</html>
