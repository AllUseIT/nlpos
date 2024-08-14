<?php
// Start the session
session_start();

// Check if the session is set
if(isset($_SESSION['username'])) {
    // Unset all session variables
    $_SESSION = [];

    // Destroy the session
    session_destroy();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
