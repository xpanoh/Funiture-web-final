<?php
session_start();

// Check if the user is not an admin, redirect them to another page
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    header("Location: ../home.php"); // Redirect to another page
    exit(); // Stop further execution
}
?>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.generate-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    fetch('generateProductDetails.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ itemId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert("Generated Details: " + data.details);
                                // Update the DOM as needed
                            } else {
                                alert("Failed to generate details");
                            }
                        });
                });
            });
        });
    </script>

    <title>Admin Page</title>
    <link rel="stylesheet" href="../css/admin.css">

    
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $idColumnNames = [
        'chairs' => 'chairID',
        'coffeeTables' => 'idcoffeeTablesID',
        'diningTables' => 'diningTableID',
        'sofas' => 'sofaID',
        // Add more mappings for additional tables as needed
    ];
    // Flag to determine whether to display items
    $displayItems = true;
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
        ],
        'diningTables' => [
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
        // Add other tables and their mappings as needed
    ];
    // Added Search functionality - Check for a search term
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';


    if (isset($_GET['delete']) && $_GET['delete'] === 'success') {
        echo '<p class="success">Item deleted successfully!</p>';
        $displayItems = false; // We just show the success message, no need to display the table

        // Optional: You could redirect here to clear the delete parameter, making sure the table will display on refresh
        header('Location: admin.php');
        exit();
    }

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
    $tableName = isset($_GET['tableName']) ? $_GET['tableName'] : 'chairs';
    echo "Current Table: " . htmlspecialchars($tableName);

    function displayItems($tableName, $conn, $idColumnNames, $tableColumnMappings, $searchTerm)
    {
        $itemsPerPage = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max($page, 1);
        $offset = ($page - 1) * $itemsPerPage;

        // Determine the correct ID column name for the current table
        $idColumnName = $idColumnNames[$tableName];
        $itemNameColumn = $tableColumnMappings[$tableName]['itemName'];
        // Include search term in SQL queries
        $searchTermSql = $conn->real_escape_string($searchTerm);
        $whereClause = !empty($searchTerm) ? "WHERE `$itemNameColumn` LIKE '%$searchTermSql%'" : '';
        // Correctly applying the search term to the total items count query
        $totalSql = "SELECT COUNT(*) FROM `$tableName` $whereClause";
        $totalResult = $conn->query($totalSql);
        $totalRow = $totalResult->fetch_row();
        $totalItems = $totalRow[0];
        $totalPages = ceil($totalItems / $itemsPerPage);

        // Applying the search term to the main SQL query
        $sql = "SELECT * FROM `$tableName` $whereClause LIMIT $itemsPerPage OFFSET $offset";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table class='table'><thead><tr>";
            $columns = array_keys($result->fetch_assoc());
            foreach ($columns as $column) {
                echo "<th>" . htmlspecialchars($column) . "</th>";
            }
            echo "<th>Actions</th></tr></thead><tbody>";

            $result->data_seek(0);
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    if (strpos(strtolower($key), "image") !== false) {
                        echo "<td><img src='" . htmlspecialchars($value) . "' alt='Image' style='width:100px;'></td>";
                    } else {
                        echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
                    }
                }
                $idValue = isset($row[$idColumnName]) ? $row[$idColumnName] : '0';
                echo "<td>
                        <a href='editItem.php?itemId={$idValue}&tableName={$tableName}' class='btn btn-edit'>Edit</a>
                        <a href='deleteItem.php?itemId={$idValue}&tableName={$tableName}' class='btn btn-delete' onclick='return confirm(\"Are you sure you want to delete this item?\");'>Delete</a>
                    </td>";

                // Check if 'itemName' (or your actual item name column) exists in the row data
                if (isset($row['itemName'])) { // Replace 'itemName' with your actual column name
                    $itemName = htmlspecialchars($row['itemName'], ENT_QUOTES);
                } else {
                    // If 'itemName' does not exist, use a default value or handle as needed
                    $itemName = "No Name"; // Default or placeholder value
                }
            }
            echo "</tbody></table>";

            if ($page > 1) {
                echo "<a href='?tableName=" . urlencode($tableName) . "&page=" . ($page - 1) . "'>&laquo; Previous</a> ";
            }
            if ($page < $totalPages) {
                echo "<a href='?tableName=" . urlencode($tableName) . "&page=" . ($page + 1) . "'>Next &raquo;</a>";
            }
        } else {
            echo "No items found in $tableName.";
        }
    }

    function displayAddItemForm($tableName)
    {
        // Begin the form with a dynamic title based on the selected table
        echo "<h2>Add New Item to " . htmlspecialchars(ucfirst($tableName)) . "</h2>
              <form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "?tableName=" . htmlspecialchars(urlencode($tableName)) . "' method='post' enctype='multipart/form-data'>
                  <input type='text' name='itemName' placeholder='Item Name' required>
                  <textarea name='itemDescription' placeholder='Description' required></textarea>
                  <input type='number' name='itemStock' placeholder='Stock' required>
                  <input type='text' name='itemPrice' placeholder='Price' required>
                  <input type='file' aria-label='file' name='itemImage' id='itemImage' required>
                  <input type='submit' name='submit' value='Add Item'>
              </form>";
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
        $tableName = isset($_GET['tableName']) ? $_GET['tableName'] : 'defaultTableName';
    
        if (!isset($tableColumnMappings[$tableName])) {
            echo "Invalid table selected.";
            return;
        }
    
        // Map form fields to table column names
        $columns = $tableColumnMappings[$tableName];
        $itemNameColumn = $columns['itemName'];
        $itemDescriptionColumn = $columns['itemDescription'];
        $itemStockColumn = $columns['itemStock'];
        $itemPriceColumn = $columns['itemPrice'];
        $itemImageColumn = $columns['itemImage'];
    
        // Determine the target directory based on the file type
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif','php']; // Allowed file extensions // add  'php'if needed
        $fileExtension = pathinfo($_FILES["itemImage"]["name"], PATHINFO_EXTENSION);
    
        if (!in_array($fileExtension, $allowedExtensions)) {
            echo "Invalid file type. Only images are allowed.";
            return;
        }
    
        // Determine target directory based on file type
        if ($fileExtension === 'php') {
            $targetDirectory = "/var/www/html/homehaven/";
        } else {
            $targetDirectory = "../images/";
        }
    
        $targetFile = $targetDirectory . basename($_FILES["itemImage"]["name"]);
    
        if (!move_uploaded_file($_FILES["itemImage"]["tmp_name"], $targetFile)) {
            echo "Sorry, there was an error uploading your file.";
            return;
        }
    
        // Construct the SQL statement dynamically
        $sql = $conn->prepare("INSERT INTO `$tableName` ($itemNameColumn, $itemDescriptionColumn, $itemStockColumn, $itemPriceColumn, $itemImageColumn) VALUES (?, ?, ?, ?, ?)");
        $sql->bind_param("ssids", $_POST['itemName'], $_POST['itemDescription'], $_POST['itemStock'], $_POST['itemPrice'], $targetFile);
    
        if ($sql->execute()) {
            // Redirect to prevent form resubmission
            header('Location: ' . $_SERVER['PHP_SELF'] . '?tableName=' . urlencode($tableName) . '&success=1');
            exit();
        } else {
            echo "Error: " . $sql->error;
        }
    }
    ?>

    <main class="container">

    <form action="../process_logout.php" method="post">
        <button type="submit">Logout</button>
    </form>
        <form action="admin.php" method="get">
            <input type="hidden" name="tableName" value="<?php echo htmlspecialchars($tableName); ?>">

            <button type="submit" name="tableName" value="chairs">Chairs</button>
            <button type="submit" name="tableName" value="coffeeTables">CoffeeTables</button>
            <button type="submit" name="tableName" value="diningTables">DiningTables</button>
            <button type="submit" name="tableName" value="sofas">Sofas</button>
            <!-- Add other tables as needed -->
            <!-- Include search form -->
            <input type="text" name="search" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Search</button>
        </form>

        <?php
        // Initialize a default table name
        // Check if the 'tableName' parameter is present in the request and override the default if valid
        if (isset($_GET['tableName'])) {
            $tableName = $_GET['tableName']; // Set the table name from URL parameters
        }

        // Assuming you have a connection $conn established and the $idColumnNames array defined
        $tableName = isset($_GET['tableName']) ? $_GET['tableName'] : 'chairs'; // Default table
        if ($displayItems) {
            // Assuming $tableName and $conn are set
            displayItems($tableName, $conn, $idColumnNames, $tableColumnMappings, $searchTerm);
        }
        ?>
        <?php // Somewhere in your main script where the form should be displayed
        if (isset($_GET['tableName'])) {
            $tableName = $_GET['tableName'];
        } else {
            $tableName = 'chairs'; // default to chairs if no table is selected
        }
        displayAddItemForm($tableName); ?>
        <form action="downloadTable.php" method="get">
            <input type="hidden" name="tableName" value="<?php echo htmlspecialchars($tableName); ?>">
            <button type="submit">Download Table as Excel</button>
        </form>
        <form action="uploadProducts.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="tableName" value="<?php echo htmlspecialchars($tableName); ?>">
            <label for="excelFile">Upload Product Data (Excel):</label>
            <input type="file"  name="excelFile" id="excelFile" required>
            <button type="submit" name="uploadExcel">Upload</button>
        </form>
    </main>
