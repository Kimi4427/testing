<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Define your secret key
$secretKey = '0x4AAAAAABA5K2Vx9iNo8rJ_lTvltZNNvGQ';

// Get response token from the form
$responseToken = $_POST['cf-turnstile-response'];

// Get user's IP address
$remoteIP = $_SERVER['REMOTE_ADDR'];

// Prepare data for the POST request
$data = array(
    'secret' => $secretKey,
    'response' => $responseToken,
    'remoteip' => $remoteIP
);

// Initialize curl session
$ch = curl_init();

// Set curl options
curl_setopt($ch, CURLOPT_URL, 'https://challenges.cloudflare.com/turnstile/v0/siteverify');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the POST request
$response = curl_exec($ch);

// Close curl session
curl_close($ch);

// Decode JSON response
$result = json_decode($response, true);

// Check if verification was successful
if ($result && isset($result['success']) && $result['success'] === true) {
    // Verification successful, proceed with sending email
    
    // Instantiate PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'SMTP server address'; // Enter SMTP server address
        $mail->SMTPAuth = true;
        $mail->Username = 'SMTP username'; // SMTP authentication username
        $mail->Password = 'SMTP password'; // SMTP authentication password
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';

        // Set sender (From) with email address and name
        $mail->setFrom('Sender email address', 'Subject');
        $mail->addAddress('Recipient email address'); // Add recipient
        // Content
        $mail->isHTML(false);
        $mail->Subject = 'Form submission';
        $mail->Body    = "Name: ".$_POST['name']."\n"."Email: ".$_POST['email']."\n"."Message: ".$_POST['message'];

        $mail->send();
     
        // Here you can choose between redirection or a simple alert

        //  header("Location: confirmation.html");
        echo '<script>';
        echo 'alert("Email sent successfully");';
        echo '</script>';
    } catch (Exception $e) {
        echo '<script>';
        echo 'alert("Captcha verification successful, but email could not be sent. Please try again.");';
        echo '</script>';
    }
} else {
    // Verification failed, handle error
    echo '<script>';
    echo 'alert("Captcha verification failed. Please try again.");';
    echo '</script>';
}
?>
