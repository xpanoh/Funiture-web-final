<?php
session_start();

// Check if the user is not an admin, redirect them to another page
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    header("Location: home.php"); // Redirect to another page
    exit(); // Stop further execution
}
?>
<?php
ob_start();
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php-error.log');
set_time_limit(300); // 5 minutes
$config = parse_ini_file('/var/www/private/db-config.ini');
    if (!$config) {
        echo "Failed to read database config file.";
        return;
    }

    $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        echo "Connection failed: " . $conn->connect_error;
        return;
    }
    $tableName = $conn->real_escape_string(isset($_GET['tableName']) ? $_GET['tableName'] : 'chairs');


// Fetch data from database
// Fetch data from database
$sql = "SELECT * FROM `$tableName`";
$result = $conn->query($sql);

// Check if query was successful
if ($result) {
    // Attempt to fetch the first row as an associative array
    $firstRow = $result->fetch_assoc();

    // Check if $firstRow is actually an array
    if (is_array($firstRow)) {
        // Proceed with creating your spreadsheet

        // Create a new spreadsheet and set properties
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Table Data');

        // Use $firstRow to extract column names
        $columnNames = array_keys($firstRow);

        // Set column names as the first row in the sheet
        $column = 'A';
        foreach ($columnNames as $columnName) {
            $sheet->setCellValue($column . '1', $columnName);
            $column++;
        }

        // Reset result pointer to iterate over the results again
        $result->data_seek(0);

        // Fill data in the sheet
        $rowCount = 2;
        while ($row = $result->fetch_assoc()) {
            $column = 'A';
            foreach ($row as $cellValue) {
                $sheet->setCellValue($column . $rowCount, $cellValue);
                $column++;
            }
            $rowCount++;
        }

        // Continue with file output as before...
    } else {
        // Handle the case where no data was returned
        die("No data returned from the database.");
    }
} else {
    // Handle query failure
    die("Failed to fetch data from database: " . $conn->error);
}

// Proceed to clear the output buffer and send the file to the client as before...
ob_end_clean(); // Discard the buffer without outputting it

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $tableName . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

?>
