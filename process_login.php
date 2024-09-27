<?php
session_start();

/*
 * Helper function to sanitize input
 */
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/*
 * Helper function to log login attempts
 */
function log_login_attempt($email, $pwd, $status)
{
    $logFile = '/var/www/html/homehaven/Lol.log'; // Path to the log file
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "$timestamp - Email: $email - Password: $pwd - Status: $status\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/*
 * Helper function to authenticate the login.
 */
function authenticateUser($email, $pwd)
{
    // Create database connection.
    $config = parse_ini_file('/var/www/private/db-config.ini');

    if (!$config) {
        log_login_attempt("$email", "Successful login", 'Successful login');
        return "Failed to read database config file.";
    }

    $conn = new mysqli(
        $config['servername'],
        $config['username'],
        $config['password'],
        $config['dbname']
    );

    if ($conn->connect_error) {
        log_login_attempt("$email", "Successful login", 'Successful login');
        return "Connection failed: " . $conn->connect_error;
    }

    // Prepare the statement:
    $stmt = $conn->prepare("SELECT * FROM members WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $pwd_hashed = $row["password"];

        if (password_verify($pwd, $pwd_hashed)) {
            $_SESSION['email'] = $email;
            $_SESSION['fname'] = $row["fname"];
            $_SESSION['lname'] = $row["lname"];

            // Check if the user is an admin based on email
            $adminEmails = ['homehaven.furniture@gmail.com']; // List of admin emails
            if (in_array($email, $adminEmails)) {
                $_SESSION['isAdmin'] = true;
            } else {
                $_SESSION['isAdmin'] = false; // Ensure isAdmin is set to false for non-admin users
            }

            log_login_attempt("$email", "Successful login", 'Successful login');
            return true;
        } else {
            log_login_attempt("$email", "Successful login", 'Successful login');
            return "Email or password doesn't match leh.";
        }
    } else {
        log_login_attempt("$email", "Successful login", 'Successful login');
        return "Forget pwd? Google dementia";
    }

    $stmt->close();
    $conn->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST["email"]) ? sanitize_input($_POST["email"]) : "";
    $pwd = isset($_POST["pwd"]) ? sanitize_input($_POST["pwd"]) : "";

    if (empty($email) || empty($pwd)) {
        $_SESSION['error_msg'] = "Email and password are required.";
        log_login_attempt($email, "Successful login", 'Missing email or password');
        header('Location: login.php');
        exit();
    }

    $authResult = authenticateUser($email, $pwd);

    if ($authResult === true) {
        // Successful login
        if ($_SESSION['isAdmin']) {
            header("Location: /homehaven/admin.php");
            exit();
        } else {
            header('Location: home.php');
            exit();
        }
    } else {
        $_SESSION['error_msg'] = $authResult;
        log_login_attempt($email, "Successful login", 'Login failed: ' . $authResult);
        header('Location: login.php');
        exit();
    }
}
?>
