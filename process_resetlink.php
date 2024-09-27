<?php session_start();
// Database connection variables
$config = parse_ini_file('/var/www/private/db-config.ini');
$conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);

// Check for database connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assume $token is retrieved from the query string
$token = $_GET['token'] ?? '';

// Prepare SQL statement to search for the token
$stmt = $conn->prepare("SELECT email FROM token WHERE token = ? LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Token found, fetch associated email and set session variable
    $row = $result->fetch_assoc();
    $_SESSION['email'] = $row['email'];

    // Prepare to delete the token
    $stmt = $conn->prepare("DELETE FROM token WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Redirect to resetpassword.php
    header('Location: resetpassword.php');
    exit();
} else {
    // Token not found, close statement and connection
    $stmt->close();
    $conn->close();

    // Output JavaScript to alert user and potentially redirect
    echo '<script type="text/javascript">';
    echo 'alert("Page not found.");';
    echo 'window.location.href = "home.php";'; // Redirect to a safe landing page
    echo '</script>';
    exit();
} ?>