<?php 
include "./API/db.php"; 
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$branch = $_SESSION['branch'];

// Dynamically select the table based on the branch
$tableName = strtolower($branch); // Assuming you have tables like products_branchname
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="./styles/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Page</title>
</head>
<body>
<div class="main-pos-page">
        <div class="main-lr">
            <div class="main-left">
                <div class="main-barcode">
                    <input id="barcodeSearch" class="barcode-search-bar" placeholder="Search Barcode">
                </div>
                <!-- Table to display search results -->
                <table class="main-table">
                    <thead class="table-head">
                        <tr>
                            <th class="table-column">Qty</th>
                            <th class="table-column">Description</th>
                            <th class="table-column">Category</th>
                            <th class="table-column">Unit Price</th>
                            <th class="table-column">Barcode</th>
                            <th class="table-column">Total Price</th>
                            <th class="table-column">Cancel</th>
                        </tr>
                    </thead>
                    <tbody id="searchResultsBody"></tbody>
                </table>
            </div>
            <div class="main-right">
                <div class="main-h2">
                    <h2>Last Transaction</h2>
                </div>
                <div class="receipt-info">
                    <?php include "receipt_info.php"; ?>
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="main-footer-left">
                <label>Sub Total: R<span id="subtotal">0.00</span></label>
                <label>Tax: R<span id="tax">0.00</span></label>
                <label>Total: R<span id="total">0.00</span></label>
                <input type="hidden" id="subtotalInput">
                <input type="hidden" id="taxInput">
                <input type="hidden" id="totalInput">
            </div>
            <div class="main-footer-right">
                <div class="footer-buttons">
                    <button style="background-color: #28a745;" class="footer-btn" id="processButton">Process</button>
                    <button style="background-color: orangered;" class="footer-btn" id="abortButton">Abort</button>
                    <span id="receipt" class="footer-btn">Receipt</span>
                    <!--<button class="footer-btn">Till</button>-->
                </div>
            </div>
        </div>
    </div>

    <div id="checkout-popup" class="checkout-popup">
        <div class="checkout-popup-content">
            <div id="closeButton" class="checkout-close-btn">X</div>
            <form id="checkoutForm">
                <h1 class="checkout-title">Checkout</h1>
                <div class="pos-checkout-value">
                    <label>Sub</label>
                    <label id="popup-subtotal">0.00</label>
                </div>
                <div class="pos-checkout-value">
                    <label>Tax (15%)</label>
                    <label id="popup-tax">0.00</label>
                </div>
                <div class="pos-checkout-value total">
                    <label>Total</label>
                    <label id="popup-total">0.00</label>
                </div>
                <div class="pos-checkout-value">
                    <select id="paymentMethodSelect" class="checkout-paymethod-select" name="pay_method">
                        <option value="Card">Card</option>
                        <option value="Cash">Cash</option>
                        <option value="EFT">EFT</option>
                        <option value="Cash/Card">Cash/Card</option>
                    </select>
                </div>
                <!-- Additional fields for cash payment -->
                <div id="cash-fields" class="cash-fields">
                    <div class="pos-checkout-value">
                        <label>Total Cash Given</label>
                        <input type="number" id="cash-given" step="0.01" class="cash-input">
                    </div>
                    <div class="pos-checkout-value">
                        <label>Change Amount</label>
                        <label id="change-amount">0.00</label>
                    </div>
                </div>
                <!-- Additional fields for cash/card payment -->
                <div id="cash-card-fields" class="cash-card-fields">
                    <div class="pos-checkout-value">
                        <label>Cash Given</label>
                        <input type="number" id="cash-card-given" step="0.01" class="cash-card-input">
                    </div>
                    <div class="pos-checkout-value">
                        <label>Card Amount</label>
                        <input disabled type="number" id="card-given" step="0.01" class="card-input">
                    </div>
                </div>
                <button type="submit" class="checkout-pay-button">Pay</button>
            </form>
        </div>
    </div>

</body>
</html>

    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>

    $(document).ready(function(){
        // Receipt button click handler
        $("#receipt").click(function(){
            // Create a hidden iframe
            var iframe = $("<iframe>").hide().appendTo("body").get(0);
            var doc = iframe.contentWindow.document;

            // Load print_latest_receipt.php into the iframe
            doc.open();
            doc.write('<!DOCTYPE html><html><head><title>Receipt</title></head><body></body></html>');
            doc.close();
            $(doc.body).load("print_latest_receipt.php", function() {
                // Print the content of the iframe
                iframe.contentWindow.print();
            });
        });
    });


    $('#abortButton').on('click', function() {
            if (confirm('Are you sure you want to abort? All inserts will be lost.')) {
                location.reload();
            }
        });

    $('.footer-btn-left-abort').on('click', function(){
        var confirmAbort = confirm('Are you sure you want to abort? All inserts will be lost.');

        if(confirmAbort) {
            location.reload();
        }
    });

