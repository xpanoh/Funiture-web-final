<link rel="stylesheet" href="css/main.css">
<?php session_start() ?>
<nav class="navbar navbar-expand-sm navbar-light" style="background-color: #61361e;">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="home.php" style="color: white;">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: white;">
                        Products
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="diningTable.php">Dining Table</a></li>
                        <li><a class="dropdown-item" href="chair.php">Chair</a></li>
                        <li><a class="dropdown-item" href="coffeeTable.php">Coffee Table</a></li>
                        <li><a class="dropdown-item" href="sofa.php">Sofa</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="aboutUs.php" style="color: white;">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="faq.php" style="color: white;">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contactUs.php" style="color: white;">Contact Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="viewCart.php" style="color: white;">Cart</a>
                </li>
                <?php
                // Check if the session variable is set and not null
                if (isset($_SESSION['email'])) {
                    // Session variable is not null, meaning a session exists/user is logged in
                    echo '<li class="nav-item">
                            <a class="nav-link" href="viewHistory.php" style="color: white;">History</a>
                        </li>';
                }
                ?>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="profile.php"><img src="images/profile.png" alt="User" style="width:10%; float:right;"></a>
                </li>
                <?php
                // Check if the session variable is set and not null
                if (isset($_SESSION['email'])) {
                    // Session variable is not null, meaning a session exists/user is logged in
                    echo '<li class="nav-item">
                            <a href="process_logout.php"><img src="images/logout.png" alt="User" style="float:right;"></a>
                        </li>';
                }
                ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>