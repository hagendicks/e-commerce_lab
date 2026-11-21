<?php

require_once __DIR__ . '/../../core/core.php';

require_login();

require_once __DIR__ . '/../layout/header.php';

?>

<div class="form-container">

    <h1>Delete Account</h1>

    <div class="server-error">
        This permanently deletes your account and cannot be undone.
    </div>

    <form id="deleteAccountForm" method="POST" action="<?= BASE_URL ?>/actions/delete_account_action.php" novalidate>

        <div class="form-group">
            <label for="password">Enter your password to confirm</label>
            <input type="password" id="password" name="password" autocomplete="current-password">
            <div class="error-message" id="passwordError"></div>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" id="confirm_delete" name="confirm_delete" value="1">
                I understand my account will be deleted
            </label>
            <div class="error-message" id="confirmDeleteError"></div>
        </div>

        <button type="submit" class="delete-btn">Delete My Account</button>

    </form>

    <a href="<?= BASE_URL ?>/views/account/my_account.php" class="form-link">Cancel</a>

</div>

<script src="<?= BASE_URL ?>/js/account.js"></script>

<?php

require_once __DIR__ . '/../layout/footer.php';

?>
