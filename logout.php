<?php
include 'redisconnect.php';
// Start the PHP session
session_start();
$me = $_COOKIE['PHPSESSID'];
// $logged = $redis->hgetall("user:$me");

// Handle logout
if (isset($logged["user:$me"])) {
    // Remove session variables from Redis
    $redis->del("user:$me");
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