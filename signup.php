<?php
include "mail.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="sign.css">
</head>

<body>
    <header>
        <h1>Distributed systems assignment sign up page</h1>
    </header>
    <div class="signup-form">
        <h2>Sign Up</h2>
        <form action="signup.php" method="post">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="adm">Registration Number</label>
                <input type="text" id="adm" name="adm" required>
            </div>
            <div class="form-group">
                <label for="mobile">Mobile Number</label>
                <input type="text" id="mobile" name="mobile" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="cpassword">Confirm Password</label>
                <input type="password" id="cpassword" name="cpassword" required>
            </div>
            <button type="submit">Sign Up</button>
            <?php if ($_SERVER['REQUEST_METHOD']!='POST') : ?>
                <div> Already have an account,<a href='login.php'>login</a>?</div>
            <?php endif; ?>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Retrieve form data
                $name = $_POST['name'];
                $username = $_POST['username'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $cpassword = $_POST['cpassword'];
                $adress = $_POST['address'];
                $adm = $_POST['adm'];
                $mobile = $_POST['mobile'];

                // You can add your validation and database insertion logic here

                // For demonstration purposes, let's just print the received data
                // echo "Username: $username <br>";
                // echo "Email: $email <br>";
                // echo "Password: $password <br>";
                // Example usage:
                if ($password == $cpassword) {
                    if (is_string($name)) {
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {


                            // Create connection
                            $conn = new mysqli($servername, $dbusername, $dbpassword, $database);

                            // Check connection
                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }

                            // Hash the password
                            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                            $findsql = $sql = "SELECT `UID` FROM `users` WHERE `Username` = ? OR `email` = ? OR `UID` = ?";
                            $stmt = $conn->prepare($sql);

                            // Bind the parameters to the statement
                            
                            $stmt->bind_param("sss", $username, $email, $adm);

                            // Execute the query
                            $stmt->execute();

                            // Get the result
                            $result = $stmt->get_result();
                            // echo $result->num_rows;
                            if ($result->num_rows == 0) {

                                // SQL query with placeholders
                                $sql = "INSERT INTO `users`(`UID`, `Username`, `email`, `password`, `Name`, `mobile`, `address`) VALUES (?,?,?,?,?,?,?)";

                                // Prepare and bind the statement
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("sssssss", $adm, $username, $email, $password, $name, $mobile, $adress);

                                // Execute the statement
                                if ($stmt->execute()) {
                                    //if db written seccessfully then:
                                    $to_email = $email;
                                    $subject = "WELCOME";
                                    $body = "
                            <h1>Welcome to Our Website</h1>
                            <p>Dear $username,</p>
                            <p>Thank you for joining our website. We are excited to have you on board!</p>
                            <p>Best regards,<br>Distributed systems</p>";

                                    $from_email = "keterdummy@gmail.com";

                                    if (sendEmail($to_email, $subject, $body, $from_email)) {
                                        echo "Email sent successfully.";
                                        header("Location: login.php");
                                    } else {
                                        echo "Email sending failed.";
                                    }
                                } else {
                                    echo "Error: " . $sql . "<br>" . $conn->error;
                                }

                                // Close statement and connection
                                $stmt->close();
                                $conn->close();
                            } else {
                                echo "<div style='color:red'>You already have an account,<a style='color:blue' href='login.php'>login.</a></div>";
                            }
                        } else {
                            echo "Email address is not valid.";
                        }
                    } else {
                        echo "Your full name should not contain numbers and symbols";
                    }
                } else {
                    echo "passwords do not match";
                }
            }
            ?>
        </form>
    </div>
    <footer>
        <p>&copy; 2024 Your Company. All rights reserved.</p>
    </footer>

</body>

</html>