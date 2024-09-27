
    <?php
    include "inc/head.inc.php";
    include "inc/header.inc.php";
    include "inc/nav.inc.php";
    ?>
<main>
    <h1 style="margin-top: 40px; margin-bottom: 50px;">Contact Us</h1>
        <div class="container contact-container">
            <div class="contact-info">
                <p>Showroom Address:
                <br>#02-102, Orchard Road, Singapore 238879</p>

                <p>Operating Hours:
                <br>Monday to Friday: 11:00am - 6:00pm<br>Saturday, Sunday &amp; Public Holiday: 11:00am - 7:00pm<br>(Close on every Wednesdays)</p>

                <p>General Enquiries:
                <br>E: homehaven.furniture@gmail.com<br>T: +65 6123 6456<br>WA: +65 6123 6456</p>
            </div>

            <div class="contact-form">
                <form action="process_contact.php" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name:</label>
                        <input required type="text" class="form-control" id="name" name="name">
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone:</label>
                        <input required type="tel" class="form-control" id="phone" name="phone">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input required maxlength="45" class="form-control" id="email" name="email">
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Message:</label>
                        <textarea required class="form-control" id="message" name="message" rows="5"></textarea>
                    </div>


                    <div class="mb-3">
                        <button type="submit" class = "btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
</main>
    <?php
    include "inc/footer.inc.php";
    ?>
