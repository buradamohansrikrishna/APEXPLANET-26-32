/* =========================================
   SKILLSPHERE VALIDATION JS
   assets/js/validation.js
========================================= */

// =========================================
// FORM VALIDATION
// =========================================

function validateForm(formId){

    const form =
    document.getElementById(formId);

    if(!form){

        console.error(
            'Form not found'
        );

        return false;

    }

    let isValid = true;

    // =========================================
    // INPUT FIELDS
    // =========================================

    const inputs =
    form.querySelectorAll(

        'input, textarea, select'

    );

    inputs.forEach((input)=>{

        // REMOVE OLD ERROR

        removeError(input);

        // REQUIRED CHECK

        if(

            input.hasAttribute('required') &&
            input.value.trim() === ''

        ){

            showError(

                input,

                `${getFieldName(input)} is required`

            );

            isValid = false;

            return;

        }

        // =========================================
        // EMAIL VALIDATION
        // =========================================

        if(

            input.type === 'email' &&
            input.value.trim() !== ''

        ){

            const emailPattern =

            /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

            if(

                !emailPattern.test(
                    input.value.trim()
                )

            ){

                showError(

                    input,

                    'Invalid email address'

                );

                isValid = false;

            }

        }

        // =========================================
        // PASSWORD VALIDATION
        // =========================================

        if(

            input.type === 'password' &&
            input.value.length > 0

        ){

            if(input.value.length < 6){

                showError(

                    input,

                    'Password must be at least 6 characters'

                );

                isValid = false;

            }

        }

        // =========================================
        // PHONE VALIDATION
        // =========================================

        if(

            input.name === 'phone' &&
            input.value.trim() !== ''

        ){

            const phonePattern =
            /^[0-9]{10}$/;

            if(

                !phonePattern.test(
                    input.value.trim()
                )

            ){

                showError(

                    input,

                    'Enter valid 10 digit phone number'

                );

                isValid = false;

            }

        }

        // =========================================
        // NUMBER VALIDATION
        // =========================================

        if(

            input.type === 'number' &&
            input.value !== ''

        ){

            if(Number(input.value) < 0){

                showError(

                    input,

                    'Value cannot be negative'

                );

                isValid = false;

            }

        }

    });

    return isValid;

}

// =========================================
// SHOW ERROR
// =========================================

function showError(input,message){

    input.style.border =
    '1px solid #ef4444';

    const error =
    document.createElement('small');

    error.className =
    'error-message';

    error.style.color =
    '#ef4444';

    error.style.display =
    'block';

    error.style.marginTop =
    '6px';

    error.innerText =
    message;

    input.parentElement.appendChild(
        error
    );

}

// =========================================
// REMOVE ERROR
// =========================================

function removeError(input){

    input.style.border = '';

    const error =

    input.parentElement.querySelector(
        '.error-message'
    );

    if(error){

        error.remove();

    }

}

// =========================================
// GET FIELD NAME
// =========================================

function getFieldName(input){

    return (

        input.getAttribute('placeholder') ||

        input.getAttribute('name') ||

        'Field'

    );

}

// =========================================
// LIVE VALIDATION
// =========================================

document.querySelectorAll(

    'input, textarea, select'

).forEach((input)=>{

    input.addEventListener(
        'keyup',
        ()=>{

            removeError(input);

        }
    );

    input.addEventListener(
        'change',
        ()=>{

            removeError(input);

        }
    );

});

// =========================================
// PASSWORD TOGGLE
// =========================================

function togglePassword(inputId){

    const input =
    document.getElementById(inputId);

    if(!input){

        return;

    }

    if(input.type === 'password'){

        input.type = 'text';

    }else{

        input.type = 'password';

    }

}

// =========================================
// IMAGE PREVIEW
// =========================================

function previewImage(

    inputId,
    previewId

){

    const input = (typeof inputId === 'object' && inputId !== null) ? inputId : document.getElementById(inputId);

    const preview =
    document.getElementById(previewId);

    if(

        input &&
        preview &&
        input.files[0]

    ){

        const reader =
        new FileReader();

        reader.onload =
        function(e){

            preview.src =
            e.target.result;

            preview.style.display =
            'block';

        };

        reader.readAsDataURL(
            input.files[0]
        );

    }

}

// =========================================
// CHARACTER COUNTER
// =========================================

function characterCounter(

    inputId,
    counterId,
    maxLength

){

    const input =
    document.getElementById(inputId);

    const counter =
    document.getElementById(counterId);

    if(input && counter){

        input.addEventListener(
            'input',
            ()=>{

                counter.innerText =

                `${input.value.length}/${maxLength}`;

            }
        );

    }

}

// =========================================
// CONSOLE MESSAGE
// =========================================

console.log(
    'SkillSphere Validation System Loaded'
);