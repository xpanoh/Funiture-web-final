<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect user to login page if not logged in
    header("Location: login.php");
    exit;
}
$fname = $_SESSION['fname'];
$lname = $_SESSION['lname'];
$email = $_SESSION['email'];

// Include any necessary header, navigation, or footer files
include "inc/head.inc.php";
include "inc/header.inc.php";
include "inc/nav.inc.php";
?>
<link rel="stylesheet" href="css/login.css">
<?php 
if (substr($_SESSION["email"], 0, 5) === 'admin'){
?>

<main class="container">
    <form action="process_promocode.php" method="POST">
    <div class="headingsContainer">
        <h3>Edit Profile details</h3>
    </div>
    <div class="mainContainer mb-3">

        <label for="fname">First Name</label>
        <input type="text" placeholder="Enter First Name" name="fname" id="fname" value="<?php echo htmlspecialchars($fname); ?>" disabled>
        <br>
        <label for="lname">Last Name</label>
        <input type="text" placeholder="Enter Last Name" name="lname" id="lname" value="<?php echo htmlspecialchars($lname); ?>" disabled>
        <br>
        <label for="email">Email</label>
        <input type="text" placeholder="Enter Email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" disabled>
        <br>
        <label for="email">Promo codes</label>
        <input type="text" placeholder="Enter new Promo code" name="promocode" id="promocode" required>
        <br>
        <label for="email">Promo code Discount (%)</label>
        <input type="number" placeholder="Enter Promo code discount" name="promocodedisc" id="promocodedisc" min="1" style="width: 100%;
    margin: 10px 0;
    border-radius: 5px;
    padding: 15px 18px;
    box-sizing: border-box;" required>
        <br>
    </div>
    <div class="mb-3">
<button type="submit">Add new Promo Code</button>
</div>
    <?php 
    if (isset($_SESSION['error_msg'])) {
        echo '<p>' . $_SESSION['error_msg'] . '</p>';
        unset($_SESSION['error_msg']); // Clear the message after displaying
    }

?> 
 <?php } else { ?>
    <main class="container">
    <form action="process_profile.php" method="POST">
    <div class="headingsContainer">
        <h3>Edit Profile details</h3>
    </div>
    <div class="mainContainer mb-3">
    
        <label for="fname">First Name</label>
        <input type="text" placeholder="Enter First Name" name="fname" id="fname" value="<?php echo htmlspecialchars($fname); ?>" required>
        <br>
        <label for="lname">Last Name</label>
        <input type="text" placeholder="Enter Last Name" name="lname" id="lname" value="<?php echo htmlspecialchars($lname); ?>" required>
        <br>
        <label for="email">Email</label>
        <input type="text" placeholder="Enter Email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
        <br>
    </div>
    <div class="mb-3">
    <button type="submit">Edit details</button>
</div>

    <?php 
    if (isset($_SESSION['error_msg'])) {
        echo "<p style='color:#794324'>" . $_SESSION['error_msg'] . '</p>';
        unset($_SESSION['error_msg']); // Clear the message after displaying
    }    echo "<!-- Admin: I should store my password in a image so i wont forget it, anyway hackers wont be able to find it hahaha -->";

    
}
    ?>
</main> 
<?php include "inc/footer.inc.php";
?>
