<?php
include "mail.php";
include "dbconfig.php";
session_start();
$resetToken = generateResetToken();
$reset = false;
$rUID = "";

// Destination page (destination.php)
$receivedData = isset($_GET['data']) ? urldecode($_GET['data']) : false; // Decode the URL parameter

if ($receivedData != false) {
    // Prepare the SQL statement with a parameter placeholder
    $sql = "SELECT `rUID`, `time` FROM `reset` WHERE `token` = ?";
    $stmt = $conn->prepare($sql);

    // Bind the parameter to the statement
    $stmt->bind_param("s", $receivedData);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if the query returned any rows
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Access the values of UID and Username columns
        $rUID  = $row['rUID'];
        $reset = true;
        error_log("rUID :" . $rUID . " retrieved from :" . $receivedData);

        // Store $rUID in the session variable
        $_SESSION['rUID'] = $rUID;
    }
    error_log("after IUD: " . $rUID);
}


function sendPasswordResetEmail($email, $resetToken)
{
    // Get the current page's protocol (http or https)
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';

    // Get the host (localhost or domain name)
    $host = $_SERVER['HTTP_HOST'];

    // Get the path and script name (e.g., /project/example.php)
    $path = $_SERVER['REQUEST_URI'];

    // Construct the full URL
    $rootDirectory = $protocol . '://' . $host . $path;
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $to_email = $email;
        $subject = "Reset Password";
        $body = '
        <p>Hello,</p>
        <p>You have requested to reset your password. Please click on the link below to reset your password:</p>
        <p><a href=" ' . $rootDirectory . '?data=' . $resetToken . '">Reset Password</a></p>
        <p>If you did not request this, please ignore this email.</p>
        <p>Regards,<br>Distributed Assignment</p>
        ';

        $from_email = "keterdummy@gmail.com";

        if (sendEmail($to_email, $subject, $body, $from_email)) {
            return "Email sent successfully.";
        } else {
            return "Email sending failed.";
        }
    } else {
        return "Email address is not valid.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="sent.css">
    <link rel="stylesheet" href="sign.css">
</head>

<body>
    <header>
        <h1>Distributed systems Assignment Login Page</h1>
    </header>


    <div class="login-form">
        <h2>Reset password</h2>
        <form action="reset.php" method="post">
            <?php if ($receivedData == 1) : ?>
                <div class="form-group">
                    <label for="username">Enter your email adress</label>
                    <input type="text" id="email" name="email" required>
                </div>
                <button type="submit" name="send">send reset email</button>
            <?php elseif ($reset) : ?>
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="cpassword">Confirm New Password</label>
                    <input type="password" id="cpassword" name="cpassword" required>
                </div>
                <button type="submit" name="change">reset password</button>
            <?php elseif ($receivedData == 2) : ?>
                <div class="form-group">
                    <label for="username">Enter your email adress</label>
                    <input type="text" id="email" name="email" required>
                </div>
                <button type="submit" name="send">send reset email</button>
            <?php else : ?>
                <div class="container">
                    <div class="alert">
                        <h2>Password has been reset successfully</h2>
                        <p>A confirmation email has been sent.</p>
                        <a href="login.php" class="btn">Return to Login</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['send'])) {
                    $email = $_POST['email'];

                    // Prepare the SQL statement with a parameter placeholder
                    $sql = "SELECT `user_id`, `user_name` FROM user WHERE email = ?";
                    $stmt = $conn->prepare($sql);

                    // Bind the parameter to the statement
                    $stmt->bind_param("s", $email);

                    // Execute the query
                    $stmt->execute();

                    // Get the result
                    $result = $stmt->get_result();


                    // Check if the query returned any rows
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        // Access the values of UID and Username columns
                        $rUID  = $row['user_id'];
                        $username = $row['user_name'];

                        // Values to insert or update

                        $token = $resetToken;
                        $time = date("Y-m-d H:i:s"); // Current date and time

                        // Check if the rUID exists in the reset table
                        $sql_check = "SELECT * FROM `reset` WHERE `rUID` = ?";
                        $stmt_check = $conn->prepare($sql_check);
                        $stmt_check->bind_param("s", $rUID);
                        $stmt_check->execute();
                        $result_check = $stmt_check->get_result();

                        if ($result_check->num_rows > 0) {
                            // rUID exists, update the record with current time and token
                            $sql_update = "UPDATE `reset` SET `token` = ?, `time` = ? WHERE `rUID` = ?";
                            $stmt_update = $conn->prepare($sql_update);
                            $stmt_update->bind_param("sss", $token, $time, $rUID);
                            $stmt_update->execute();

                            echo "Record updated successfully.";
                        } else {
                            // rUID does not exist, insert a new record with current time and token
                            $sql_insert = "INSERT INTO `reset`(`rUID`, `token`, `time`) VALUES (?, ?, ?)";
                            $stmt_insert = $conn->prepare($sql_insert);
                            $stmt_insert->bind_param("sss", $rUID, $token, $time);
                            $stmt_insert->execute();

                            echo "Record inserted successfully.";
                        }
                        echo sendPasswordResetEmail($email, $resetToken);
                        // Close the statements and connection
                        $stmt_check->close();
                        if (isset($stmt_update)) $stmt_update->close();
                        if (isset($stmt_insert)) $stmt_insert->close();
                        echo '<script>window.location.href = "sent.html"</script>';
                    } else {
                        echo "No account with this email.";
                        $receivedData = 2;
                    }

                    // Close statement and connection
                    $stmt->close();
                    $conn->close();
                } elseif (isset($_POST['change'])) {
                    // change password button was clicked
                    error_log("submit 2.");
                    try {
                        $cpassword = $_POST['cpassword'];
                        $password = $_POST['password'];
                        $rUID = $_SESSION["rUID"];
                        if ($password == $cpassword) {
                            error_log("passwords match.");
                            // Hash the password
                            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                            // SQL query with placeholders
                            $sql = "UPDATE `user` SET `password` = ? WHERE `user_id` = ?";

                            // Prepare and bind the statement
                            $stmt = $conn->prepare($sql);
                            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                            $stmt->bind_param("ss", $hashedPassword, $rUID);

                            // Prepare the SQL statement with a parameter placeholder
                            $sql1 = "SELECT `email`, `user_name` FROM user WHERE user_id = ?";
                            $stmt1 = $conn->prepare($sql1);

                            // Bind the parameter to the statement
                            $stmt1->bind_param("s", $rUID);

                            // Execute the query
                            $stmt1->execute();
                            error_log("Email & username query executed in search for UID:" . $rUID);
                            // Get the result
                            $result1 = $stmt1->get_result();
                            // error_log("Get result".$result1);
                            // Check if the query returned any rows
                            if ($result1->num_rows > 0) {
                                $row = $result1->fetch_assoc();
                                // Access the values of UID and Username columns
                                $email  = $row['email'];
                                $username = $row['user_name'];
                                error_log("User found" . $email);
                                // Execute the statement
                                if ($stmt->execute()) {
                                    error_log("New password saved.");
                                    $to_email = $email;
                                    $subject = "SUCCESS";
                                    $body = "
                                        <h1>Password Reset Successful</h1>
                                        <p>Dear '.$username',</p>
                                        <p>Your password has been successfully reset. If you did not request this reset, please contact us immediately.</p>
                                        <p>Best regards,<br>Distributed systems</p>
                                    ";
                                    $from_email = "keterdummy@gmail.com";

                                    if (sendEmail($to_email, $subject, $body, $from_email)) {
                                        echo "Email sent successfully.";
                                        error_log("confirmation email sent");
                                    } else {
                                        echo "Email sending failed.";
                                    }
                                } else {
                                    echo "Error: " . $sql . "<br>" . $conn->error;
                                }
                            } else {
                                error_log("reset code not found");
                            }
                            // Close statement and connection
                            $stmt->close();
                            echo '<script>window.location.href = "login.php"</script>';
                        } else {
                            echo "passwords do not match";
                        }
                    } catch (Exception $e) {
                        echo 'Error: ' . $e->getMessage();
                    }

                    $conn->close();
                } else {
                    // Neither button was clicked
                    echo "No button clicked.";
                    $conn->close();
                }
            }
            ?>


        </form>
    </div>
    <footer>
        <p>&copy; Distributed systems assignment.</p>
    </footer>
</body>

</html>