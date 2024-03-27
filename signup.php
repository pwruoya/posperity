<?php
include "mail.php";
include 'dbconfig.php';
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
        <h1>Posperity System</h1>
    </header>
    <div class="signup-form">
        <h2>Sign Up</h2>
        <form action="signup.php" method="post">
            <div class="form-group">
                <label for="adm">Merchant Name</label>
                <input type="text" id="mname" name="mname" required>
            </div>
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="mobile">Mobile Number</label>
                <input type="text" id="mobile" name="mobile" required>
            </div>
            <div class="form-group">
                <label for="address">Bussiness Address</label>
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
            <?php if ($_SERVER['REQUEST_METHOD'] != 'POST') : ?>
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
                $address = $_POST['address'];
                $mname = $_POST['mname'];
                $mobile = $_POST['mobile'];


                if ($password == $cpassword) {
                    if (is_string($name)) {
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {


                            // Hash the password
                            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                            // check if username or email already exists
                            // if rows > 0 then write to database
                            // then send email

                            $findsql = $sql = "SELECT `user_id` FROM `user` WHERE `user_name` = ? OR `email` = ?";
                            $stmt = $conn->prepare($sql);

                            // Bind the parameters to the statement

                            $stmt->bind_param("ss", $username, $email);

                            // Execute the query
                            $stmt->execute();

                            // Get the result
                            $result = $stmt->get_result();
                            // echo $result->num_rows;
                            if ($result->num_rows == 0) {

                                // SQL query with placeholders
                                $sql = "INSERT INTO `merchant`(`merchantname`) VALUES (?)";
                                // Prepare and bind the statement
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("s", $mname);

                                // Execute the statement
                                if ($stmt->execute()) {
                                    $sql = "SELECT `mid` FROM `merchant` WHERE `merchantname` = ?";
                                    $stmt = $conn->prepare($sql);

                                    // Bind the parameter to the statement
                                    $stmt->bind_param("s", $mname);

                                    // Execute the query
                                    $stmt->execute();

                                    // Get the result
                                    $result = $stmt->get_result();

                                    // Check if the query returned any rows
                                    if ($result->num_rows > 0) {
                                        $row = $result->fetch_assoc();
                                        $mid  = $row['mid'];
                                        // SQL query with placeholders
                                        $sql = "INSERT INTO `user`( `user_name`, `password`, `merchant_id`, `email`, `fullname`, `address`, `mobile`) VALUES (?,?,?,?,?,?,?)";
                                        // Prepare and bind the statement
                                        $stmt = $conn->prepare($sql);
                                        $stmt->bind_param("sssssss", $username, $hashedPassword, $mid, $email, $name, $address, $mobile);

                                        // Execute the statement
                                        if ($stmt->execute()) {
                                            //if db written seccessfully then:
                                            $to_email = $email;
                                            $subject = "WELCOME";
                                            $body = "
                                                 <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                                                    <h1>Welcome to Posperity - Your Business Empowered with Our POS Web App!</h1>
                                                    <p>Dear $name,</p>
                                                    <p>On behalf of the entire team here at Posperity, I want to extend a warm welcome to you as a valued
                                                        member of our community! We are thrilled to have you on board and excited to embark on this journey together
                                                        to elevate your business to new heights.</p>

                                                    <p>We're here to support you every step of the way. Whether you have questions, need assistance, or want to
                                                        explore additional features, our dedicated support team is just a click away.</p>
                                                    <p>Thank you once again for choosing Posperity. We're committed to your success and look forward to
                                                        helping you thrive in the digital market.</p>
                                                    <p>Warm regards,</p>
                                                    <p>Posperity</p>
                                                </div>";

                                            $from_email = "keterdummy@gmail.com";

                                            if (sendEmail($to_email, $subject, $body, $from_email)) {
                                                echo "Email sent successfully.";
                                                echo '<script>window.location.href = "login.php"</script>';
                                            } else {
                                                echo "Email sending failed.";
                                            }
                                        } else {
                                            echo "Error: " . $sql . "<br>" . $conn->error;
                                        }
                                    } else {
                                        echo "merchant does not exist";
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
        <p>&copy; 2024 Posperity. All rights reserved.</p>
    </footer>

</body>

</html>