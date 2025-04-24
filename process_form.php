<?php
// Start session to store status messages
session_start();

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

//Required files
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Check if form was submitted
if (isset($_POST["send"])) {
    // Form validation
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['form_status'] = 'error';
        $_SESSION['error_msg'] = 'Invalid email format';
        header('Location: contactus.php');
        exit;
    }
    
    // Check required fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $_SESSION['form_status'] = 'error';
        $_SESSION['error_msg'] = 'All fields are required';
        header('Location: contactus.php');
        exit;
    }
    
    try {
        $mail = new PHPMailer(true);
        
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'morillojefferson24@gmail.com'; // Your Gmail
        $mail->Password = 'tpmapqttnysuthds'; // Your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use constant instead of string
        $mail->Port = 465;
        
        // Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress('morillojefferson24@gmail.com'); // Recipient address
        $mail->addReplyTo($email, $name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        
        // Create a more structured email body
        $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <h2>New Contact Form Submission</h2>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Subject:</strong> {$subject}</p>
            <h3>Message:</h3>
            <p>" . nl2br($message) . "</p>
        </body>
        </html>";
        
        // Plain text alternative
        $mail->AltBody = "New message from: {$name}\nEmail: {$email}\nSubject: {$subject}\n\nMessage:\n{$message}";
        
        // Send email
        $mail->send();
        
        // Set success message in session
        $_SESSION['form_status'] = 'success';
        
        // Redirect back to the form
        header('Location: contactus.php');
        exit;
        
    } catch (Exception $e) {
        // Log the error
        error_log("Mailer Error: " . $mail->ErrorInfo);
        
        // Set error message in session
        $_SESSION['form_status'] = 'error';
        $_SESSION['error_msg'] = 'Message could not be sent. Please try again later.';
        
        // Redirect back to the form
        header('Location: contactus.php');
        exit;
    }
} else {
    // If someone tries to access this file directly without submitting the form
    header('Location: contactus.php');
    exit;
}
?>