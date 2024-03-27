<?php
include "redisconnect.php";
include "dbconfig.php";
// Start session
session_start();

// Close Redis connection (Predis automatically handles connections, so no explicit close is needed)
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales</title>
    <link rel="stylesheet" href="home.css">
    <script src="https://kit.fontawesome.com/f7e75704ad.js" crossorigin="anonymous"></script>
    <script src="title.js"></script>

    <title>Selected Products</title>
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

        .belowTables div {
            margin-right: 2vw;
        }

        .belowTables button {
            flex: 1;
        }

        .belowTables {
            display: flex;
            justify-content: space-around;
        }
    </style>
</head>


<body>
    <header>
        <h1>
            <?php
            if ($redis->exists('merchantname')) {
                echo "{$redis->get('merchantname')} Invoice";
            }
            ?>
        </h1>
    </header>

    <div id="maindiv">
        <div class="main-content" id="div2">
            <h1>Selected Products</h1>

            <?php
            if (isset($_GET['selected_products'])) {
                $selectedProducts = filter_input(INPUT_GET, 'selected_products', FILTER_SANITIZE_SPECIAL_CHARS);
                $productIDs = explode(',', $selectedProducts);

                $sql = "SELECT `product_id`, `name`, `price`, `quantity` FROM `product` WHERE `product_id` = ?";
                $stmt = $conn->prepare($sql);

                echo '<form action="process_sale.php" method="post">'; // Form for submitting sales data
                echo '<table>';
                echo '<tr><th>Name</th><th>Price</th><th>Stock</th><th>Quantity</th><th>Discount</th></tr>';

                foreach ($productIDs as $productId) {
                    $stmt->bind_param("i", $productId);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row["name"]) . '</td>';
                        echo '<td>' . htmlspecialchars($row["price"]) . '</td>';
                        echo '<td>' . htmlspecialchars($row["quantity"]) . '</td>';
                        echo '<td><input type="number" min="1" max="' . htmlspecialchars($row["quantity"]) . '" name="quantity[]" value="1"></td>';
                        echo '<td><input type="number" min="0" max="' . htmlspecialchars($row["price"]) . '" name="discount[]" value="0"></td>';
                        echo '<input type="hidden" name="product_id[]" value="' . htmlspecialchars($row["product_id"]) . '">';
                        echo '<input type="hidden" name="prod_price[]" value="' . htmlspecialchars($row["price"]) . '">';
                        echo '</tr>';
                    }
                }

                echo '</table>';
                echo '<div class="belowTables">';
                echo '<div><button class="button" onclick="toRoot()"><i class="fa-solid fa-house-user"></i></button></div>';
                echo '<button type="submit" class="button" name="sell">Sell</button>';
                echo '</div>';
                echo '</form></br>';

                $stmt->close();
                $conn->close();
            } else {
                echo '<p>No product(s) selected.</p>';
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