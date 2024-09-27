/*
* This function sets the currently selected menu item to the 'active' state.
* It should be called whenever the page first loads.
*/
function activateMenu()
{
 const navLinks = document.querySelectorAll('nav a');
 navLinks.forEach(link =>
 {
 if (link.href === location.href)
 {
 link.classList.add('active');
 }
 })
}

function updatePrice(price)
{
    var amount = document.getElementById('stock').value;
    var total_amount = amount * price;
    document.getElementById('total').textContent = total_amount.toFixed(2);
    document.getElementById('price').value = total_amount.toFixed(2);
}

function updatePromocodeValid()
{
    var code = document.getElementById('discount').value;
    var check = false;
    var discount = 0;
    // Iterate over the promoCodes array to check if the code exists
    for (var i = 0; i < promoCodes.length; i++) {
        if (promoCodes[i].promocode === code) {
            discount = promoCodes[i].discount;
            check = true;
            break; // Exit the loop once a match is found
        }
    }

    if (check) {
        document.getElementById('validity').textContent = 'Your code is valid! Discount: ' + discount + '%';
    }
    else {
        document.getElementById('validity').textContent = 'Invalid code.';
    }
}


/* FAQ buttons */
const items = document.querySelectorAll(".accordion button");
function toggleAccordion() {
    const itemToggle = this.getAttribute('aria-expanded');
    
    for (i = 0; i < items.length; i++) {
    items[i].setAttribute('aria-expanded', 'false');
    }
    
    if (itemToggle == 'false') {
    this.setAttribute('aria-expanded', 'true');
    }
}
items.forEach(item => item.addEventListener('click', toggleAccordion));
activateMenu();

function showProductModal(index, name, image, description, price, stock, productId, productType) {
    // Construct the URL with product information
    var url = "productPage.php?name=" + encodeURIComponent(name) + "&image=" + encodeURIComponent(image) + "&description=" + encodeURIComponent(description) + "&price=" + encodeURIComponent(price) + "&stock=" + encodeURIComponent(stock) + "&productID=" + encodeURIComponent(productId) + "&productType=" +  encodeURIComponent(productType);
    // Redirect the user to the new page
    window.location.href = url;
}
