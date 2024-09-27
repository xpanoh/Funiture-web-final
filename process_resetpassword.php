
<?php include "inc/head.inc.php";            
            include "inc/header.inc.php";
            include "inc/nav.inc.php";
            session_start();
             ?>


<?php
    // Helper function to sanitize input
    function sanitize_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Helper function to write the member data to the database
    function updatePassToDB($email,  $pwd_hashed, &$errorMsg) {
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
                $stmt = $conn->prepare("UPDATE members SET password=? WHERE email = ?");
                // Bind & execute the query statement
                $stmt->bind_param("ss", $pwd_hashed, $email);
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

    $email= $pwd = $pwd_confirm =$pwd_hashed = $errorMsg = "";
$success = true;


    if ($_POST["pwd"] != $_POST["pwd_confirm"]) {
        $errorMsg .= "Passwords are not the same.<br>";
        $success = false;
    } else {
        // $pwd = password_hash($_POST["pwd"], PASSWORD_DEFAULT);
        $regex = '/^(?=.*[A-Z])(?=.*[!@#$&*]).{8,}$/';

    // Check if the password matches the strength requirements
    if (preg_match($regex, $_POST["pwd"])) {
        $pwd = password_hash($_POST["pwd"], PASSWORD_DEFAULT);
    } else {
        $errorMsg .= "Password does not meet strength requirements. Password needs to have 8 characters minimally, with it consisting of at least 1 special character and 1 uppercase letter.";
        $success = false;
    }
        
}

    // Similar checks and assignments for other form fields...

    if ($success) {
        $email = $_SESSION["email"];
        $pwd = sanitize_input($_POST["pwd"]);
        $pwd_hashed = password_hash($pwd, PASSWORD_DEFAULT);
        if (updatePassToDB($email, $pwd_hashed, $errorMsg)) {
            echo '<script>';
            echo 'window.location.href = "login.php";';
            echo 'alert("Password reset successfully!");';
            echo '</script>';
                exit();
            //echo 'Message has been sent';
        
        } else {
            $_SESSION['error_msg'] = $errorMsg;
            header('Location: resetpassword.php'); 
                exit();
        }
    } else {
        $_SESSION['error_msg'] = $errorMsg;
        header('Location: resetpassword.php'); 
            exit();
    }

?>
