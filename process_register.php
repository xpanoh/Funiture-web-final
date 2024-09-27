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
 * Helper function to write the member data to the database
 */
function saveMemberToDB($fname, $lname, $email, $pwd_hashed, &$errorMsg)
{
    // Create database connection.
    $config = parse_ini_file('/var/www/private/db-config.ini');
    if (!$config) {
        $errorMsg = "Failed to read database config file.";
        return false;
    } else {
        $conn = new mysqli(
            $config['servername'],
            $config['username'],
            $config['password'],
            $config['dbname']
        );
        // Check connection
        if ($conn->connect_error) {
            $errorMsg = "Connection failed: " . $conn->connect_error;
            return false;
        } else {
            // Prepare the statement
            $stmt = $conn->prepare("INSERT INTO members (fname, lname, email, password) VALUES (?, ?, ?, ?)");
            // Bind & execute the query statement
            $stmt->bind_param("ssss", $fname, $lname, $email, $pwd_hashed);
            if (!$stmt->execute()) {
                $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                return false;
            }
            $stmt->close();
            $conn->close();
            return true;
        }
    }
}

$email = $fname = $lname = $pwd = $pwd_confirm = $pwd_hashed = $errorMsg = "";
$success = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validation and sanitization
    $fname = sanitize_input($_POST["fname"]);
    $lname = sanitize_input($_POST["lname"]);
    $email = sanitize_input($_POST["email"]);
    $pwd = sanitize_input($_POST["pwd"]);
    $pwd_confirm = sanitize_input($_POST["pwd_confirm"]);

    if ($pwd != $pwd_confirm) {
        $errorMsg .= "Passwords are not the same.<br>";
        $success = false;
    } else {
        $regex = '/^(?=.*[A-Z])(?=.*[!@#$&*]).{8,}$/';

        // Check if the password matches the strength requirements
        if (preg_match($regex, $pwd)) {
            $pwd_hashed = password_hash($pwd, PASSWORD_DEFAULT);
        } else {
            $errorMsg .= "Password does not meet strength requirements. Password needs to have 8 characters minimally, with it consisting of at least 1 special character and 1 uppercase letter.";
            $success = false;
        }
    }

    if ($success) {
        if (saveMemberToDB($fname, $lname, $email, $pwd_hashed, $errorMsg)) {
            echo '<script>';
            echo 'alert("Thanks for registering as a member!");';
            echo 'window.location.href = "login.php";'; // Redirect immediately
            echo '</script>';
            exit();
        } else {
            $_SESSION['error_msg'] = "An error occurred while saving to the database: " . $errorMsg;
            header('Location: register.php');
            exit();
        }
    } else {
        $_SESSION['error_msg'] = $errorMsg;
        header('Location: register.php');
        exit();
    }
    
    
}
?>
