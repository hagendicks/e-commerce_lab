<?php

require_once __DIR__ . '/../../core/core.php';

require_login();

require_once __DIR__ . '/../layout/header.php';

?>

<div class="form-container">

    <h1>Change Password</h1>

    <form id="changePassForm" method="POST" action="<?= BASE_URL ?>/actions/change_password_action.php" novalidate>

        <div class="form-group">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" autocomplete="current-password">
            <div class="error-message" id="currentError"></div>
        </div>

        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" autocomplete="new-password">
            <div class="password-requirements">
                <p id="length">✗ At least 8 characters</p>
                <p id="uppercase">✗ One uppercase letter</p>
                <p id="lowercase">✗ One lowercase letter</p>
                <p id="number">✗ One number</p>
                <p id="special">✗ One special character</p>
            </div>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password">
            <div class="error-message" id="confirmError"></div>
        </div>

        <button type="submit" class="btn">Change Password</button>

    </form>

    <a href="<?= BASE_URL ?>/views/account/my_account.php" class="form-link">Back to My Account</a>

</div>

<script src="<?= BASE_URL ?>/js/account.js"></script>

<?php

require_once __DIR__ . '/../layout/footer.php';

?>
