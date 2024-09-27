<body>
    <?php include "inc/head.inc.php";            
            include "inc/header.inc.php";
            include "inc/nav.inc.php"; ?>


<?php
    // Helper function to sanitize input
    function sanitize_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Helper function to write the member data to the database
    function saveMemberToDB($fname, $lname, $email, &$errorMsg, $currentemail) {
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
                $stmt = $conn->prepare("UPDATE members SET fname = ?, lname = ?, email = ? WHERE email = ?");
                // Bind & execute the query statement
                $stmt->bind_param("ssss", $fname, $lname, $email, $currentemail);
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
    session_start();
    $email = $fname = $lname = $currentemail = $errorMsg = "";
$success = true;

    // Additional check to make sure e-mail address is well-formed.
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errorMsg .= "Invalid email format.<br> ";
        $success = false;
    }

    // Similar checks and assignments for other form fields...

    if ($success) {
        $fname = sanitize_input($_POST["fname"]);
        $lname = sanitize_input($_POST["lname"]);
        $email = sanitize_input($_POST["email"]);
        $currentemail = (string) $_SESSION['email'];
        if (saveMemberToDB($fname, $lname, $email, $errorMsg, $currentemail)) {
            $_SESSION['fname'] = $fname;
        $_SESSION['lname'] = $lname;
        $_SESSION['email'] = $email;
        // Redirect with a success message
        echo '<script>';
        echo 'window.location.href = "login.php";';
        echo 'alert("Details successfully changed.");';
        echo '</script>';
        header('Location: profile.php');
        exit();
    } else {
        echo "<br><div class=\"container\"><hr><h2>Oops!</h2><h4>An error occurred while saving to the database:</h4>";
        echo "<p>" . $errorMsg . "</p>";
    }
} else {
    $_SESSION['error_msg'] = $errorMsg;
    header('Location: profile.php'); 
    exit();
}

?>

<?php include "inc/footer.inc.php"; ?>
</body>
