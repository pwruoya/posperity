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
    <title>Inventory</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="listItems.css">
    <script src="title.js"></script>
    <script src="https://kit.fontawesome.com/f7e75704ad.js" crossorigin="anonymous"></script>
</head>


<body>
    <header>
        <h1>
            <?php
            if ($redis->exists('merchantname')) {
                echo "{$redis->get('merchantname')} Inventory";
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

    <div class="inventorydiv">
        <div>
            <div class="actionsBar">
                <div>
                    <button class="button" onclick="toAdd()">Add items</button>
                </div>
                <div>
                    <div class="searchBar">
                        <input type="text" id="searchInput" class="searchInput" placeholder="Search...">
                        <button class="searchButton" onclick="searchItems()">Search</button>
                    </div>
                </div>
            </div>
            <div class="inventorydiv1">
                <!-- <button>::</button> -->
                <?php
                // Connect to your database
                include 'dbconfig.php';

                // Fetch data from the database
                $sql = "SELECT `product_id`, `name`, `description`, `price`, `quantity`, `img_url`,
                    `user_id`, `merchant_id` FROM `product` WHERE `merchant_id` = ?";

                $stmt = $conn->prepare($sql);

                // Bind the parameter to the statement
                $stmt->bind_param("i", $redis->get('merchantid'));

                // Execute the query
                $stmt->execute();

                // Get the result
                $result = $stmt->get_result();

                // Check if the query returned any rows
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        $prod = strval($row["product_id"]);
                        echo "<div class='card' style='color:white;'>";
                        echo "<img src='" . $row["img_url"] . "' alt='Product Image'>";
                        echo "<div class='card-content'>";
                        echo "<h4>" . $row["name"] . "</h4>";
                        echo "<p>" . $row["description"] . "</p>";
                        echo "<p>Ksh. " . $row["price"] . "</p>";
                        $quantity = $row["quantity"];

                        // Check if quantity is less than zero
                        if ($quantity <= 0) {
                            // If quantity is negative, echo "Out of stock" in red
                            echo '<p style="color: red;">Out of stock</p>';
                        } else {
                            // Otherwise, echo the quantity as normal
                            echo "<p>stock: $quantity</p>";
                        }
                        echo '<div class="edit"><a href="editInventory.php?product_id=' . $prod . '"><i class="fa-regular fa-pen-to-square" style="color: #ffffff;"></i></a></div>';

                        echo "</div>";
                        echo "</div>";
                    }
                } else {

                    echo '<div id="maindiv">';
                    echo '<div class="main-content" id="div2">';
                    echo 'You have no prducts in your inventory';
                    echo '<button class="button" onclick="toAdd()">Add new products</button>';
                    echo '</div>';
                    echo '</div>';
                }
                $conn->close();
                ?>

            </div>
        </div>
        <footer>
            <p style="font-size: 10px;color:white;">
                &copy; 2024 posperity,all rights reserved</p>
        </footer>
    </div>


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


    function searchItems() {
        var input = document.getElementById('searchInput').value.trim().toLowerCase();
        var cards = document.getElementsByClassName('card');

        for (var i = 0; i < cards.length; i++) {
            var name = cards[i].querySelector('h4').textContent.toLowerCase();
            var description = cards[i].querySelector('p').textContent.toLowerCase();

            if (name.includes(input) || description.includes(input)) {
                cards[i].style.display = 'block';
            } else {
                cards[i].style.display = 'none';
            }
        }
    }

    function toAdd() {
        // Redirect to another page (replace 'page-url' with the actual URL)
        window.location.href = 'add_product.php';
    }
</script>

</html>