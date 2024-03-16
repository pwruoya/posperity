<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales</title>
    <link rel="stylesheet" href="home.css">
    <script src="https://kit.fontawesome.com/f7e75704ad.js" crossorigin="anonymous"></script>
    <script src="title.js"></script>

    <title>Sales success</title>
</head>


<body>
    <header>
        <h1>
            <?php
            session_start();
            if (isset($_SESSION['merchantname'])) {
                echo "{$_SESSION['merchantname']} Invoice";
            }
            ?>
        </h1>
    </header>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            color: black;
        }

        th {
            background-color: #f2f2f2;
            color: black;
        }
    </style>
    </style>
    <div id="maindiv">
        <div class="actionsBar">

        </div>
        <div class="main-content" id="div2">
            <h1>Sale Details</h1>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sell'])) {
                // Retrieve the submitted quantities, product IDs, prices, and discounts
                $quantities = $_POST['quantity'];
                $productIDs = $_POST['product_id'];
                $prices = $_POST['prod_price']; // Prices from the form
                $discounts = $_POST['discount'];

                // Check if all arrays have the same length (for safety)
                if (count($quantities) === count($productIDs) && count($productIDs) === count($prices) && count($prices) === count($discounts)) {
                    // Database connection
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "posperity";

                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Prepare and bind parameters for the update query
                    $updateSql = "UPDATE `product` SET `quantity` = `quantity` - ? WHERE `product_id` = ? AND `quantity` >= ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("iii", $quantity, $productId, $quantity);

                    // Prepare and bind parameters for the insert query
                    $insertSql = "INSERT INTO `sale` (`product_id`, `merchant`, `Timestamp`, `quantity`, `price`, `discount`, `selling_price`, `payment_method`, `user`) VALUES (?, ?, NOW(), ?, ?, ?, ?, 'money', ?)";
                    $insertStmt = $conn->prepare($insertSql);
                    $insertStmt->bind_param("iisddii", $productId, $merchantId, $quantity, $price, $discount, $sellingPrice, $userId);

                    // Get session variables
                    $userId = $_SESSION['userid'];
                    $merchantId = $_SESSION['merchantid'];

                    // Initialize arrays to store sale details
                    $productsSold = [];
                    $quantitiesSold = [];
                    $discountsApplied = [];
                    $totalQuantity = 0;
                    $totalDiscount = 0;
                    $totalRevenue = 0;

                    // Process each selected product
                    foreach ($quantities as $index => $quantity) {
                        $productId = $productIDs[$index];
                        $price = $prices[$index];
                        $discount = $discounts[$index];

                        // Calculate the selling price
                        $sellingPrice = ($price * $quantity) - $discount;

                        // Execute the update query
                        if ($updateStmt->execute()) {
                            // Execute the insert query for transaction details
                            $insertStmt->execute();

                            // Store sale details for display
                            $productsSold[] = $productId;
                            $quantitiesSold[] = $quantity;
                            $discountsApplied[] = $discount;
                            $prodPrice[] = $price;
                            $prodSellingPrice[] = $sellingPrice;
                            $totalQuantity += $quantity;
                            $totalDiscount += $discount;
                            $totalRevenue += $sellingPrice;
                        }
                    }

                    // Close prepared statements and database connection
                    $updateStmt->close();
                    $insertStmt->close();

                    // Display the products, quantities sold, discounts applied, and totals
                    echo '<h2>Confirmed</h2>';
                    echo '<table>';
                    echo '<tr><th>Product Name</th><th>Quantity Sold</th><th>Discount Applied</th></tr>';
                    foreach ($productsSold as $index => $productId) {
                        // Fetch product name from database
                        $productNameSql = "SELECT `name` FROM `product` WHERE `product_id` = ?";
                        $productNameStmt = $conn->prepare($productNameSql);
                        $productNameStmt->bind_param("i", $productId);
                        $productNameStmt->execute();
                        $productNameResult = $productNameStmt->get_result();
                        if ($productNameResult->num_rows > 0) {
                            $productNameRow = $productNameResult->fetch_assoc();
                            $productName = htmlspecialchars($productNameRow['name']);
                        } else {
                            $productName = 'Unknown Product';
                        }
                        $productNameStmt->close();

                        echo '<tr>';
                        echo '<td>' . $productName . '</td>';
                        echo '<td>' . $quantitiesSold[$index] . 'x'.$prodPrice[$index] . '</td>';
                        echo '<td>' . $discountsApplied[$index] .'('.$prodSellingPrice[$index].')'. '</td>';
                        echo '</tr>';
                    }
                    echo '<tr><th>Total</th><th>' . $totalQuantity . '</th><th>' . $totalDiscount . '</th></tr>';
                    echo '</table>';
                    echo '<p>Total Revenue: Ksh' . $totalRevenue . '</p>';

                    echo '<div><button class="button" onclick="toRoot()"><i class="fa-solid fa-house-user"></i></button></div>';

                    $conn->close();
                } else {
                    echo "Error: Quantity, product ID, price, and discount arrays do not match.";
                }
            } else {
                // Redirect to the homepage or error page if the form was not submitted properly
                header("Location: index.php");
                exit();
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