<?php
include "redisconnect.php";
// Start session
session_start();

// Close Redis connection (Predis automatically handles connections, so no explicit close is needed)
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="home.css">
    <script src="https://kit.fontawesome.com/f7e75704ad.js" crossorigin="anonymous"></script>
    <script src="title.js"></script>
</head>

<body>
    <header>
        <h1>
            <?php
            session_start();
            if ($redis->exists('merchantname')) {
                echo $redis->get('merchantname');
            } else {
                echo '<script>window.location.href = "login.php"</script>';
            }
            ?>
        </h1>
        <div class="head">

            <div class="menu">
                <a onclick="toggleMenu()"><i class="fa-solid fa-bars"></i></a>
                <div id="hide" class="navbar-toggle">
                    <a class="bar" href="index.php">Home</a>
                    <a class="bar" href="makeSale.php">Make Sale</a>
                    <a class="bar" href="inventory.php">Inventory</a>
                    <a class="bar" href="transactions.php">Transactions</a>
                    <a class="bar" href="about.html">About</a>
                    <a class="bar" href="services.html">Services</a>
                    <a class="bar" href="logout.php">Log out</a>
                </div>
            </div>
            <nav class="nav" id="navbarLinks">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="makeSale.php">Make Sale</a></li>
                    <li><a href="inventory.php">Inventory</a></li>
                    <li><a href="transactions.php">Transactions</a></li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="services.html">Services</a></li>
                    <li><a href="logout.php"><i class="fa-regular fa-user" style="color: #ffffff;"></i> log out</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div id="maindiv">
        <div class="main-content" id="div2">
            <?php
            if ($redis->exists('merchantname')) {
                // Display user details if logged in
                echo "<h2> " . $redis->get('username') . " </h2>";
                // echo "<h2>Welcome, " . $redis->get('merchantid') . "</h2>";
                echo "<div class='profile-image'><img src='assets\profile.png' alt='Profile Image'></div>";
            } else {
                // Display login button if not logged in
                echo "<a href='login.php' class='button'>Login</a>";
            }
            ?>
        </div>

    </div>
    <footer>
        <p style="font-size: 10px;">
            &copy; 2024 posperity,all rights reserved</p>
    </footer>
</body>


</html>