<?php
session_start();
include "./API/db.php";

$error_message = ''; // Initialize error message

// Fetch active user data and populate dropdown menu with prepared statements
$user_query = "SELECT username, branch, job, till FROM users WHERE active = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("s", $active);
$active = 'Y';
$stmt->execute();
$user_result = $stmt->get_result();
$user_options = '';
while ($row = $user_result->fetch_assoc()) {
    $username = htmlspecialchars($row['username']);
    $branch = htmlspecialchars($row['branch']);
    $job = htmlspecialchars($row['job']);
    $till = htmlspecialchars($row['till']);
    // Check if till is set
    if (empty($till)) {
        $till = 'N/A'; // Default value if till is not set
    }
    $user_options .= '<option value="' . $username . '">' . "$username ($branch - $job - $till)" . '</option>';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_user = $_POST['username'];
    $password = $_POST['password'];

    // Validate user's credentials using prepared statements
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $selected_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user_data = $result->fetch_assoc();

        // Hash the input password and compare with the stored hashed password
        $hashed_password = hash("sha512", $password);
        if ($hashed_password === $user_data['password']) {
            // Check if the user is active
            if ($user_data['active'] === 'Y') {
                $_SESSION['username'] = $selected_user;
                $_SESSION['branch'] = $user_data['branch'];
                $_SESSION['job'] = $user_data['job'];
                $_SESSION['till'] = $user_data['till'];

                // Fetch additional user information
                $user_query = "SELECT name, surname, job FROM users WHERE username = ?";
                $stmt = $conn->prepare($user_query);
                $stmt->bind_param("s", $selected_user);
                $stmt->execute();
                $user_result = $stmt->get_result();
                $user_info = $user_result->fetch_assoc();

                // Store additional user information in session
                $_SESSION['name'] = $user_info['name'];
                $_SESSION['surname'] = $user_info['surname'];
                $_SESSION['job'] = $user_info['job'];

                // Redirect based on user role
                if ($user_data['job'] === "Admin") {
                    header("Location: admin.php");
                } else {
                    header("Location: pos.php");
                }
                exit();
            } else {
                $error_message = "User is not active.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "Invalid username or password.";
    }

    // After handling POST, redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// If redirected to login.php and session is not destroyed yet, destroy session and clear cache
if (basename($_SERVER['PHP_SELF']) === 'login.php' && isset($_SESSION['username'])) {
    session_destroy();
    // Redirect to login.php to avoid continuous refreshing
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" type="text/css" href="./styles/styles.css">
    <style>
        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
            border: 1px solid red;
            padding: 5px;
            background-color: #ffe6e6;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const userSelect = document.getElementById("username");

            userSelect.addEventListener("change", function () {
                const selectedOption = userSelect.options[userSelect.selectedIndex];
                console.log(selectedOption.text); // Log the selected user's info
            });
        });
    </script>
</head>
<body class="login-body">
    <div class="login-container">
        <h2 class="login-h2">Login</h2>
        
        <!-- Display error message if present -->
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="login-form">
            <div class="form-group">
                <label for="username" class="form-label">Select User:</label>
                <select class="login-select" name="username" id="username">
                    <?php echo $user_options; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" id="password" class="login-input" placeholder="Password" required>
            </div>
            <div class="form-group">
                <input class="login-btn" type="submit" value="Login">
            </div>
        </form>
    </div>
</body>
</html>
