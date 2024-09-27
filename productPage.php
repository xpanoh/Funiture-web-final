<?php
session_start();
include "inc/head.inc.php";
include "inc/header.inc.php";
include "inc/nav.inc.php";
include "inc/zendesk_widget.inc.php";

$name = isset($_GET['name']) ? $_GET['name'] : '';
$image = isset($_GET['image']) ? $_GET['image'] : '';
$description = isset($_GET['description']) ? $_GET['description'] : '';
$price = isset($_GET['price']) ? $_GET['price'] : '';
$stock = isset($_GET['stock']) ? $_GET['stock'] : '';
$productID = isset($_GET['productID']) ? $_GET['productID'] : '';
$productType = isset($_GET['productType']) ? $_GET['productType'] : '';

// Function to establish a database connection
function connectToDatabase()
{
    $config = parse_ini_file('/var/www/private/db-config.ini');
    if (!$config) {
        return false;
    } else {
        $conn = new mysqli(
            $config['servername'],
            $config['username'],
            $config['password'],
            $config['dbname']
        );
        if ($conn->connect_error) {
            return false;
        } else {
            return $conn;
        }
    }
}

// Check if the user has already reviewed the product
function hasUserReviewedProduct($email, $productName)
{
    $conn = connectToDatabase();
    if (!$conn) {
        return false;
    } else {
        $stmt = $conn->prepare("SELECT * FROM reviews WHERE email = ? AND productName = ?");
        $stmt->bind_param("ss", $email, $productName);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $conn->close();
        return $result->num_rows > 0;
    }
}

// Check if user has already reviewed the product
$userHasReviewed = isset($_SESSION['email']) ? hasUserReviewedProduct($_SESSION['email'], $name) : false;


// Helper function to sanitize input
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Helper function to write the review data to the database
function saveReviewToDB($name, $review, $rating, $fname, $email, &$errorMsg)
{
    $conn = connectToDatabase();
    if (!$conn) {
        $errorMsg = "Failed to connect to the database.";
        return false;
    } else {
        $stmt = $conn->prepare("INSERT INTO reviews (productName, review, ratings, fname, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $name, $review, $rating, $fname, $email);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            return false;
        }
        $stmt->close();
        $conn->close();
        return true;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form data
    $review = sanitize_input($_POST['review']);
    $rating = sanitize_input($_POST['rating']);

    // Retrieve user information from session
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
    $fname = isset($_SESSION['fname']) ? $_SESSION['fname'] : '';

    // Insert into database
    $errorMsg = "";
    if (saveReviewToDB($name, $review, $rating, $fname, $email, $errorMsg)) {
        $successMessage = "Review saved successfully.";
    } else {
        $errorMessage = "Error saving review: $errorMsg";
    }
}
?>

<main>

        <h1 style="text-align:center;">
            <?php echo $name; ?>
        </h1>
        
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>" class="product-image">
            </div>
            <div class="col-md-6">
                <div class="detail-box">
                    <p>Description:
                        <?php echo $description; ?>
                    </p>
                    <p>Price:
                        <?php echo $price; ?>
                    </p>
                    <p>Stock:
                        <?php echo $stock; ?>
                    </p>
                    <form action="addToCart.php" method="post">
                        <input type="hidden" name="name" id="name" value="<?php echo $name; ?>">
                        <input type="hidden" name="price" id="price" value="<?php echo $price; ?>">
                        <input type="hidden" name="stock" id="stock" value="<?php echo $stock; ?>">
                        <input type="hidden" name="desc" id="desc" value="<?php echo $description; ?>">
                        <input type="hidden" name="image" id="image" value="<?php echo $image; ?>">
                        <input type="hidden" name="product_type" id="product_type" value="<?php echo $productType; ?>">
                        <input type="submit" value="Add Item To Cart">
                    </form>
                    <?php if (isset($_SESSION['email'])): ?>
                        <!-- Review Form -->
                        <form method="post"
                            action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>#reviews-section">
                            <label for="review">Your Review:</label><br>
                            <textarea id="review" name="review" rows="4" cols="50"></textarea><br>
                            <label for="rating">Rating:</label>
                            <select id="rating" name="rating">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select><br><br>
                            <input type="submit" value="Submit Review">
                        </form>
                    <?php elseif ($userHasReviewed): ?>
                        <p>You have already reviewed this product.</p>
                    <?php endif; ?>
                    <?php if (isset($successMessage)): ?>
                        <p>
                            <?php echo $successMessage; ?>
                        </p>
                    <?php endif; ?>
                    <?php if (isset($errorMessage)): ?>
                        <p>
                           
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div id="reviews-section">
        <h2>Reviews</h2>
        <?php
        $conn = connectToDatabase();
        if ($conn) {
            $stmt = $conn->prepare("SELECT review, ratings, fname FROM reviews WHERE productName = ?");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='review'>";
                    echo "<p><strong>Review by: " . $row['fname'] . "</strong></p>";
                    echo "<p>Rating: " . $row['ratings'] . "</p>";
                    echo "<p>" . $row['review'] . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>There are no reviews for this product yet.</p>";
            }
            $stmt->close();
            $conn->close();
        } else {
            echo "Failed to connect to the database.";
        }
        ?>
    </div>
    </main>

<?php
include "inc/footer.inc.php";
?>