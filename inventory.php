<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link rel="stylesheet" href="home.css">
    <script src="https://kit.fontawesome.com/f7e75704ad.js" crossorigin="anonymous"></script>
</head>
<style>
    table {
        width: 96vw;
        border-collapse: collapse;
        margin: 2vw;
    }

    th,
    td {
        border: 1px solid #dddddd;
        padding: 8px;
        text-align: center;
    }

    .inventorydiv {
        flex: 1;
        display: flex;
        justify-content: space-around;
        flex-direction: column;
        background-color: rgba(0, 0, 0, 0.8);
    }

    body {
        justify-content: flex-start;
    }
</style>

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
        <div class="head">
            <a href="logout.php">Logout</a>
            <div class="menu">
                <a onclick="toggleMenu()"><i class="fa-solid fa-bars"></i></a>
                <div id="hide" class="navbar-toggle">
                    <a class="bar" href="index.php">Home</a>
                    <a class="bar" href="#">Make Sale</a>
                    <a class="bar" href="inventory.php">Inventory</a>
                    <a class="bar" href="#">Transactions</a>
                    <a class="bar" href="about.html">About</a>
                    <a class="bar" href="services.html">Services</a>
                </div>
            </div>
            <nav class="nav" id="navbarLinks">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#">Make Sale</a></li>
                    <li><a href="inventory.php">Inventory</a></li>
                    <li><a href="#">Transactions</a></li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="services.html">Services</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="inventorydiv">
        <!-- <button>::</button> -->
        <table>
            <thead>
                <tr>
                    <!-- <th>Product ID</th> -->
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <!-- <th>Image URL</th> -->
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Connect to your database
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "posperity";

                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch data from the database
                $sql = "SELECT `product_id`, `name`, `description`, `price`, `quantity`, `img_url`,
             `user`, `merchant` FROM `product` WHERE `merchant` = ?";

                $stmt = $conn->prepare($sql);

                // Bind the parameter to the statement
                $stmt->bind_param("i", $_SESSION['merchantid']);

                // Execute the query
                $stmt->execute();

                // Get the result
                $result = $stmt->get_result();

                // Check if the query returned any rows
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        // echo "<td>" . $row["product_id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["description"] . "</td>";
                        echo "<td>" . $row["price"] . "</td>";
                        echo "<td>" . $row["quantity"] . "</td>";
                        //echo "<td>" . $row["img_url"] . "</td>";
                        echo "<td>" . $row["user"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No data found</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
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
</script>

</html>