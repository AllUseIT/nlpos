<?php
include "db.php";

$query = "SELECT * FROM `refunds` ORDER BY refund_date desc";
$result = $conn->query($query);

$sales_table_rows = '';

while($row = $result->fetch_assoc()) {
    $sales_table_rows .= '<tr>';
    $sales_table_rows .= '<td class="table-column">' . $row['refund_username'] . '</td>';
    $sales_table_rows .= '<td class="table-column">' . $row['refund_branch'] . '</td>';
    $sales_table_rows .= '<td class="table-column">' . $row['refund_description'] . '</td>';
    $sales_table_rows .= '<td class="table-column">' . $row['refund_category'] . '</td>';
    $sales_table_rows .= '<td class="table-column">' . $row['refund_barcode'] . '</td>';
    $sales_table_rows .= '<td class="table-column">' . $row['refund_sale_nr'] . '</td>';
    $sales_table_rows .= '<td class="table-column">' . $row['refund_price'] . '</td>';
    $sales_table_rows .= '<td class="table-column">' . $row['refund_total'] . '</td>';
    $sales_table_rows .= '<td class="table-column">' . $row['refund_quantity'] . '</td>';
    $sales_table_rows .= '<td class="table-column">' . $row['refund_date'] . '</td>';
    $sales_table_rows .= '<td class="table-column"><button class="view-sale-btn" data-sale-id="' . $row['id'] . '" data-sale-nr="' . $row['refund_sale_nr'] . '">View</button></td>';
    $sales_table_rows .= '</tr>';
}

echo $sales_table_rows;
?>

<!-- JavaScript to handle the view sale button click -->
<script>
$(document).ready(function(){
    // Delegate the click event handling to the document
    $(document).on('click', '.view-sale-btn', function(){
        var saleId = $(this).data('sale-id');
        var saleNr = $(this).data('sale-nr');
        
        // Fetch and display transactions data for the selected sale
        fetchTransactionsData(saleNr);
        
        // You can also add code to open a modal or navigate to a new page to display the sale details
    });
});
</script>
