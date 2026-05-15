// ===============================
// GLOBAL EMAIL FLAG
// ===============================

window.emailExists = false;

// ===============================
// SHOW / HIDE PASSWORD
// ===============================

const toggles = document.querySelectorAll('.toggle-password');

toggles.forEach(toggle => {

    toggle.addEventListener('click', function () {

        const input = this.previousElementSibling;

        if (input.type === 'password') {

            input.type = 'text';

            this.classList.remove('bi-eye-fill');
            this.classList.add('bi-eye-slash-fill');

        } 
        else {

            input.type = 'password';

            this.classList.remove('bi-eye-slash-fill');
            this.classList.add('bi-eye-fill');

        }

    });

});

// ===============================
// PASSWORD STRENGTH CHECKER
// ===============================

const passwordInput = document.getElementById('password');

if (passwordInput) {

    passwordInput.addEventListener('keyup', function () {

        const value = this.value;

        const strengthBar =
            document.getElementById('strengthBar');

        // RESET

        strengthBar.style.width = '0%';

        // WEAK

        if (value.length < 4) {

            strengthBar.style.width = '25%';

            strengthBar.className =
                'progress-bar bg-danger';

        }

        // MEDIUM

        else if (value.length < 8) {

            strengthBar.style.width = '60%';

            strengthBar.className =
                'progress-bar bg-warning';

        }

        // STRONG

        else {

            strengthBar.style.width = '100%';

            strengthBar.className =
                'progress-bar bg-success';

        }

    });

}

// ===============================
// EMAIL VALIDATION
// ===============================

function validateEmail(email) {

    const pattern =
        /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

    return email.match(pattern);

}

// ===============================
// REGISTER FORM VALIDATION
// ===============================

const registerForm =
    document.getElementById('registerForm');

if (registerForm) {

    registerForm.addEventListener('submit', function (e) {

        e.preventDefault();

        // INPUT VALUES

        const name =
            document.getElementById('name').value.trim();

        const email =
            document.getElementById('email').value.trim();

        const password =
            document.getElementById('password').value;

        const confirmPassword =
            document.getElementById('confirmPassword').value;

        // EMPTY FIELD CHECK

        if (
            name === '' ||
            email === '' ||
            password === '' ||
            confirmPassword === ''
        ) {

            Swal.fire({
                icon: 'error',
                title: 'All Fields Are Required'
            });

            return;

        }

        // EMAIL FORMAT CHECK

        if (!validateEmail(email)) {

            Swal.fire({
                icon: 'warning',
                title: 'Invalid Email Address'
            });

            return;

        }

        // EMAIL EXISTS CHECK

        if (window.emailExists === true) {

            Swal.fire({
                icon: 'error',
                title: 'Email Already Exists',
                text: 'Please use another email address'
            });

            return;

        }

        // PASSWORD LENGTH

        if (password.length < 6) {

            Swal.fire({
                icon: 'warning',
                title: 'Password Must Be At Least 6 Characters'
            });

            return;

        }

        // PASSWORD MATCH CHECK

        if (password !== confirmPassword) {

            Swal.fire({
                icon: 'error',
                title: 'Passwords Do Not Match'
            });

            return;

        }

        // SUCCESS

        Swal.fire({
            icon: 'success',
            title: 'Account Created Successfully',
            text: 'Redirecting to Dashboard...',
            showConfirmButton: false,
            timer: 2500
        });

        // REDIRECT

        setTimeout(() => {

            window.location.href = 'dashboard.html';

        }, 2500);

    });

}

// ===============================
// LOGIN FORM VALIDATION
// ===============================

const loginForm =
    document.getElementById('loginForm');

if (loginForm) {

    loginForm.addEventListener('submit', function (e) {

        e.preventDefault();

        // INPUT VALUES

        const email =
            document.getElementById('loginEmail').value.trim();

        const password =
            document.getElementById('loginPassword').value;

        // EMPTY FIELD CHECK

        if (email === '' || password === '') {

            Swal.fire({
                icon: 'error',
                title: 'Please Fill All Fields'
            });

            return;

        }

        // EMAIL FORMAT

        if (!validateEmail(email)) {

            Swal.fire({
                icon: 'warning',
                title: 'Please Enter Valid Email'
            });

            return;

        }

        // PASSWORD LENGTH

        if (password.length < 6) {

            Swal.fire({
                icon: 'warning',
                title: 'Password Too Short'
            });

            return;

        }

        // SUCCESS

        Swal.fire({
            icon: 'success',
            title: 'Login Successful',
            text: 'Welcome Back!',
            showConfirmButton: false,
            timer: 2000
        });

        // REDIRECT

        setTimeout(() => {

            window.location.href = 'dashboard.html';

        }, 2000);

    });

}