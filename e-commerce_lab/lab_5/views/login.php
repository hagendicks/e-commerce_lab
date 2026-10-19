<?php

require_once __DIR__ . '/../core/core.php';

require_once __DIR__ . '/layout/header.php';

?>

<div class="form-container">

    <h1>Login</h1>

    <div id="loginMessage"></div>


    <form id="loginForm">

        <div class="form-group">

            <label for="login_email">
                Email
            </label>

            <input
                type="email"
                id="login_email"
                name="customer_email"
                required
            >

        </div>


        <div class="form-group">

            <label for="login_password">
                Password
            </label>

            <input
                type="password"
                id="login_password"
                name="customer_pass"
                required
            >

        </div>


        <button
            type="submit"
            class="btn">

            Login

        </button>

    </form>


    <a
        href="<?= BASE_URL ?>/views/register.php"
        class="form-link">

        Don't have an account? Register

    </a>

</div>


<script>

    window.APP_BASE = <?= json_encode(BASE_URL) ?>;

</script>

<script src="<?= BASE_URL ?>/js/validate.js"></script>


<?php

require_once __DIR__ . '/layout/footer.php';

?>
