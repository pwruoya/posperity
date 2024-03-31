<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="home.css">
    <title>Delete Product</title>
</head>

<body>
    <div id="maindiv">
        <div class="main-content" id="div2">
            <?php
            session_start();

            // Check if the product_id is set in the GET request
            if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['product_id'])) {
                $_SESSION['selectedId'] = $_GET['product_id'];

                include 'dbconfig.php'; // Include database configuration file

                try {
                    $sql = "DELETE FROM `sale` WHERE `product_id` = ?";
                    $stmt = $conn->prepare($sql);

                    $id = intval($_SESSION['selectedId']);
                    $stmt->bind_param("i", $id);

                    $stmt->execute();

                    // Check the number of rows affected
                    $rowsAffected = $stmt->affected_rows;

                    if ($rowsAffected > 0) {
                        echo "<script>";
                        echo  "alert('" . $rowsAffected . " related transactions deleted');";
                        echo "</script>";
                    }

                    $stmt->close();


                    // Prepare and bind the SQL query with a placeholder for the ID
                    $sql = "DELETE FROM `product` WHERE `product_id` = ?";
                    $stmt = $conn->prepare($sql);

                    // Bind the ID variable to the prepared statement
                    $id = intval($_SESSION['selectedId']);
                    $stmt->bind_param("i", $id);

                    // Execute the statement
                    $stmt->execute();

                    // Check if the deletion was successful
                    if ($stmt->affected_rows > 0) {
                        echo "Product deleted successfully.";
                    } else {
                        echo "Failed to delete product.";
                    }
                    echo '<script>window.location.href = "inventory.php"</script>';

                    $stmt->close();
                } catch (Exception $e) {
                    echo 'Error: ' . $e->getMessage();
                }

                $conn->close();
            } else {
                echo "Invalid request.";
            }
            ?>
        </div>
    </div>
</body>

</html>