document.addEventListener("DOMContentLoaded", function () {

    const registerForm = document.getElementById("registerForm");

    if (registerForm) {

        registerForm.addEventListener("submit", function (event) {

            event.preventDefault();

            clearErrors();

            let valid = true;


            const name =
                document.getElementById("customer_name").value.trim();

            const email =
                document.getElementById("customer_email").value.trim();

            const password =
                document.getElementById("customer_pass").value;

            const country =
                document.getElementById("customer_country").value;

            const city =
                document.getElementById("customer_city").value.trim();

            const contact =
                document.getElementById("customer_contact").value.trim();


            const emailRegex =
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            const phoneRegex =
                /^[0-9+\-\s]{7,15}$/;


            if (name === "") {

                document.getElementById("nameError").textContent =
                    "Full name is required.";

                valid = false;
            }


            if (!emailRegex.test(email)) {

                document.getElementById("emailError").textContent =
                    "Enter a valid email.";

                valid = false;
            }


            if (password.length < 8) {

                document.getElementById("passwordError").textContent =
                    "Password must be at least 8 characters.";

                valid = false;
            }


            if (country === "") {

                document.getElementById("countryError").textContent =
                    "Please select your country.";

                valid = false;
            }


            if (city === "") {

                document.getElementById("cityError").textContent =
                    "City is required.";

                valid = false;
            }


            if (!phoneRegex.test(contact)) {

                document.getElementById("contactError").textContent =
                    "Enter a valid contact number.";

                valid = false;
            }


            if (!valid) {
                return;
            }


            const formData =
                new FormData(registerForm);


            const button =
                document.getElementById("registerButton");

            button.disabled = true;

            button.textContent = "Registering...";


            fetch(
                window.APP_BASE + "/actions/register_action.php",
                {
                    method: "POST",
                    body: formData
                }
            )

            .then(response => response.json())

            .then(data => {

                if (data.success) {

                    document.getElementById(
                        "registerMessage"
                    ).innerHTML =
                        `<div class="success-message">
                            ${data.message}
                         </div>`;

                    registerForm.reset();

                } else {

                    document.getElementById(
                        "registerMessage"
                    ).innerHTML =
                        `<div class="server-error">
                            ${data.error}
                         </div>`;
                }

            })

            .catch(error => {

                console.error(error);

                document.getElementById(
                    "registerMessage"
                ).innerHTML =
                    `<div class="server-error">
                        Something went wrong. Please try again.
                     </div>`;

            })

            .finally(() => {

                button.disabled = false;

                button.textContent = "Register";

            });

        });

    }


    const loginForm =
        document.getElementById("loginForm");


    if (loginForm) {

        loginForm.addEventListener(
            "submit",
            handleLogin
        );

    }


    const brandForm =
        document.getElementById("brandForm");


    if (brandForm) {

        brandForm.addEventListener(
            "submit",
            handleBrandSubmit
        );

    }

});


function clearErrors()
{
    const errors =
        document.querySelectorAll(".error-message");

    errors.forEach(function (error) {

        error.textContent = "";

    });
}


function handleLogin(event)
{
    event.preventDefault();

    const email =
        document.getElementById("login_email").value.trim();

    const password =
        document.getElementById("login_password").value;


    const message =
        document.getElementById("loginMessage");


    const emailRegex =
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


    if (!emailRegex.test(email)) {

        message.innerHTML =
            `<div class="server-error">
                Enter a valid email.
             </div>`;

        return;
    }


    if (password === "") {

        message.innerHTML =
            `<div class="server-error">
                Enter your password.
             </div>`;

        return;
    }


    const formData =
        new FormData(event.target);


    fetch(
        window.APP_BASE + "/actions/login_action.php",
        {
            method: "POST",
            body: formData
        }
    )

    .then(response => response.json())

    .then(data => {

        if (data.success) {

            window.location.href =
                window.APP_BASE + "/index.php";

        } else {

            message.innerHTML =
                `<div class="server-error">
                    ${data.error}
                 </div>`;

        }

    })

    .catch(error => {

        console.error(error);

        message.innerHTML =
            `<div class="server-error">
                Something went wrong.
             </div>`;

    });
}


function handleBrandSubmit(event)
{
    event.preventDefault();

    const form =
        event.target;

    const name =
        document.getElementById("brand_name").value.trim();

    const message =
        document.getElementById("brandMessage");


    if (name === "") {

        message.innerHTML =
            `<div class="server-error">
                Brand name is required.
             </div>`;

        return;
    }


    const formData =
        new FormData(form);


    let url =
        window.APP_BASE + "/actions/add_brand_action.php";


    if (form.dataset.editing === "true") {

        url =
            window.APP_BASE +
            "/actions/update_brand_action.php";

    }


    fetch(url, {
        method: "POST",
        body: formData
    })

    .then(response => response.json())

    .then(data => {

        if (data.success) {

            message.innerHTML =
                `<div class="success-message">
                    ${data.message}
                 </div>`;

            setTimeout(function () {

                window.location.href =
                    window.APP_BASE +
                    "/views/admin/brand.php";

            }, 700);

        } else {

            message.innerHTML =
                `<div class="server-error">
                    ${data.error}
                 </div>`;

        }

    })

    .catch(error => {

        console.error(error);

        message.innerHTML =
            `<div class="server-error">
                Something went wrong.
             </div>`;

    });
}
