<?php
include 'includes/config.php';
include 'includes/auth.php';

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
$error = '';

if (isset($_GET['sent']) && $_GET['sent'] == '1') {
    $message = "Your message has been sent successfully! We'll get back to you soon.";
}

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $auth = new Auth($pdo);
    $currentUser = $auth->getCurrentUser();
    $isLoggedIn = $auth->isLoggedIn();
    
    if (!$isLoggedIn) {
        header("Location: login.php");
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $messageContent = trim($_POST['message'] ?? '');
        
        if (empty($name) || empty($email) || empty($subject) || empty($messageContent)) {
            $error = "All fields are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } elseif (strlen($subject) > 100) {
            $error = "Subject must be 100 characters or less.";
        } else {
            $adminEmail = "viethung6002@gmail.com";
            
            $emailSubject = "Q&A System Contact: " . $subject;
            $emailBody = "New contact message from Student Q&A System\n\n";
            $emailBody .= "From: " . $name . "\n";
            $emailBody .= "Email: " . $email . "\n";
            $emailBody .= "Subject: " . $subject . "\n";
            $emailBody .= "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";
            $emailBody .= "Message:\n" . $messageContent . "\n\n";
            $emailBody .= "---\n";
            $emailBody .= "This message was sent via the Student Q&A System contact form.";
            
            $mail = new PHPMailer(true);
            
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'viethung6002@gmail.com';
                $mail->Password   = 'opnbyfsdngrtlcqz';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('viethung6002@gmail.com', 'COMP1841 Q&A System');
                $mail->addAddress($adminEmail);
                $mail->addReplyTo($email, $name);

                $mail->isHTML(false);
                $mail->Subject = $emailSubject;
                $mail->Body    = $emailBody;

                $mail->send();
                
                header('Location: contact.php?sent=1');
                exit;
            } catch (Exception $e) {
                $error = "Failed to send your message. Please try again later.";
            }
        }
    }
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

include './templates/contact.html.php';
?>