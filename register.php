<?php
            include "inc/head.inc.php";
            include "inc/header.inc.php";
            include "inc/nav.inc.php";
            session_start();
?>

<link rel="stylesheet" href="css/login.css">


    <main class="container">
        <?php if(!isset($_SESSION["email"])){ ?>
        <form action="process_register.php" method="POST">
        <div class="headingsContainer">
            <h3>Member Registration</h3>
            <p>For existing members, please go to the <a href="login.php" style="color: #66ccff;">Sign In page</a>.</p>
        </div>
        <div class="mainContainer mb-3">
        
            <label for="fname">First Name</label>
            <input type="text" placeholder="Enter First Name" name="fname" id="fname" required>
            <br>
            <label for="lname">Last Name</label>
            <input type="text" placeholder="Enter Last Name" name="lname" id="lname" required>
            <br>
            <label for="email">Email</label>
            <input type="text" placeholder="Enter Email" name="email" id="email" required>
            <br>
            <label for="pwd">Your password</label>
            <input type="password" placeholder="Enter Password" name="pwd" id="pwd" required>
            <br>
            <label for="pwd_confirm">Confirm password</label>
            <input type="password" placeholder="Confirm Password" name="pwd_confirm" id="pwd_confirm" required>
            <br>
            <?php 
    if (isset($_SESSION['error_msg'])) {
    echo "<h5 style='color:#794324'>" . $_SESSION['error_msg'] . "</h5></div>";
    unset($_SESSION['error_msg']); }
    // Unset the error message after displaying it so it doesn't keep appearing.
    } else {
        echo '<script>';
            echo 'window.location.href = "home.php";';
            echo 'alert("Page not allowed");';
            echo '</script>'; }?>
        <div class="mb-3">
             <button type="submit">Submit</button>
        </div>
        </div>

    </form>

    </main>

    <?php
    include "inc/footer.inc.php";
    ?>
