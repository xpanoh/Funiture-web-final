<?php
// Start the session and include necessary files if needed
session_start();

// Capture and sanitize the token from the URL
$token = isset($_GET['token']) ? htmlspecialchars($_GET['token']) : '';

// Include any necessary header, navigation, or footer files
include "inc/head.inc.php";
include "inc/header.inc.php";
include "inc/nav.inc.php";

?>

<link rel="stylesheet" href="css/login.css">
<main class="container">
    <?php if (!isset($_SESSION['email'])): ?>
        <script>
            window.location.href = "home.php";
            alert("Page not found");
        </script>
    <?php else: ?>
        <form action="process_resetpassword.php?token=<?= $token ?>" method="POST">
            <div class="headingsContainer">
                <h3>Reset password</h3>
                <p>Reset password for <?= $_SESSION["email"] ?></p>
            </div>
            <div class="mainContainer mb-3">
                <!-- Password fields and any other inputs -->
                <label for="pwd">Your password</label>
                <input type="password" placeholder="Enter Password" name="pwd" id="pwd" required>
                <br>
                <label for="pwd_confirm">Confirm password</label>
                <input type="password" placeholder="Confirm Password" name="pwd_confirm" id="pwd_confirm" required>
                <!-- Submit button and potentially other form elements -->
            </div>
            <?php if (isset($_SESSION['error_msg'])) {
    echo "<h5 style='color:#794324'>" . $_SESSION['error_msg'] . "</h5></div>";
    unset($_SESSION['error_msg']); }?>
            <div class="mb-3">
                 <button type="submit">Submit</button>
            </div>
        </form>
    <?php endif; ?>
</main>

<?php
// Include footer if you have one
include "inc/footer.inc.php";
?>
