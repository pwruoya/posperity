<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="home.css">
    <script src="https://kit.fontawesome.com/f7e75704ad.js" crossorigin="anonymous"></script>
</head>

<body>
    <header>
        <h1>
            <?php
            session_start();
            if (isset($_SESSION['merchantname'])) {
                echo $_SESSION['merchantname'];
            }
            ?>
        </h1>
        <div class="menu">
            <a onclick="toggleMenu()"><i class="fa-solid fa-bars"></i></a>
            <div id="hide" class="navbar-toggle" >
                <div class="bar">Home</div>
                <div class="bar">About</div>
                <div class="bar">Services</div>
                <div class="bar">Services</div>
            </div>
        </div>
        <nav class="nav" id="navbarLinks">
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="services.html">Services</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div id="maindiv">
        <div class="main-content" id="div2">
            <?php
            if (isset($_SESSION['username'])) {
                // Display user details if logged in
                echo "<h2>Welcome, " . $_SESSION['username'] . "</h2>";
                echo "<div class='profile-image'><img src='assets\profile.png' alt='Profile Image'></div>";
            } else {
                // Display login button if not logged in
                echo "<a href='login.php' class='button'>Login</a>";
            }
            ?>
            <button class="button">
                make sale
            </button>
        </div>

    </div>
    <footer>
        <p style="font-size: 10px;">
            &copy; 2024 posperity,all rights reserved</p>
    </footer>
</body>
<script>
    function toggleMenu() {
        var navbarLinks = document.getElementById("hide");
        if (navbarLinks.style.display === "flex") {
            navbarLinks.style.display = "none";
        } else {
            navbarLinks.style.display = "flex";
        }
    }
</script>

</html>