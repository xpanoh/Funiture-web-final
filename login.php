<body>
    <?php
    session_start();
    unset($_SESSION['email']); 
            include "inc/head.inc.php";
            include "inc/header.inc.php";
            include "inc/nav.inc.php";
    ?>
<link rel="stylesheet" href="css/login.css">


    <main class="container">
        <?php if (!isset($_SESSION["email"])){ ?>
    <form action="process_login.php" method="POST">
        <div class="headingsContainer">
            <h3>Sign in</h3>
            <p>Sign in with your email and password</p>
        </div>

        <div class="mainContainer mb-3">
            <label for="email">Your Email</label>
            <input type="text" placeholder="Enter email" name="email" required>
            <br>
            <label for="pwd">Your password</label>
            <input type="password" placeholder="Enter Password" name="pwd" required>
            <br>
    <?php 
    if (isset($_SESSION['error_msg'])) {
    echo "<h5 style='color:#794324'>" . $_SESSION['error_msg'] . "</h5></div>";}
    // Unset the error message after displaying it so it doesn't keep appearing.
    unset($_SESSION['error_msg']); }
    else {
        echo '<script>';
            echo 'window.location.href = "home.php";';
            echo 'alert("Already logged in");';
            echo '</script>';;
    }?>

             <button type="submit">Login</button>

            <p class="register">Not a member?  <a href="register.php">Register here!</a></p>
            <br>
            <p class="forgotpwd"><a href="forgotpassword.php">Forgot your password?</a></p>
        </div>

    </form>
    </main>

    <?php
    echo "<!-- Admin: The password to my vault is ABCDEFG -->";
    include "inc/footer.inc.php";
    ?>

</body>
