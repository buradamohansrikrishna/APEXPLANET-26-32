/* =========================
   WEBSITE LOADED
========================= */

console.log("QuickBite Loaded Successfully");

/* =========================
   RESTAURANT CARD EFFECTS
========================= */

const cards =
document.querySelectorAll('.restaurant-card');

cards.forEach(card => {

    card.addEventListener('mouseenter', () => {

        card.style.transform =
        'translateY(-15px) scale(1.02)';

        card.style.boxShadow =
        '0 20px 40px rgba(255,107,53,0.15)';
    });

    card.addEventListener('mouseleave', () => {

        card.style.transform =
        'translateY(0px) scale(1)';

        card.style.boxShadow =
        '0 10px 30px rgba(0,0,0,0.08)';
    });
});

/* =========================
   NAVBAR SCROLL EFFECT
========================= */

const navbar =
document.querySelector('.navbar');

window.addEventListener('scroll', () => {

    if(window.scrollY > 50){

        navbar.style.padding = '14px 8%';

        navbar.style.boxShadow =
        '0 8px 25px rgba(0,0,0,0.08)';

        navbar.style.background =
        'rgba(255,255,255,0.92)';

    }else{

        navbar.style.padding = '18px 8%';

        navbar.style.boxShadow =
        '0 4px 20px rgba(0,0,0,0.05)';

        navbar.style.background =
        'rgba(255,255,255,0.85)';
    }
});

/* =========================
   SCROLL REVEAL ANIMATION
========================= */

const revealElements =
document.querySelectorAll(
    '.restaurant-card, .feature-box, .info-box'
);

function revealOnScroll(){

    const triggerBottom =
    window.innerHeight * 0.85;

    revealElements.forEach(element => {

        const elementTop =
        element.getBoundingClientRect().top;

        if(elementTop < triggerBottom){

            element.style.opacity = '1';

            element.style.transform =
            'translateY(0px)';
        }
    });
}

/* INITIAL STYLES */

revealElements.forEach(element => {

    element.style.opacity = '0';

    element.style.transform =
    'translateY(40px)';

    element.style.transition =
    'all 0.7s ease';
});

window.addEventListener(
    'scroll',
    revealOnScroll
);

revealOnScroll();

/* =========================
   BUTTON RIPPLE EFFECT
========================= */

const buttons =
document.querySelectorAll('.primary-btn');

buttons.forEach(button => {

    button.addEventListener('click', function(e){

        const ripple =
        document.createElement('span');

        ripple.classList.add('ripple');

        this.appendChild(ripple);

        const x =
        e.clientX -
        this.getBoundingClientRect().left;

        const y =
        e.clientY -
        this.getBoundingClientRect().top;

        ripple.style.left = `${x}px`;

        ripple.style.top = `${y}px`;

        setTimeout(() => {

            ripple.remove();

        }, 600);
    });
});

/* =========================
   IMAGE HOVER ZOOM
========================= */

const images =
document.querySelectorAll(
    '.restaurant-card img'
);

images.forEach(image => {

    image.addEventListener('mouseenter', () => {

        image.style.transform =
        'scale(1.08)';
    });

    image.addEventListener('mouseleave', () => {

        image.style.transform =
        'scale(1)';
    });
});

/* =========================
   LOADING ANIMATION
========================= */

window.addEventListener('load', () => {

    document.body.style.opacity = '1';

    document.body.style.transition =
    'opacity 0.5s ease';
});

/* =========================
   SMOOTH SCROLL LINKS
========================= */

document.querySelectorAll('a[href^="#"]')
.forEach(anchor => {

    anchor.addEventListener('click', function(e){

        e.preventDefault();

        const target =
        document.querySelector(
            this.getAttribute('href')
        );

        if(target){

            target.scrollIntoView({

                behavior:'smooth'
            });
        }
    });
});