<body>
    <?php include "inc/head.inc.php"; 
    include "inc/header.inc.php";
    include "inc/nav.inc.php";
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
    function saveContactFormToDB($name, $phone, $email, $message, &$errorMsg) {
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
                $stmt = $conn->prepare("INSERT INTO contactUs (name, phone, email, message) VALUES (?, ?, ?, ?)");
                // Bind & execute the query statement
                $stmt->bind_param("ssss", $name, $phone, $email, $message);
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

    $name = $phone = $email = $message = $errorMsg = "";
    $success = true;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["name"])) {
            $errorMsg .= "Name is required.<br>";
            $success = false;
        } else {
            $name = sanitize_input($_POST["name"]);
        }

        if (empty($_POST["phone"])) {
            $errorMsg .= "Phone number is required.<br>";
            $success = false;
        } else {
            $phone = sanitize_input($_POST["phone"]);
            // Additional check to validate phone number format.
            if (!preg_match("/^\d{8}$/", $phone)) {
                $errorMsg .= "Invalid phone number format. Please enter a 8-digit number.<br>";
                $success = false;
            }
        }        

        if (empty($_POST["email"])) {
            $errorMsg .= "Email is required.<br>";
            $success = false;
        } else {
            $email = sanitize_input($_POST["email"]);
            // Additional check to make sure e-mail address is well-formed.
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorMsg .= "Invalid email format.<br>";
                $success = false;
            }
        }

        if (empty($_POST["message"])) {
            $errorMsg .= "Message is required.<br>";
            $success = false;
        } else {
            $message = sanitize_input($_POST["message"]);
        }

        if ($success) {
            $name = sanitize_input($_POST["name"]);
            $phone = sanitize_input($_POST["phone"]);
            $email = sanitize_input($_POST["email"]);
            $message = sanitize_input($_POST["message"]);
            if (saveContactFormToDB($name, $phone, $email, $message, $errorMsg)) {
                echo "<br><div class=\"container\"><hr><h3>Thank you for contacting us! We will get back to you soon.</h3>";
                //echo "<h4>Thank you for signing up, $fname $lname.</h4>";
            } else {
                echo "<br><div class=\"container\"><hr><h2>Oops!</h2><h4>An error occurred while saving to the database:</h4>";
                echo "<p>" . $errorMsg . "</p>";
                //echo '<button class="btn btn-primary" onclick="window.location.href=\'register.php\'">Return to Sign Up</button></div><br>';
            }
        } else {
            echo "<br><div class=\"container\"><hr><h2>Oops!</h2><h4>The following input errors were detected:</h4>";
            echo "<p>" . $errorMsg . "</p>";
            //echo '<button class="btn btn-primary" onclick="window.location.href=\'register.php\'">Return to Sign Up</button></div><br>';
        }
    }
?>

    <?php include "inc/footer.inc.php"; ?>
</body>
