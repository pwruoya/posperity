<?php
session_start();
// Check if the date parameter is set
if (isset($_GET['date'])) {
    // Sanitize the date parameter
    $date = htmlspecialchars($_GET['date']);
    $ldate = date('F j, Y', strtotime($_GET['date']));

    // Database connection parameters
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "posperity"; // Replace 'your_database_name' with your actual database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to fetch transactions for the given date
    $query = "SELECT s.sale_id, s.product_id, p.name, s.Timestamp, s.quantity, s.price, s.discount, s.selling_price, s.payment_method, s.user
              FROM sale s
              JOIN product p ON s.product_id = p.product_id
              WHERE s.merchant = ? AND DATE(s.Timestamp) = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $_SESSION['merchantid'], $date);
    $stmt->execute();
    $result = $stmt->get_result();

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
    <style>
        .tname {
            font-size: larger;
            font-weight: bold;
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            color: rgb(50, 67, 103);
        }

        .sold_prod {
            display: flex;
            padding: 3px;
            justify-content: space-between;
            border-bottom: solid 1px rgb(50, 67, 103);
        }

        .nameTime div {
            text-align: left;
        }

        .price div {
            text-align: right;
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

            .trans_button div,
            .mtrans_button div {
                font-size: 4.5vw;
            }

        }

        .elevate {
            height: fit-content;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Add box shadow for elevation */
            padding: 2px;
            margin: 5px;
        }
    </style>

    <body>
        <header>
            <h1>
                <?php
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
                <div class="elevate">
                    <h2><?php echo $ldate; ?></h2>
                    <h3>Transaction Details</h3>
                </div>
                <?php
                // Check if there are any transactions for the given date
                if ($result->num_rows > 0) {
                    // Output cards for each transaction
                    while ($row = $result->fetch_assoc()) {
                ?>
                        <div class="sold_prod" data-saleid="<?php echo htmlspecialchars($row['sale_id']); ?>">
                            <div class="nameTime">
                                <div class="tname">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                    <i><?php echo htmlspecialchars($row['sale_id']); ?></i>
                                </div>
                                <div>
                                    <?php echo date('H:i', strtotime($row['Timestamp'])); ?>
                                </div>
                            </div>
                            <div class="price">
                                <div><?php
                                        if ($row['discount'] === 0) {
                                            echo "@" . $row['price'];
                                        } else {
                                            echo "@(" . $row['price'] . "-" . $row['discount'] . ")";
                                        }
                                        ?></div>
                                <div><?php echo "<i>Ksh</i> <b>" . $row['selling_price'] . "</b>"; ?></div>
                            </div>
                        </div>

            <?php
                    }
                } else {
                    echo '<p>No transactions found for the selected date.</p>';
                }

                // Close statement and connection
                $stmt->close();
                $conn->close();
            } else {
                // Date parameter not set
                echo '<p>Error: Date parameter not provided.</p>';
            }
            ?>
            </div>

        </div>
        <footer>
            <p style="font-size: 10px;">
                &copy; 2024 posperity,all rights reserved</p>
        </footer>
    </body>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const soldProds = document.querySelectorAll('.sold_prod');
            soldProds.forEach(soldProd => {
                soldProd.addEventListener('click', () => {
                    const saleId = soldProd.getAttribute('data-saleid');
                    window.location.href = 'one_transaction.php?sale_id=' + saleId;
                });
            });
        });
    </script>

    </html>