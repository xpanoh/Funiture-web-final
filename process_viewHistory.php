<?php
session_start();

function getTransactionHistory()
{
    global $success, $errorMsg;
    $email = $_SESSION['email'];
    $config = parse_ini_file('/var/www/private/db-config.ini');
    if (!$config) {
        $errorMsg = "Failed to read database config file.";
        $success = false;
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
            $success = false;
        } else { 
            $sql = $conn->prepare("SELECT price,dateOfPurchase,discountUsed FROM transactions where email=?");
            $sql->bind_param("s", $email);
            $sql->execute();
            $sql->store_result();
            
            $price = $dateOfPurchase = $discountUsed = null;
            $sql->bind_result($price, $dateOfPurchase, $discountUsed);

            while ($sql->fetch()) {
                $transactions[] = [
                    'price' => $price,
                    'date' => $dateOfPurchase,
                    'discountUsed' => $discountUsed
                ];
            }


            $conn->close();
            return $transactions;
        }
    }
}