<?php
session_start();

require_once "./API/db.php";

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

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="./styles/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Styles for session expiration popup */
        .session-expiry-popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .session-expiry-content {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            width: 80%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .session-expiry-content h2 {
            margin-top: 0;
            font-size: 20px;
            color: #333;
        }

        .session-expiry-content p {
            font-size: 16px;
            color: #666;
        }

        .session-expiry-content button {
            margin-top: 20px;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #3498db;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .session-expiry-content button:hover {
            background-color: #2185be;
        }
    </style>
</head>
<body>
    <div class="container2">
        <header class="header">
            <h1>ADMIN PANEL</h1>
            <div>
                <span style="padding: 10px;">Welcome <?php echo $username; ?></span>
                <span style="padding: 10px;">Branch: <?php echo $branch; ?></span>
                <button onclick="logout()" class="logout-button">Logout</button>
            </div>
        </header>

        <div class="flex">
            <nav class="navbar">
                <ul class="nav-list">
                    <li class="nav-item"><button id="dashboard" class="nav-button">Dashboard</button></li>
                    <li class="nav-item"><button id="sales" class="nav-button">Sales</button></li>
                    <li class="nav-item"><button id="refunds" class="nav-button">Refunds</button></li>
                    <li class="nav-item"><button id="users" class="nav-button">Users</button></li>
                    <li class="nav-item"><button id="products" class="nav-button">Products</button></li>
                    <li class="nav-item"><button id="stock" class="nav-button">Stock</button></li>
                    <li class="nav-item"><button id="importExport" class="nav-button">Import/Export</button></li>
                </ul>
            </nav>

            <main class="main-content" id="display-page">
                Loading...
            </main>
        </div>
    </div>

    <div id="loading-screen" class="loading-screen">
        <div class="loading-spinner"></div>
    </div>

    <!-- Session Expiry Popup -->
    <div id="session-expiry-popup" class="session-expiry-popup">
        <div style="display: flex; justify-content: center; align-items: center; height: -webkit-fill-available;">
            <div class="session-expiry-content">
                <h2>Session Expired</h2>
                <p>Your session has expired due to inactivity. Please log in again.</p>
                <button onclick="redirectToLogin()">Login Page</button>
            </div>
        </div>
    </div>

    <script>
    
        window.onload = function() {
            document.body.style.zoom = "100%"; // Zoom to 125%
        };
        $(document).ready(function() {
            var timeout;
            var inactivityLimit = 900000; // 15 minutes

            // Load the dashboard page by default
            loadPage('dashboard.php');
            setActiveButton('#dashboard'); // Set initial active button

            function resetTimer() {
                clearTimeout(timeout);
                timeout = setTimeout(showSessionExpiryPopup, inactivityLimit);
            }

            function showSessionExpiryPopup() {
                $('#session-expiry-popup').fadeIn();
            }

            function redirectToLogin() {
                window.location.href = 'index.php'; // Redirect to the login page
            }

            function loadPage(page) {
                $('#loading-screen').addClass('active');
                $("#display-page").load(page, function(response, status, xhr) {
                    $('#loading-screen').removeClass('active');
                    if (status == "error") {
                        const msg = "An error occurred: ";
                        $("#display-page").html(msg + xhr.status + " " + xhr.statusText);
                    }
                });
            }

            function setActiveButton(button) {
                $(".nav-button").removeClass("active");
                $(button).addClass("active");
            }

            $("#dashboard, #sales, #refunds, #users, #products, #stock, #importExport").click(function() {
                const page = $(this).attr('id') + ".php";
                loadPage(page);
                setActiveButton(this); // Set clicked button as active
            });

            // Reset the timer on user activity
            $(document).on('mousemove keypress click', resetTimer);

            // Initialize the timer
            resetTimer();

            // Logout function
            function logout() {
                $.ajax({
                    url: './API/logout.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            window.location.href = 'index.php';
                        } else {
                            alert('Logout failed. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:", xhr.responseText);
                        alert('Logout failed due to an error.');
                    }
                });
            }

            window.logout = logout;
            window.redirectToLogin = redirectToLogin;
        });
    </script>
</body>
</html>
