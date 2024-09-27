<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <!-- Linking the admin.css stylesheet -->
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<?php
// Include your database connection directly in the script as you preferred

error_reporting(E_ALL);
ini_set('display_errors', 1);

$configFilePath = '/var/www/private/db-config.ini';
$config = parse_ini_file($configFilePath);
if (!$config) {
    die("Failed to read database config file from '$configFilePath'.");
}

$conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$itemId = $_GET['itemId'] ?? '';
$tableName = $_GET['tableName'] ?? '';

$allowedTables = ['chairs', 'coffeeTables', 'diningTables', 'sofas'];
if (!in_array($tableName, $allowedTables)) {
    die('Invalid table name.');
}

$idColumnNames = [
    'chairs' => 'chairID',
    'coffeeTables' => 'idcoffeeTablesID',
    'diningTables' => 'diningTableID',
    'sofas' => 'sofaID',
];
$idColumnName = $idColumnNames[$tableName] ?? 'id';

$sql = "SELECT * FROM `$tableName` WHERE `$idColumnName` = ?";
$idColumnName = $idColumnNames[$tableName] ?? 'id';
$sql = "SELECT * FROM `$tableName` WHERE `$idColumnName` = ?";
error_log("Executing SQL: $sql with ID: $itemId"); // Add this line for debugging
$stmt = $conn->prepare($sql);

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $itemId);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    echo "Item not found.";
    exit;
}
?>

<h2>Edit Item</h2>
<form action="updateItem.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="itemId" value="<?php echo htmlspecialchars($itemId); ?>">
    <input type="hidden" name="tableName" value="<?php echo htmlspecialchars($tableName); ?>">
    
    <?php foreach ($item as $key => $value): ?>
        <?php if (strpos($key, 'Image') !== false): ?>
            <label for="<?php echo htmlspecialchars($key); ?>">Current Image:</label><br>
            <img src="<?php echo htmlspecialchars($value); ?>" alt="Item Image" style="width:100px;"><br>
            <input type="file" name="<?php echo htmlspecialchars($key); ?>" id="<?php echo htmlspecialchars($key); ?>"><br>
        <?php else: ?>
            <label for="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars(ucfirst($key)); ?>:</label>
            <input type="text" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>" id="<?php echo htmlspecialchars($key); ?>"><br>
        <?php endif; ?>
    <?php endforeach; ?>
    
    <input type="submit" name="submit" value="Update">
</form>

</body>
</html>
