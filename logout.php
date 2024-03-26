<?php
include 'redisconnect.php';
// Start the PHP session
session_start();


// Handle logout
if ($redis->exists('merchantname')) {
    // Remove session variables from Redis
    $redis->del('username', 'merchantname', 'merchantid', 'userid');
    // Clear all session variables
    session_unset();
    // Destroy the session
    session_destroy();
    // Redirect to the login page or any other desired page

}
header("Location: login.php?logout=true");
exit();
?>

<!-- Your HTML content here -->

<a href="login.php?logout=true">Logout</a>

<!-- Your remaining HTML content -->