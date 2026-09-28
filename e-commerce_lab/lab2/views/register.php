<?php

require_once __DIR__ . '/../core/core.php';

require_once __DIR__ . '/layout/header.php';

?>

<div class="form-container">

    <h1>Create an Account</h1>

    <div id="registerMessage"></div>


    <form id="registerForm" novalidate>

        <div class="form-group">

            <label for="customer_name">
                Full Name
            </label>

            <input
                type="text"
                id="customer_name"
                name="customer_name"
            >

            <div
                id="nameError"
                class="error-message">
            </div>

        </div>


        <div class="form-group">

            <label for="customer_email">
                Email
            </label>

            <input
                type="email"
                id="customer_email"
                name="customer_email"
            >

            <div
                id="emailError"
                class="error-message">
            </div>

        </div>


        <div class="form-group">

            <label for="customer_pass">
                Password
            </label>

            <input
                type="password"
                id="customer_pass"
                name="customer_pass"
            >

            <div
                id="passwordError"
                class="error-message">
            </div>

        </div>


        <div class="form-group">

            <label for="customer_country">
                Country
            </label>

            <select
                id="customer_country"
                name="customer_country">

                <option value="">
                    Select Country
                </option>

                <option value="Ghana">
                    Ghana
                </option>

                <option value="Nigeria">
                    Nigeria
                </option>

                <option value="Togo">
                    Togo
                </option>

                <option value="Ivory Coast">
                    Ivory Coast
                </option>

            </select>

            <div
                id="countryError"
                class="error-message">
            </div>

        </div>


        <div class="form-group">

            <label for="customer_city">
                City
            </label>

            <input
                type="text"
                id="customer_city"
                name="customer_city"
            >

            <div
                id="cityError"
                class="error-message">
            </div>

        </div>


        <div class="form-group">

            <label for="customer_contact">
                Contact Number
            </label>

            <input
                type="text"
                id="customer_contact"
                name="customer_contact"
            >

            <div
                id="contactError"
                class="error-message">
            </div>

        </div>


        <button
            type="submit"
            class="btn"
            id="registerButton">

            Register

        </button>

    </form>


    <a
        href="<?= BASE_URL ?>/views/login.php"
        class="form-link">

        Already have an account? Login

    </a>

</div>


<script>

    window.APP_BASE = <?= json_encode(BASE_URL) ?>;

</script>

<script src="<?= BASE_URL ?>/js/validate.js"></script>


<?php

require_once __DIR__ . '/layout/footer.php';

?>
