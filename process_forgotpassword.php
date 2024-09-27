<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
session_start();
require 'vendor/autoload.php';

// Function to fetch member details or return an error if email not found
function fetchMemberDetails($email, $token) {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    // Initialize an array to hold the response, including a success flag and message
    $response = [
        'success' => false,
        'message' => 'Email not found in database.',
        'fname' => '',
        'lname' => ''
    ];
    
    if (!$config) {
        $response['message'] = 'Database configuration error.';
        return $response;
    }
    
    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $response['message'] = 'Database connection failed: ' . $conn->connect_error;
        return $response;
    }
    
    $stmt = $conn->prepare("SELECT fname, lname FROM members WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $response['fname'] = $row['fname'];
            $response['lname'] = $row['lname'];
            $response['success'] = true;
            $response['message'] = 'Member found.';
            
        
        // Prepare a new statement for inserting the token
        $stmt->close(); // Close the previous statement
        $stmt = $conn->prepare("INSERT INTO token (email, token) VALUES (?, ?)");
        $stmt->bind_param("ss", $_POST["email"], $token);
        if (!$stmt->execute()) {
            $response['message'] .= " Token insertion failed: (" . $stmt->errno . ") " . $stmt->error;
        }
    } else {
        $response['message'] = 'Email not found in database.';
    }

    // Clean up
    $stmt->close();
    $conn->close();

    return $response;
}}
// Function to fetch member details or return an error if email not found

$mail = new PHPMailer(true);

try {
    
    $email = $_POST["email"];
    $token = bin2hex(random_bytes(32));
    $memberDetails = fetchMemberDetails($email, $token);

    if (!$memberDetails['success']) {
        // Handle the error, such as by displaying a message to the user
        $_SESSION['error_msg'] = $memberDetails['message'];
        header('Location: forgotpassword.php'); 
        exit();
    }
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'adm1homehaven@gmail.com';                     //SMTP username
        $mail->Password   = 'fefyxdswnlekiitw';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    // Set up PHPMailer with the successful fetch
    $mail->setFrom('adm1homehaven@gmail.com', 'Home Haven');
    $mail->clearAddresses(); 
    $mail->addAddress($email, $memberDetails['fname'] . ' ' . $memberDetails['lname']); 

    $msg = 'A password reset attempt has been made for this email. <br>Password reset link <a href="35.212.197.178/process_resetlink.php?token='.$token.'">here</a>. <br> Do not share this link with anyone as it can only be used once.';
    $mail->isHTML(true);
    $mail->Subject = 'Reset Password';
    $mail->Body = $msg;

    $mail->send();
    $_SESSION['email'] = $email;
    echo '<script>alert("Reset password link sent to '. $_SESSION["email"] . '! Please check all your email inboxes.");window.location.href = "login.php";</script>';
    exit();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
}?>
