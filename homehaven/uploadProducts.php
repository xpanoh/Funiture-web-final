<?php
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define table column mappings
$tableColumnMappings = [
    'chairs' => [
        'itemName' => 'chairName',
        'itemDescription' => 'chairDescription',
        'itemStock' => 'chairStock',
        'itemPrice' => 'chairPrice',
        'itemImage' => 'chairImagePath'
    ],
    'coffeeTables' => [
        'itemName' => 'coffeeTableName',
        'itemDescription' => 'coffeeTableDescription',
        'itemStock' => 'coffeeTableStock',
        'itemPrice' => 'coffeeTablePrice',
        'itemImage' => 'coffeeTableImagePath'
    ], 'diningTables' => [
        'itemName' => 'diningTableName',
        'itemDescription' => 'diningTableDescription',
        'itemStock' => 'diningTableStock',
        'itemPrice' => 'diningTablePrice',
        'itemImage' => 'diningTableImagePath'
    ],
    'sofas' => [
        'itemName' => 'sofaName',
        'itemDescription' => 'sofaDescription',
        'itemStock' => 'sofaStock',
        'itemPrice' => 'sofaPrice',
        'itemImage' => 'sofaImagePath'
    ]
    // Add other tables as needed
];

// Database connection using PDO
try {
    $config = parse_ini_file('/var/www/private/db-config.ini');
    $pdo = new PDO("mysql:host={$config['servername']};dbname={$config['dbname']};charset=utf8mb4", $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: Failed to connect to the database. " . $e->getMessage();
    exit;
}

// Validate table name from the form submission
$tableName = $_POST['tableName'] ?? '';
if (!array_key_exists($tableName, $tableColumnMappings)) {
    echo "Error: Invalid table name.";
    exit;
}

// Process file upload and Excel file
if (isset($_POST['uploadExcel']) && $_FILES['excelFile']['error'] == 0) {
    // Sanitize file name and validate file type
    $filename = basename($_FILES['excelFile']['name']);
    $filename = preg_replace('/[^A-Za-z0-9_.-]/', '_', $filename);
    $targetPath = 'uploads/' . $filename;
    
    $allowedExtensions = ['xlsx'];
    $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
    if (!in_array($fileExtension, $allowedExtensions)) {
        echo "Error: Invalid file extension. Please upload an Excel file.";
        exit;
    }

    if (!move_uploaded_file($_FILES['excelFile']['tmp_name'], $targetPath)) {
        echo "Error: There was an error uploading the file.";
        exit;
    }

    // Read the Excel file
    try {
        $reader = new Xlsx();
        $spreadsheet = $reader->load($targetPath);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
    } catch (Exception $e) {
        echo "Error reading Excel file: " . $e->getMessage();
        exit;
    }

    // Process each row of the Excel file
    foreach ($sheetData as $rowIndex => $row) {
        if ($rowIndex > 0) {  // Skip header row
            $itemName = $row[1] ?? '';
            $itemImage = $row[2] ?? '';
            $itemDescription = (string)($row[3] ?? '');
            $itemStock = (int)($row[4] ?? 0);
            $itemPrice = (float)($row[5] ?? 0.0);

            // Construct SQL query dynamically based on table name
            $sql = "INSERT INTO {$tableName} ({$tableColumnMappings[$tableName]['itemName']}, {$tableColumnMappings[$tableName]['itemImage']}, {$tableColumnMappings[$tableName]['itemDescription']}, {$tableColumnMappings[$tableName]['itemStock']}, {$tableColumnMappings[$tableName]['itemPrice']}) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$itemName, $itemImage, $itemDescription, $itemStock, $itemPrice]);
        }
    }

    // Redirect after successful upload and processing
    $redirectUrl = "http://18.217.214.64/homehaven/admin.php?tableName=" . urlencode($tableName);
    header("Location: " . $redirectUrl);
    exit;
} else {
    echo "Error: File upload failed.";
}
?>
