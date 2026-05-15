// ===============================
// GLOBAL EMAIL FLAG
// ===============================

window.emailExists = false;

// ===============================
// ELEMENTS
// ===============================

const emailInput =
document.getElementById('email');

const emailMessage =
document.getElementById('emailMessage');

const registerBtn =
document.getElementById('registerBtn');

// ===============================
// AJAX EMAIL CHECK
// ===============================

if(emailInput){

    emailInput.addEventListener('keyup', function(){

        let email = this.value.trim();

        // EMPTY FIELD

        if(email === ''){

            emailMessage.innerHTML = '';

            emailInput.style.border =
            'none';

            registerBtn.disabled = false;

            registerBtn.style.opacity = '1';

            registerBtn.style.cursor =
            'pointer';

            window.emailExists = false;

            return;

        }

        // FETCH PHP

        fetch(`php/check-email.php?email=${email}`)

        .then(response => response.text())

        .then(data => {

            // SHOW MESSAGE

            emailMessage.innerHTML = data;

            // EMAIL EXISTS

            if(data.trim() === "Email already exists"){

                // FLAG

                window.emailExists = true;

                // MESSAGE STYLE

                emailMessage.style.color =
                '#ff4d6d';

                emailMessage.style.fontWeight =
                '600';

                // INPUT BORDER

                emailInput.style.border =
                '2px solid #ff4d6d';

                // DISABLE BUTTON

                registerBtn.disabled = true;

                registerBtn.style.opacity =
                '0.6';

                registerBtn.style.cursor =
                'not-allowed';

            }

            // EMAIL AVAILABLE

            else{

                // FLAG

                window.emailExists = false;

                // MESSAGE STYLE

                emailMessage.style.color =
                '#86efac';

                emailMessage.style.fontWeight =
                '600';

                // INPUT BORDER

                emailInput.style.border =
                '2px solid #86efac';

                // ENABLE BUTTON

                registerBtn.disabled = false;

                registerBtn.style.opacity =
                '1';

                registerBtn.style.cursor =
                'pointer';

            }

        })

        // SERVER ERROR

        .catch(error => {

            emailMessage.innerHTML =
            'Server Error';

            emailMessage.style.color =
            '#ff4d6d';

            emailInput.style.border =
            '2px solid #ff4d6d';

            registerBtn.disabled = true;

            registerBtn.style.opacity =
            '0.6';

            registerBtn.style.cursor =
            'not-allowed';

            window.emailExists = true;

        });

    });

}