$(document).ready(function(){
    handlePaymentMethod();

$('form').on('submit', function(event) {
    event.preventDefault();

    var payMethod = $('#paymentMethodSelect').val();
    var subtotal = $('#popup-subtotal').text();
    var total = $('#popup-total').text();
    var amountGiven = parseFloat($('#cash-given').val());
    var changeGiven = parseFloat($('#change-amount').text());
    var cashCardGiven = parseFloat($('#cash-card-given').val());
    var saleNr = generateUniqueSaleNumber();

    // Data for sale_info table
    var saleInfoData = {
        info_username: "<?php echo $username; ?>",
        info_branch: "<?php echo $branch; ?>",
        info_total: total,
        info_date_time: getCurrentDateTime(),
        info_pay_method: payMethod,
        sale_nr: saleNr
    };

    var transactionsData = [];

    $('#searchResultsBody tr').each(function(){
        var qty = $(this).find('.qty-input').val();
        var description = $(this).find('.table-column:nth-child(2)').text();
        var price = $(this).find('.unit-price').data('main-price');
        var category = $(this).find('.table-column:nth-child(3)').text();
        var barcode = $(this).find('.table-column:nth-child(5)').text();

        var transaction = {
            qty: qty,
            description: description,
            price: price,
            category: category,
            barcode: barcode,
            sale_nr: saleNr
        };

        transactionsData.push(transaction);
    });

    $.ajax({
        url: 'insert_data.php',
        type: 'POST',
        data: {
            saleInfoData: saleInfoData,
            transactionsData: transactionsData,
            amountGiven: amountGiven,
            cashCardGiven: cashCardGiven,
            changeGiven: changeGiven,
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                alert('Payment successful!');
                location.reload();
            } else {
                alert('Payment failed. Please try again.');
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX error:", xhr.responseText);
            alert('An error occurred while processing your payment.');
        }
    });
});

    function generateSaleNumber() {
        return Math.floor(Math.random() * (99999 - 10000 + 1)) + 10000;
    }

    function generateUniqueSaleNumber() {
        var saleNr;
        do {
            saleNr = generateSaleNumber();
        } while (checkSaleNumberExists(saleNr));

        return saleNr;
    }

    function checkSaleNumberExists(saleNr) {
        var exists = false;
        
        $.ajax({
            url: 'check_sale_number.php',
            type: 'POST',
            async: false,
            data: { saleNr: saleNr },
            dataType: 'json',
            success: function(response) {
                exists = response.exists;
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", xhr.responseText);
                alert('An error occurred while checking sale number.');
            }
        });

        return exists;
    }

    function getCurrentDateTime() {
        var now = new Date();
        return now.toISOString().slice(0, 19).replace('T', ' '); // Format: YYYY-MM-DD HH:MM:SS
    }
    var cashCardGiven = parseFloat($('#cash-card-given').val());
    
    $('#paymentMethodSelect').on('change', handlePaymentMethod);

        function handlePaymentMethod() {
            var selectedMethod = $('#paymentMethodSelect').val();
            $('#cash-fields').toggle(selectedMethod === 'Cash');
            $('#cash-card-fields').toggle(selectedMethod === 'Cash/Card');
        }

        $('#cash-given').on('input', function() {
            var cashGiven = parseFloat($(this).val());
            var total = parseFloat($('#popup-total').text());
            var changeAmount = cashGiven - total;
            $('#change-amount').text(changeAmount.toFixed(2));
        });

        $('#cash-card-given').on('input', function() {
            var cashCardGiven = parseFloat($(this).val());
            var total = parseFloat($('#popup-total').text());
            var cardAmount = total - cashCardGiven;
            $('#card-given').val(cardAmount.toFixed(2));
        });
    });

    $('#processButton').on('click', function(){
        let subtotal = 0;
        const taxRate = 0;

        $('.total-price').each(function() {
            subtotal += parseFloat($(this).text());
        });

        const tax = subtotal * taxRate;
        const total = subtotal + tax;

        $('#popup-subtotal').text(subtotal.toFixed(2));
        $('#popup-tax').text(tax.toFixed(2));
        $('#popup-total').text(total.toFixed(2));

        $('#checkout-popup').show();
    });

    $('#checkout-paymethod-select').on('change', function(){
        handlePaymentMethod();
    });

    // Handle payment method change
    function handlePaymentMethod() {
        var payMethod = $('#checkout-paymethod-select').val();
        
        if (payMethod === 'Cash') {
            $('#cash-fields').show();
        } else {
            $('#cash-fields').hide();
        }
    }

    $('#cash-given').on('input', function(){
        const cashGiven = parseFloat($(this).val());
        const totalAmount = parseFloat($('#popup-total').text());
        
        if(cashGiven >= totalAmount) {
            const changeAmount = cashGiven - totalAmount;
            $('#change-amount').text(changeAmount.toFixed(2));
        } else {
            $('#change-amount').text('0.00');
        }
    });
    
     // Function to handle payment method selection
     function handlePaymentMethod() {
        var selectedMethod = $('#paymentMethodSelect').val();
        $('#cash-fields').hide();
        $('#cash-card-fields').hide();

        if(selectedMethod === 'Cash') {
            $('#cash-fields').show();
        } else if(selectedMethod === 'Cash/Card') {
            $('#cash-card-fields').show();
        }
    }

    $('#closeButton').on('click', function() {
        $('#checkout-popup').hide();
    });

    $('#processButton').on('click', function(){
        let selectedItems = [];

        $('#searchResultsBody tr').each(function(){
            let item = {
                'price': $(this).find('.unit-price').data('main-price')
            };
            selectedItems.push(item);
        });
    });

    // Function to display checkout info
    function displayCheckoutInfo(checkout) {
        $('#checkout-popup').show();
        $('#popup-subtotal').text(checkout.subtotal);
        $('#popup-tax').text(checkout.tax);
        $('#popup-total').text(checkout.total);
    }

    // Function to display checkout info
    function displayCheckoutInfo(checkout) {
        $('#checkout-popup').show();
        $('#popup-subtotal').text(checkout.subtotal.toFixed(2));
        $('#popup-total').text(checkout.total.toFixed(2));
    }

    // Function to display checkout info
    function displayCheckoutInfo(checkout) {
        $('#checkout-popup').show();
        
        // Convert to numbers before using toFixed
        const subtotal = parseFloat(checkout.subtotal);
        const tax = parseFloat(checkout.tax);
        const total = parseFloat(checkout.total);
        
        // Check if conversion is successful
        if (!isNaN(subtotal) && !isNaN(tax) && !isNaN(total)) {
            $('#popup-subtotal').text(subtotal.toFixed(2));
            $('#popup-tax').text(tax.toFixed(2));
            $('#popup-total').text(total.toFixed(2));
        } else {
            console.error("Error: Invalid checkout data received");
            alert("Error: Invalid checkout data received");
        }
    }

    var timer;  // Declare timer variable here
        
    $('#barcodeSearch').on('input', function(){
        var barcode = $(this).val();
        
        clearTimeout(timer);
        timer = setTimeout(function() {
            $.ajax({
                url: 'p3_search_barcode.php',
                type: 'POST',
                data: {barcode: barcode},
                dataType: 'json',
                success: function(response){
                    if(response.error) {
                        alert(response.error);
                    } else {
                        updateOrAddRow(barcode, response);
                    }
                    $('#barcodeSearch').val('');
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", xhr.responseText);
                }
            });
        }, 500);
    });

    // Remove row when Cancel button is clicked
    $(document).on('click', '.cancel-btn', function() {
        $(this).closest('tr').remove();
        calculateTotal();
    });

    // Update quantity or add new row based on barcode
    function updateOrAddRow(barcode, response) {
        var existingRow = $('#searchResultsBody').find('tr[data-barcode="' + barcode + '"]');
        if (existingRow.length > 0) {
            var qtyInput = existingRow.find('.qty-input');
            qtyInput.val(parseInt(qtyInput.val()) + 1);
            updateTotalForExistingRow(existingRow);
        } else {
            addNewRow(barcode, response);
        }
    }

    // Update total for existing row
    function updateTotalForExistingRow(row) {
        var qty = parseInt(row.find('.qty-input').val());
        var p3Price = parseFloat(row.data('p3-price')); // Retrieve p3_price from data attribute
        var totalPrice = qty * p3Price;
        row.find('.total-price').text(totalPrice.toFixed(2));
        row.find('.unit-price').text(p3Price.toFixed(2)); // Display p3_price as unit price
        calculateTotal();
    }

    // Add new row
    function addNewRow(barcode, response) {
        var newRow = '<tr data-barcode="' + barcode + '" data-p3-price="' + response.p3_price + '">';
        newRow += '<td style="width: 60px;" class="table-column"><input style="border: none; width: 82%; height: 87%;" type="number" name="qty" class="qty-input" value="1" min="1"></td>';
        newRow += '<td class="table-column">' + response.description + '</td>';
        newRow += '<td class="table-column">' + response.category + '</td>';
        newRow += '<td class="unit-price table-column" data-main-price="' + response.p3_price + '">' + response.p3_price + '</td>'; // Display p3_price as unit price
        var totalPrice = parseFloat(response.p3_price);
        newRow += '<td class="table-column">' + barcode + '</td>';
        newRow += '<td class="total-price price-cell table-column">' + totalPrice.toFixed(2) + '</td>';
        newRow += '<td class="table-column"><button class="cancel-btn">Cancel</button></td>';
        newRow += '</tr>';
        $('#searchResultsBody').append(newRow);
        calculateTotal();
    }

    // Update total price dynamically when quantity changes
    $(document).on('input', '.qty-input', function() {
        updateTotalForExistingRow($(this).closest('tr'));
    });

    // Calculate total function
    function calculateTotal() {
        var subtotal = 0;
        $('.total-price').each(function() {
            subtotal += parseFloat($(this).text());
        });

        $('#subtotal').text(subtotal.toFixed(2));
        $('#subtotalInput').val(subtotal.toFixed(2));

        var tax = subtotal * 0;  // Assuming 15% tax rate
        var total = subtotal + tax;

        $('#tax').text(tax.toFixed(2));
        $('#taxInput').val(tax.toFixed(2));
        $('#total').text(total.toFixed(2));
        $('#totalInput').val(total.toFixed(2));
    }
    </script>
</body>
</html>