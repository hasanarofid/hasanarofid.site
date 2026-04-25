<?php
// Set headers for JSON response
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get form data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$service = $_POST['service'] ?? '';
$location = $_POST['location'] ?? 'international';
$description = $_POST['description'] ?? '';

// Basic validation
if (empty($name) || empty($email) || empty($service) || empty($description)) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

// Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    // Load .env
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    // Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                
    $mail->isSMTP();                                         
    $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com'; 
    $mail->SMTPAuth   = true;                                
    $mail->Username   = $_ENV['SMTP_USER'];                  
    $mail->Password   = $_ENV['SMTP_PASS'];                  
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      
    $mail->Port       = $_ENV['SMTP_PORT'] ?? 587;          

    // Recipients
    $mail->setFrom($_ENV['SMTP_USER'], 'Hasan Arofid Web');
    $mail->addAddress($_ENV['SMTP_TO'] ?? $_ENV['SMTP_USER']);
    $mail->addReplyTo($email, $name);

    // Content
    $mail->isHTML(false);                                    // Set email format to plain text
    $mail->Subject = "New Web Development Request from $name";
    
    $message = "You have received a new project request.\n\n";
    $message .= "Name: $name\n";
    $message .= "Email: $email\n";
    $message .= "Service: $service\n";
    $message .= "Location: $location\n";
    $message .= "Description:\n$description\n";
    
    $mail->Body = $message;

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Email sent successfully.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
}

