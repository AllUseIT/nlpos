<?php 
include "./API/db.php"; 
session_start();

if(!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$branch = $_SESSION['branch'];
$job = $_SESSION['job'];

// Dynamically select the table based on the branch
$tableName = strtolower($branch); // Assuming you have tables like products_branchname
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NLpos</title>
    <link rel="stylesheet" type="text/css" href="./styles/styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        // Function to handle page content loading
        function loadContent(page, buttonId) {
            $(".main-span-left .span-left").removeClass("active");
            $("#" + buttonId).addClass("active");
            $("#display-page").load(page);
        }

        // Load initial content
        loadContent("pos_page.php", "posButton");

        // Event listeners for navigation
        $('#posButton').on('click', function() {
            loadContent("pos_page.php", "posButton");
        });

        $('#salesButton').on('click', function() {
            loadContent("sales_page.php", "salesButton");
        });

        $('#p3_page').on('click', function() {
            var job = "<?php echo $job; ?>";
            if (job === "Supervisor") {
                loadContent("p3_page.php", "p3_page");
            } else {
                alert("You do not have permission to access this feature.");
            }
        });

        // Logout function
        $('.span-right-logout').on('click', function() {
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
        });
    });
    </script>
</head>
<body>
    <div style="width: -webkit-fill-available;">
        <div class="header">
            <div class="main-span-left">
                <span class="span-left" id="posButton">POS</span>
                <span class="span-left" id="salesButton">Sales</span>
                <span class="span-left <?php if($job !== 'Supervisor') echo 'disabled'; ?>" id="p3_page">P3</span>
            </div>
            <div class="main-span-right">
                <span class="span-right">Title: <?php echo $job; ?></span>
                <span class="span-right">Branch: <?php echo $branch; ?></span>
                <span class="span-right">Welcome <?php echo $username; ?></span>
                <span class="span-right-logout">Logout</span>
            </div>
        </div>
        <div id="display-page"></div>
    </div>
    
</body>
</html>
