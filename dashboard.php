<?php
session_start();

require_once "./API/db.php";

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$branch = htmlspecialchars($_SESSION['branch']);

// Fetch user job title from the database
$query = "SELECT job FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if ($result) {
    $user = mysqli_fetch_assoc($result);
    if (!$user || $user['job'] !== 'Admin') {
        // Close the database connection
        mysqli_close($conn);
        header("Location: index.php");
        exit();
    }
} else {
    // Close the database connection
    mysqli_close($conn);
    header("Location: index.php");
    exit();
}

// Initialize variables
$totalSales = 0.00;
$totalAmountGiven = 0.00;
$totalChangeGiven = 0.00;
$cardPayments = 0.00; // Initialize $cardPayments
$eftPayments = 0.00;  // Initialize $eftPayments
$cashPayments = 0.00; // Initialize $cashPayments

// Fetch data from sale_info table
$sql = "SELECT SUM(info_total) AS total_sales, 
SUM(amount_given) AS total_amount_given, 
SUM(change_given) AS total_change_given,
SUM(CASE WHEN info_pay_method = 'Card' THEN info_total ELSE 0 END) AS card_payments,
SUM(CASE WHEN info_pay_method = 'EFT' THEN info_total ELSE 0 END) AS eft_payments,
SUM(CASE WHEN info_pay_method = 'Cash' THEN info_total ELSE 0 END) AS cash_payments
FROM sale_info";
$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    if ($row) {
        // Format amounts to two decimal places
        $totalSales = number_format($row['total_sales'], 2);
        $totalAmountGiven = number_format($row['total_amount_given'], 2);
        $totalChangeGiven = number_format($row['total_change_given'], 2);
        $cardPayments = number_format($row['card_payments'], 2); // Fetch and format card payments
        $eftPayments = number_format($row['eft_payments'], 2);   // Fetch and format EFT payments
        $cashPayments = number_format($row['cash_payments'], 2); // Fetch and format cash payments
    }
}

