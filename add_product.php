<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <link rel="stylesheet" href="add.css">
</head>

<body>
    <header>
        <h1>
            Add item to Inventory
        </h1>
    </header>
    <div class="container">
        <h2>Add New Item</h2>
        <form action="add_product.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="3" required></textarea>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" required>

            <label for="img_url">Image URL:</label>
            <input type="text" id="img_url" name="img_url" required>

            <input type="submit" value="Add Item">
            <button type="button" onclick="toInventory()">Cancel</button>
        </form>

        <?php
        // Check if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Check if all required fields are present
            if (isset($_POST['name']) && isset($_POST['description']) && isset($_POST['price']) && isset($_POST['quantity']) && isset($_POST['img_url'])) {
                // Sanitize inputs to prevent SQL injection
                $name = htmlspecialchars($_POST['name']);
                $description = htmlspecialchars($_POST['description']);
                $price = floatval($_POST['price']); // Convert to float for price
                $quantity = intval($_POST['quantity']); // Convert to integer for quantity
                $img_url = htmlspecialchars($_POST['img_url']);

                // Additional sanitization and validation can be added here

                // Connect to your database
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "posperity";

                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Prepare and bind parameters for the SQL statement
                $sql = "INSERT INTO product (name, description, price, quantity, img_url, user, merchant) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdissi", $name, $description, $price, $quantity, $img_url, $user, $merchant);

                session_start();
                // Example user and merchant values (adjust as needed)
                $user = $_SESSION['userid'];;
                $merchant = $_SESSION['merchantid']; // Assuming you store the merchant ID in a session variable

                // Execute the SQL statement
                if ($stmt->execute()) {
                    echo "Item added successfully.";
                    // You can redirect the user to another page if needed
                    // header("Location: inventory.php");
                    // exit();
                } else {
                    echo "Error adding item: " . $stmt->error;
                }

                // Close the statement and connection
                $stmt->close();
                $conn->close();
            } else {
                echo "All fields are required.";
            }
        }
        ?>
    </div>
    <footer>
        <p style="font-size: 10px;">
            &copy; 2024 posperity,all rights reserved</p>
    </footer>
    <script>
        function toInventory() {
            // Redirect to another page (replace 'page-url' with the actual URL)
            window.location.href = 'inventory.php';
        }
    </script>
</body>

</html>