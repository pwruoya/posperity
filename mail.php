<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer autoload file
require 'vendor/autoload.php'; // Adjust the path as needed

// Function to send email
function sendEmail($to_email, $subject, $body, $from_email)
{
    // Create a PHPMailer instance
    $mail = new PHPMailer(true); // True enables exceptions

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];
    try {
        // SMTP settings
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'keterdummy@gmail.com'; // SMTP username (your email address)
        $mail->Password = 'lrvq wsuc bihl garm'; // SMTP password
        $mail->SMTPSecure = 'tls'; // Enable TLS encryption
        $mail->Port = 587; // TCP port to connect to

        // Sender and recipient details
        $mail->setFrom($from_email, "Posperity team");
        $mail->addAddress($to_email);

        // Email content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $body;


        // $mail->SMTPDebug = 2; // Enable verbose SMTP debugging
        // $mail->Debugoutput = 'html'; // Set debug output format to HTML

        // Send email
        $mail->send();
        return true; // Email sent successfully
    } catch (Exception $e) {
        echo $e;
        return false; // Email sending failed
    }
}
function generateResetToken($length = 32)
{
    // Define characters that can be used in the token
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    // Get the number of characters in the string
    $charLength = strlen($characters);

    // Initialize an empty token string
    $token = '';

    // Generate random characters to create the token
    for ($i = 0; $i < $length; $i++) {
        $token .= $characters[rand(0, $charLength - 1)];
    }

    // Return the generated token
    return $token;
}
