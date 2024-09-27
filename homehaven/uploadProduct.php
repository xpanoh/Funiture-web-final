<?php
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

// Include database configuration and connection setup
$config = parse_ini_file('/var/www/private/db-config.ini');
$conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['uploadExcel']) && $_FILES['excelFile']['error'] == 0) {
    $allowedFileType = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    
    // Check file type
    if (in_array($_FILES['excelFile']['type'], $allowedFileType)) {
        $targetPath = 'uploads/' . $_FILES['excelFile']['name'];
        if (move_uploaded_file($_FILES['excelFile']['tmp_name'], $targetPath)) {
            $reader = new Xlsx();
            $spreadsheet = $reader->load($targetPath);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();
            $tableName = 'YourTableName'; // Specify your table name here
            
            foreach ($sheetData as $rowIndex => $row) {
                if ($rowIndex > 0) { // Assuming the first row contains column headers
                    // Replace these with the actual column names and cell indexes from your Excel file
                    $itemName = $row[0];
                    $itemDescription = $row[1];
                    $itemStock = $row[2];
                    $itemPrice = $row[3];
                    // Construct your INSERT query here
                    $sql = "INSERT INTO $tableName (itemName, itemDescription, itemStock, itemPrice) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssii", $itemName, $itemDescription, $itemStock, $itemPrice);
                    
                    if (!$stmt->execute()) {
                        echo "Failed to insert row at index $rowIndex: " . $stmt->error;
                    }
                }
            }
            echo "File uploaded and data imported successfully.";
        } else {
            echo "There was an error uploading the file.";
        }
    } else {
        echo "Invalid file type. Please upload an Excel file.";
    }
} else {
    echo "Error in file upload.";
}
?>