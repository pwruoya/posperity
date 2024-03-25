<?php
include 'redisconnect.php';
// Start the PHP session
session_start();

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    // Store session variables in Redis
    $redis->set('username', $_SESSION['username']);
    $redis->set('merchantname', $_SESSION['merchantname']);
    $redis->set('merchantid', $_SESSION['merchantid']);
    $redis->set('userid', $_SESSION['userid']);
}

// Handle logout
if (isset($_GET['logout'])) {
    // Remove session variables from Redis
    $redis->del('username', 'merchantname', 'merchantid', 'userid');
    // Clear all session variables
    session_unset();
    // Destroy the session
    session_destroy();
    // Redirect to the login page or any other desired page
    header("Location: login.php");
    exit();
}
?>

<!-- Your HTML content here -->

<a href="login.php?logout=true">Logout</a>

<!-- Your remaining HTML content -->