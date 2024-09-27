<?php
include "inc/head.inc.php";
include "inc/header.inc.php";
include "inc/nav.inc.php";
include "inc/zendesk_widget.inc.php";
session_start();
?>

<main>

<!-- End of homehavensupport Zendesk Widget script -->
<div class="container mt-4">
    <?php if (!isset($_SESSION['email'])){
echo "<div class='popup'>";
echo "<link rel='stylesheet' href='css/modal.css'>";
echo "<button id='close'>&times;</button>";
echo "<h3>Members get 10% off their first purchase!</h3>";
echo "<br>";
echo "<h2>Code: 10OFF</h2>";
echo "<p>Sign up as a member today to enjoy 10% off your first purchase!</p>";
echo "<a href='register.php'>Register as a new member</a>";
echo "<script src='js/modal.js'></script>";
echo "</div>";
} ?>
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="3"></li>
            <!-- Add more indicators as needed -->
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="/images/chocolatLivingTable.jpg" class="d-block w-100" alt="Chocolate Living Table">
            </div>
            <div class="carousel-item">
                <img src="/images/caramellaCenterTable.jpg" class="d-block w-100" alt="Caramella Center Table">
            </div>
            <div class="carousel-item">
                <img src="/images/elevatoSideChair.jpg" class="d-block w-100" alt="Elevato Side Chair">
            </div>
            <div class="carousel-item">
                <img src="/images/himukaSofa.jpg" class="d-block w-100" alt="Himuka Sofa">
            </div>
            <!-- Add more carousel items with different images -->
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <section class="about_section layout_padding slide-from-left">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <div class="img-box">
            <img src="/images/about_img.jpg" alt="">
          </div>
        </div>
        <div class="col-md-6">
          <div class="detail-box">
            <div class="heading_container">
              <h2>
                ABOUT US
              </h2>
            </div>
            <p>
            With a commitment to minimalism and quality craftsmanship, we offer a curated collection of wooden furniture pieces designed to elevate any living space. From sleek dining tables that foster gathering to cozy sofas that invite relaxation, each item embodies the natural beauty and versatility of wood. Our passion lies in providing our customers with thoughtfully crafted pieces that seamlessly blend into modern lifestyles</p>
            <a href="aboutUs.php">
              Read More
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

</main>
<script>
// Move the chatbot widget
window.addEventListener('load', function() {
    var chatbotWidget = document.getElementById('chatbot-widget');
    var chatContainer = document.getElementById('chat-container');

    // Function to move the chatbot widget
    function moveChatbot() {
        var windowHeight = window.innerHeight;
        var chatbotHeight = chatbotWidget.offsetHeight;
        var chatContainerHeight = chatContainer.offsetHeight;

        // Calculate the position
        var bottomPosition = (windowHeight - chatbotHeight) / 8;

        // Apply the new position
        chatbotWidget.style.bottom = bottomPosition + 'px';
        chatContainer.style.bottom = (bottomPosition + chatbotHeight -300) + 'px'; // Adjust as needed
    }

    // Call the function initially
    moveChatbot();

    // Recalculate the position on window resize
    window.addEventListener('resize', moveChatbot);
});

window.addEventListener('scroll', function() {
    var aboutSection = document.querySelector('.about_section');
    var sectionTop = aboutSection.offsetTop;
    var scrollPosition = window.scrollY;
    var windowHeight = window.innerHeight;

    // If the user has scrolled past the About Us section
    if (scrollPosition > sectionTop - windowHeight) {
        aboutSection.classList.add('slide-from-left'); // Add the animation class
    }
});
</script>
<?php
include "inc/footer.inc.php";
?>
