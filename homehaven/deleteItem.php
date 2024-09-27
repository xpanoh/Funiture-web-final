

<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load database configuration
$config = parse_ini_file('/var/www/private/db-config.ini');
if (!$config) {
    die("Failed to read database config file.");
}

// Establish database connection
$conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate input
$itemId = isset($_GET['itemId']) ? intval($_GET['itemId']) : 0;
$tableName = isset($_GET['tableName']) ? $_GET['tableName'] : '';

// Define allowed tables to prevent SQL Injection
$allowedTables = ['chairs', 'coffeeTables', 'diningTables', 'sofas'];
if (!in_array($tableName, $allowedTables) || $itemId <= 0) {
    die('Invalid request.');
}

// Map table names to their ID column names
$idColumnNames = [
    'chairs' => 'chairID',
    'coffeeTables' => 'idcoffeeTablesID', // Ensure correct column name
    'diningTables' => 'diningTableID',
    'sofas' => 'sofaID',
];

$idColumnName = $idColumnNames[$tableName];

// Prepare the DELETE statement
$sql = "DELETE FROM `$tableName` WHERE `$idColumnName` = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

// Bind parameters and execute
$stmt->bind_param('i', $itemId);
if ($stmt->execute()) {
    // Redirect back to the admin page with a success message
    header("Location: admin.php?delete=success"); // Adjust the redirect location as needed
    exit();
} else {
    echo "Error deleting item: " . $stmt->error;
}

// Close connection
$stmt->close();
$conn->close();
?>
