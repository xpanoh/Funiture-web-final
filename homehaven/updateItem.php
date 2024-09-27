<?php
// Enable error reporting for debugging - remember to disable in production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration file path
$configFilePath = '/var/www/private/db-config.ini';

// Parse the database configuration file
$config = parse_ini_file($configFilePath);
if (!$config) {
    die("Failed to read database config file from '$configFilePath'.");
}

// Create a database connection
$conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve and validate form data
$itemId = isset($_POST['itemId']) ? intval($_POST['itemId']) : 0;
$tableName = $_POST['tableName'] ?? '';

// Security check for tableName
$allowedTables = ['chairs', 'coffeeTable', 'diningTables', 'sofas'];
if (!in_array($tableName, $allowedTables)) {
    die('Invalid table name provided.');
}

// Mapping of table names to their ID column names
$idColumnNames = [
    'chairs' => 'chairID',
    'coffeeTable' => 'idcoffeeTablesID',
    'diningTables' => 'diningTableID',
    'sofas' => 'sofaID',
];

// Determine the correct ID column name for the current table
$idColumnName = $idColumnNames[$tableName] ?? null;
if (null === $idColumnName) {
    die("Invalid table name or missing ID column mapping for: $tableName");
}

// Initialize SQL parts
$updateParts = [];
$params = []; // Parameters for binding
$types = ''; // Types string for binding

// Process form fields for the update statement, excluding itemId, tableName, and submit
foreach ($_POST as $key => $value) {
    if ($key === 'itemId' || $key === 'tableName' || $key === 'submit') continue;

    $updateParts[] = "`$key` = ?";
    $params[] = $value;
    $types .= 's'; // Assuming all fields are strings for simplicity
}

// Handle file uploads
foreach ($_FILES as $key => $file) {
    if ($file['error'] === UPLOAD_ERR_OK) {
        $targetDirectory = "../images/";
        $targetFile = $targetDirectory . basename($file["name"]);
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            // Update SQL part and parameters for the file path
            $updateParts[] = "`$key` = ?";
            $params[] = $targetFile;
            $types .= 's';
        }
    }
}

// Add $itemId to the parameters list at the end for WHERE clause
$params[] = $itemId;
$types .= 'i'; // Append 'i' for the integer type of itemId

// Combine SQL parts to form the full update statement
$sql = "UPDATE `$tableName` SET " . join(', ', $updateParts) . " WHERE `$idColumnName` = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

// Prepare parameters for bind_param, which requires references
$refArray = array_merge(array($types), $params);
for ($i = 0; $i < count($refArray); $i++) {
    $refArray[$i] = &$refArray[$i];
}

// Dynamically bind parameters
call_user_func_array(array($stmt, 'bind_param'), $refArray);

// Execute the update
if ($stmt->execute()) {
    echo "Item updated successfully.<br>";

    // Optional: Redirect to prevent form resubmission
     header("Location: http://18.217.214.64////homehaven/admin.php");
     exit();
} else {
    echo "Error updating item: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