// Close the database connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Include jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <style>
        /* Reset some default styles */
        body,
        h1,
        h2,
        h3,
        p,
        li {
            margin: 0;
            padding: 0;
            list-style: none;
        }



        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }




        .calendar-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            justify-content: center;
            padding-top: 10px;
        }

        .calendar-container input[type="date"],
        .calendar-container select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            flex: 1;
            min-width: 150px;
            max-width: 200px;
            background-color: #f8f9fa;
            transition: background-color 0.3s ease;
        }

        .calendar-container input[type="date"]:focus,
        .calendar-container select:focus {
            background-color: #e2e6ea;
            border-color: #3498db;
            outline: none;
        }

        .statistics {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .stat-card {
            min-width: 220px;
            padding: 16px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            text-align: center;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .stat-card .icon {
            font-size: 30px;
            margin-bottom: 10px;
            color: #2185be;
        }

        .stat-card h3 {
            font-size: 18px;
            color: #555;
            margin-bottom: 5px;
        }

        .stat-card p {
            font-size: 24px;
            color: #333;
            font-weight: bold;
        }

        .branch-selector {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .branch-selector span {
            padding: 10px 15px;
            border-radius: 8px;
            background-color: #e2e6ea;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .branch-selector span.active {
            background-color: #2185be;
            color: #ffffff;
        }

        .branch-selector span:hover {
            background-color: #3498db;
            color: #ffffff;
        }

        @media (max-width: 768px) {
            .calendar-container {
                justify-content: space-between;
            }

            .branch-selector {
                justify-content: space-between;
            }

            .stat-card {
                min-width: 150px;
            }
        }
    </style>
</head>

<body>

    <!-- Loading Overlay -->
    <div class="loading-screen" style="display: none;">
        <div class="loading-spinner"></div>
    </div>

    <div class="container">

        <div class="calendar-container">
            <input type="date" id="fromDate" value="<?php echo date('Y-m-d'); ?>" class="calendar-from">
            <input type="date" id="toDate" value="<?php echo date('Y-m-d'); ?>" class="calendar-to">
            <select id="dashboard-branch" class="dashboard-select">
                <option value="">All Branches</option>
                <option value="Montana" selected>Montana</option>
                <option value="Zambezi">Zambezi</option>
                <option value="Centurion">Centurion</option>
                <option value="Menlyn">Menlyn</option>
                <option value="Daspoort">Daspoort</option>
            </select>
        </div>

        <div class="statistics">
            <div class="stat-card">
                <i class="fas fa-cash-register icon"></i>
                <h3>Total Sales</h3>
                <p id="totalSalesLabel">R <?php echo $totalSales; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-credit-card icon"></i>
                <h3>Card Payments</h3>
                <p id="cardPaymentsLabel" style="color: #0066cc;">R <?php echo $cardPayments; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-exchange-alt icon"></i>
                <h3>EFT Payments</h3>
                <p id="eftPaymentsLabel" style="color: #ff7600;">R <?php echo $eftPayments; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-money-bill icon"></i>
                <h3>Cash Payments</h3>
                <p id="cashPaymentsLabel" style="color: #009900;">R <?php echo $cashPayments; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-hand-holding-usd icon"></i>
                <h3>Total Amount Given</h3>
                <p id="totalAmountGivenLabel" style="color: #ffc800;">R <?php echo $totalAmountGiven; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-hand-holding-usd icon"></i>
                <h3>Total Change Given</h3>
                <p id="totalChangeGivenLabel" style="color: #ff7600;">R <?php echo $totalChangeGiven; ?></p>
            </div>
        </div>

        <div class="branch-selector">
            <span class="active" data-branch="montana">Montana</span>
            <span data-branch="zambezi">Zambezi</span>
            <span data-branch="centurion">Centurion</span>
            <span data-branch="daspoort">Daspoort</span>
            <span data-branch="menlyn">Menlyn</span>
        </div>

        <div class="main-branch-stock-display" id="main-branch-stock-display">
            <!-- Branch stock display content -->
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Show loading screen when page first loads
            $(".loading-screen").show();

            // Load initial content
            $("#main-branch-stock-display").load("montana_low_stock.php", function () {
                // Hide loading screen after initial content is loaded
                $(".loading-screen").hide();
            });

            $(".branch-selector span").click(function () {
                $(".branch-selector span").removeClass("active");
                $(this).addClass("active");
                var branchName = $(this).data('branch');
                loadBranchStock(branchName);
            });

            $('#fromDate, #toDate, #dashboard-branch').change(function () {
                filterData();
            });

            filterData();
        });

        function loadBranchStock(branchName) {
            $(".loading-screen").show();
            var url = branchName + "_low_stock.php";
            $("#main-branch-stock-display").load(url, function () {
                $(".loading-screen").hide();
            });
        }

        function filterData() {
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();
            var selectedBranch = $('#dashboard-branch').val();

            // Perform AJAX request to fetch filtered data
            $.ajax({
                url: 'fetch_dates.php',
                type: 'GET',
                data: {
                    branch: selectedBranch,
                    fromDate: fromDate,
                    toDate: toDate
                },
                dataType: 'json',
                success: function (response) {
                    updateDashboard(response);
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching data:', xhr.status);
                }
            });
        }

        function updateDashboard(data) {
            $('#totalSalesLabel').text('R ' + parseFloat(data.total_sales).toFixed(2));
            $('#cardPaymentsLabel').text('R ' + parseFloat(data.card_payments).toFixed(2));
            $('#eftPaymentsLabel').text('R ' + parseFloat(data.eft_payments).toFixed(2));
            $('#cashPaymentsLabel').text('R ' + parseFloat(data.cash_payments).toFixed(2));
            $('#totalAmountGivenLabel').text('R ' + parseFloat(data.total_amount_given).toFixed(2));
            $('#totalChangeGivenLabel').text('R ' + parseFloat(data.total_change_given).toFixed(2));
        }
    </script>
</body>

</html>
