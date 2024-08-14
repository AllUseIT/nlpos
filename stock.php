<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock</title>
    <link rel="stylesheet" type="text/css" href="./styles/styles.css">
    <style>
        .active-branch {
            background-color: #2185be;
            color: #fff;
            padding-bottom: 0px;
            cursor: pointer;
        }
        .loading-screen {
            position: fixed;
            top: 51%;
            left: 56%;
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

        .branch-selector span.active-branch {
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function(){
        var requestCount = 0; // Counter for ongoing requests

        // Function to show loading screen
        function showLoadingScreen() {
            $(".loading-screen").show();
        }

        // Function to hide loading screen
        function hideLoadingScreen() {
            $(".loading-screen").hide();
        }

        // Show loading screen when page first loads
        showLoadingScreen();

        // Load initial content
        $("#main-branch-display").load("montana.php", function() {
            hideLoadingScreen(); // Hide loading screen after initial content is loaded
        });
        
        $(".branch-selector span").click(function(){
            $(".branch-selector span").removeClass("active-branch");
            $(this).addClass("active-branch");
            var branchName = $(this).text();
            
            showLoadingScreen(); // Show loading overlay when a request is made
            requestCount++; // Increment request counter

            switch(branchName) {
                case "Montana":
                    $("#main-branch-display").load("montana.php", function() {
                        requestCount--; // Decrement request counter
                        if (requestCount === 0) {
                            hideLoadingScreen(); // Hide loading overlay when all content is loaded
                        }
                    });
                    break;
                case "Zambezi":
                    $("#main-branch-display").load("zambezi.php", function() {
                        requestCount--; // Decrement request counter
                        if (requestCount === 0) {
                            hideLoadingScreen(); // Hide loading overlay when all content is loaded
                        }
                    });
                    break;
                case "Centurion":
                    $("#main-branch-display").load("centurion.php", function() {
                        requestCount--; // Decrement request counter
                        if (requestCount === 0) {
                            hideLoadingScreen(); // Hide loading overlay when all content is loaded
                        }
                    });
                    break;
                case "Daspoort":
                    $("#main-branch-display").load("daspoort.php", function() {
                        requestCount--; // Decrement request counter
                        if (requestCount === 0) {
                            hideLoadingScreen(); // Hide loading overlay when all content is loaded
                        }
                    });
                    break;
                case "Menlyn":
                    $("#main-branch-display").load("menlyn.php", function() {
                        requestCount--; // Decrement request counter
                        if (requestCount === 0) {
                            hideLoadingScreen(); // Hide loading overlay when all content is loaded
                        }
                    });
                    break;
                default:
                    console.error("Unknown branch: " + branchName);
            }
        });
    });
    </script>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-screen">
        <div class="loading-spinner"></div>
    </div>

    <div class="main-stock">
        <div>
            <h2 class="login-h2">Stock</h2>
        </div>
        <div class="branch-selector">
            <span class="active-branch" data-branch="montana">Montana</span>
            <span data-branch="zambezi">Zambezi</span>
            <span data-branch="centurion">Centurion</span>
            <span data-branch="daspoort">Daspoort</span>
            <span data-branch="menlyn">Menlyn</span>
        </div>
        <div class="main-branch-display" id="main-branch-display">

        </div>
    </div>
</body>
</html>
