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
    <title>Login</title>
    <link rel="stylesheet" href="sign.css">
</head>
<style>
    .cookie-banner {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 98%;
        background-color: #333;
        color: #fff;
        padding: 10px 20px;
        text-align: center;
    }

    .cookie-button {
        background-color: #4CAF50;
        color: #fff;
        border: none;
        padding: 10px 20px;
        text-align: center;
        display: inline-block;
        margin: 10px;
        cursor: pointer;
        border-radius: 5px;
    }
</style>

<body>
    <header>
        <h1>Posperity System <small>test</small> </h1>
    </header>


    <div class="login-form">
        <h2>Login</h2>
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
            <div style="color: red;">
                <?php
                $data = 1;

                if ($_SERVER["REQUEST_METHOD"] == "POST") {

                    // Retrieve hashed password from the database

                    // Database connection parameters
                    include 'dbconfig.php';

                    // User input (username or email)
                    $userInput = $_POST["username"];
                    $userEnteredPassword = $_POST["password"];
                    // Prepare SQL statement
                    $sql = "SELECT u.user_id, u.user_name, u.password, u.merchant_id, u.email, u.fullname, u.address, u.mobile,m.merchantname 
                        FROM user u LEFT JOIN merchant m ON u.merchant_id = m.mid WHERE u.user_name = ? OR u.email = ?";
                    $stmt = $conn->prepare($sql);

                    // Bind the parameter to the statement
                    $stmt->bind_param("ss", $userInput, $userInput);

                    // Execute the query
                    $stmt->execute();

                    // Get the result
                    $result = $stmt->get_result();

                    // Check if the query returned any rows
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $hashedPassword = $row['password'];
                        $me = $row['user_id'];
                        if (password_verify($userEnteredPassword, $hashedPassword)) {
                            // Store session data in Redis
                            $sessionData = [
                                'username' => $row['user_name'],
                                'merchantname' => $row['merchantname'],
                                'merchantid' => $row['merchant_id'],
                                'userid' => $row['user_id']
                            ];
                            $redis->hmset("user:$me", $sessionData);
                            $redis->expire("user:$me", 7200);
                            setcookie("user_id", $me, time() + 7200, "/");
                            // Redirect to the home page
                            // header("Location: index.php");
                            echo '<script>window.location.href = "index.php"</script>';
                            exit();
                        } else {
                            echo "Incorrect password, please retry";
                        }
                    } else {
                        echo "Incorrect email or username, please retry";
                    }
                    // Close statement and connection
                    $stmt->close();
                    $conn->close();
                }
                ?>
            </div>
            <div>Don't have an account,<a href="signup.php">sign up</a>?</div>
            <div><a href="reset.php?data=<?php echo urlencode($data); ?>">Forgot Password?</a></div>


    </div>
    </form>
    <div class="cookie-banner" id="cookieBanner">
        This website uses cookies to ensure you get the best experience on our website.
        <button class="cookie-button" onclick="acceptCookies()">Accept Cookies</button>
    </div>
    <footer>
        <p>&copy; 2024 Posperity. All rights reserved.</p>
    </footer>
</body>
<script>
    function acceptCookies() {
        // Set a cookie to indicate that the user has accepted cookies
        document.cookie = "cookiesAccepted=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
        // Hide the cookie banner
        document.getElementById("cookieBanner").style.display = "none";
    }

    // Check if the user has already accepted cookies
    if (document.cookie.includes("cookiesAccepted=true")) {
        document.getElementById("cookieBanner").style.display = "none";
    }
</script>

</html>