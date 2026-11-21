document.addEventListener("DOMContentLoaded", function () {

    function setError(id, message) {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = message;
        }
    }

    function clearErrors(form) {
        form.querySelectorAll(".error-message").forEach(function (el) {
            el.textContent = "";
        });
    }


    const editForm = document.getElementById("editAccountForm");

    if (editForm) {

        editForm.addEventListener("submit", function (event) {

            clearErrors(editForm);

            const name = document.getElementById("customer_name").value.trim();
            const city = document.getElementById("customer_city").value.trim();
            const contact = document.getElementById("customer_contact").value.trim();

            let valid = true;

            if (name.length < 2) {
                setError("nameError", "Please enter your full name.");
                valid = false;
            }

            if (city === "") {
                setError("cityError", "Please enter your city.");
                valid = false;
            }

            if (!/^[0-9+\-\s]{7,15}$/.test(contact)) {
                setError("contactError", "Enter a valid contact number.");
                valid = false;
            }

            if (!valid) {
                event.preventDefault();
            }
        });
    }


    const passForm = document.getElementById("changePassForm");

    if (passForm) {

        const newPass = document.getElementById("new_password");

        function checkRules(password) {

            const rules = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[^A-Za-z0-9]/.test(password)
            };

            const labels = {
                length: "At least 8 characters",
                uppercase: "One uppercase letter",
                lowercase: "One lowercase letter",
                number: "One number",
                special: "One special character"
            };

            Object.keys(rules).forEach(function (key) {
                document.getElementById(key).textContent =
                    (rules[key] ? "✓ " : "✗ ") + labels[key];
            });

            return Object.values(rules).every(Boolean);
        }

        newPass.addEventListener("input", function () {
            checkRules(newPass.value);
        });

        passForm.addEventListener("submit", function (event) {

            clearErrors(passForm);

            const current = document.getElementById("current_password").value;
            const confirm = document.getElementById("confirm_password").value;

            let valid = true;

            if (current === "") {
                setError("currentError", "Enter your current password.");
                valid = false;
            }

            if (!checkRules(newPass.value)) {
                valid = false;
            }

            if (newPass.value !== confirm) {
                setError("confirmError", "Passwords do not match.");
                valid = false;
            }

            if (!valid) {
                event.preventDefault();
            }
        });
    }


    const deleteForm = document.getElementById("deleteAccountForm");

    if (deleteForm) {

        deleteForm.addEventListener("submit", function (event) {

            clearErrors(deleteForm);

            let valid = true;

            if (document.getElementById("password").value === "") {
                setError("passwordError", "Enter your password.");
                valid = false;
            }

            if (!document.getElementById("confirm_delete").checked) {
                setError("confirmDeleteError", "Tick the box to confirm.");
                valid = false;
            }

            if (!valid) {
                event.preventDefault();
            }
        });
    }

});
