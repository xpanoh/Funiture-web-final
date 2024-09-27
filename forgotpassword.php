<?php
session_start();
// Include any necessary header, navigation, or footer files
include "inc/head.inc.php";
include "inc/header.inc.php";
include "inc/nav.inc.php";
?>
<link rel="stylesheet" href="css/login.css">
<main class="container">
        <form action="process_forgotpassword.php" method="POST">
        <div class="headingsContainer">
            <h3>Change password</h3>
            <p> Enter the email registered with the password you want to change </p>
        </div>
        <div class="mainContainer mb-3">
            <label for="email">Email</label>
            <input type="text" placeholder="Enter Email" name="email" id="email" required>
            <br>
            <?php if (isset($_SESSION['error_msg'])) {
    echo "<p style='color:#794324'>" . $_SESSION['error_msg'] . '</p>';
    unset($_SESSION['error_msg']); // Clear the message after displaying
}?>
        <div class="mb-3">
             <button type="submit">Send reset email</button>
        </div>
        </div>

    </form>

    </main>

<?php
// Include footer file if needed
include "inc/footer.inc.php";
?>
