const quantityInputs =
document.querySelectorAll('.quantity-input');

const subtotalElements =
document.querySelectorAll('.subtotal');

const totalElement =
document.querySelector('.grand-total');

const cartItems =
document.querySelectorAll('.cart-card');

/* =========================
   UPDATE CART
========================= */

function updateCart(){

    let total = 0;

    quantityInputs.forEach((input, index) => {

        let price =
        parseFloat(input.dataset.price);

        let quantity =
        parseInt(input.value);

        /* PREVENT NEGATIVE */

        if(quantity < 1){

            quantity = 1;

            input.value = 1;
        }

        /* CALCULATE SUBTOTAL */

        let subtotal = price * quantity;

        subtotalElements[index].innerText =
        `₹ ${subtotal.toFixed(2)}`;

        total += subtotal;
    });

    /* UPDATE TOTAL */

    if(totalElement){

        totalElement.innerText =
        `₹ ${total.toFixed(2)}`;

        totalElement.classList.add('total-pop');

        setTimeout(() => {

            totalElement.classList.remove('total-pop');

        }, 300);
    }

    checkEmptyCart();
}

/* =========================
   QUANTITY INPUT EVENTS
========================= */

quantityInputs.forEach(input => {

    input.addEventListener('input', updateCart);
});

/* =========================
   PLUS BUTTONS
========================= */

document.querySelectorAll('.plus-btn')
.forEach(button => {

    button.addEventListener('click', () => {

        const input =
        button.parentElement.querySelector(
            '.quantity-input'
        );

        input.value =
        parseInt(input.value) + 1;

        updateCart();
    });
});

/* =========================
   MINUS BUTTONS
========================= */

document.querySelectorAll('.minus-btn')
.forEach(button => {

    button.addEventListener('click', () => {

        const input =
        button.parentElement.querySelector(
            '.quantity-input'
        );

        if(parseInt(input.value) > 1){

            input.value =
            parseInt(input.value) - 1;

            updateCart();
        }
    });
});

/* =========================
   REMOVE ITEM
========================= */

document.querySelectorAll('.remove-btn')
.forEach(button => {

    button.addEventListener('click', () => {

        const cartCard =
        button.closest('.cart-card');

        cartCard.style.opacity = '0';

        cartCard.style.transform =
        'translateX(100px)';

        setTimeout(() => {

            cartCard.remove();

            updateCart();

        }, 300);
    });
});

/* =========================
   EMPTY CART CHECK
========================= */

function checkEmptyCart(){

    const remainingItems =
    document.querySelectorAll('.cart-card');

    const cartContainer =
    document.querySelector('.cart-container');

    if(remainingItems.length === 0){

        cartContainer.innerHTML = `

        <div class="empty-cart">

            <img
            src="../assets/images/empty-cart.png"
            width="250"
            >

            <h2>Your Cart Is Empty</h2>

            <p>
                Add delicious food items
                to continue ordering.
            </p>

            <a href="restaurants.php"
               class="primary-btn">

               Browse Foods
            </a>

        </div>
        `;
    }
}

/* =========================
   INITIAL CALL
========================= */

updateCart();