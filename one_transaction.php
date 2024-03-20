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
<style>
    .headdiv img {
        max-width: 350px;
    }

    .headdiv {
        display: flex;
        justify-content: space-around;
        align-items: center;
    }

    #details {
        display: flex;
        flex-direction: column;
        justify-content: space-around;
        align-items: start;
    }

    #pname {
        font-size: xx-large;
        color: rgb(50, 67, 103);
        font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
    }

    .main-content {
        background-color: rgba(255, 255, 255, 0.9);
    }

    @media screen and (max-width: 650px) {
        #maindiv {
            flex: 1;
            background-color: rgba(255, 255, 255, 0.7);
        }

        .main-content {
            flex: 1;
            margin-top: 0;
            background-color: transparent;
        }

        .headdiv {
            flex-direction: column;
            align-items: start;
        }

        .headdiv img {
            max-width: 100%;
        }
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
            <a href="logout.php">
                <?php
                // Check if "userid" session variable is set and not equal to 0
                if (isset($_SESSION["userid"]) && $_SESSION["userid"] != 0) {
                    echo 'log out';
                } else {
                    echo 'log in';
                }
                ?>
            </a>
            <div class="menu">
                <a onclick="toggleMenu()"><i class="fa-solid fa-bars"></i></a>
                <div id="hide" class="navbar-toggle">
                    <a class="bar" href="index.php">Home</a>
                    <a class="bar" href="makeSale.php">Make Sale</a>
                    <a class="bar" href="inventory.php">Inventory</a>
                    <a class="bar" href="transactions.php">Transactions</a>
                    <a class="bar" href="about.html">About</a>
                    <a class="bar" href="services.html">Services</a>
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
                </ul>
            </nav>
        </div>
    </header>

    <div id="maindiv">
        <div class="main-content" id="div2">
            <?php
            // Database connection parameters
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "posperity"; // Replace 'your_database' with your actual database name

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Check if sale_id is passed via GET
            if (isset($_GET['sale_id'])) {
                $saleId = $_GET['sale_id'];

                // Fetch sale details based on sale_id and join with merchant table
                $query = "SELECT s.*, p.img_url ,p.name, p.description, p.price, p.quantity AS product_quantity, u.*, m.`merchantname` AS merchant_name, DATE_FORMAT(s.Timestamp, '%Y-%m-%d %H:%i:%s') AS formatted_timestamp 
          FROM `sale` s 
          INNER JOIN `product` p ON s.product_id = p.product_id 
          INNER JOIN `user` u ON s.user = u.user_id 
          INNER JOIN `merchant` m ON s.merchant = m.`mid`
          WHERE s.`sale_id` = ?";

                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $saleId);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $ldate = date('F j, Y', strtotime($row['formatted_timestamp']));
                    $time = date('H:i', strtotime($row['Timestamp']));
                    // Display transaction details
                    echo "<h1>Transaction Details</h1>";
                    // echo "<p><strong>Sale ID:</strong> " . $row['sale_id'] . "</p>";
                    echo "<div class='headdiv'><div id='details'><strong id='pname'>" . $row['name'] . "</strong>";
                    echo "<p><strong> " . $ldate . "</strong></p>";
                    echo "<p><strong> " . $time . "</strong></p>";

                    // echo "<p><strong>Merchant Name:</strong> " . $row['merchant_name'] . "</p>";
                    echo "<p>@" . $row['price'] . "x" . $row['quantity'] . ": " . $row['price'] * $row['quantity'] . "</p>";

                    echo "<p><strong>Discount:</strong> " . $row['discount'] . "</p>";
                    echo "<p><strong>Total:</strong> " . $row['selling_price'] . "</p>";
                    // echo "<p><strong>Payment Method:</strong> " . $row['payment_method'] . "</p>";
                    echo "<p><strong>by:</strong> " . $row['fullname'] . "</p>
                    </div>";
                    echo "<div><img src= " . $row['img_url'] . "</div></div>";
                    // Add more details as needed

                } else {
                    echo "Sale details not found.";
                }

                // Close connections
                $stmt->close();
                $conn->close();
            } else {
                echo "Sale ID not provided.";
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