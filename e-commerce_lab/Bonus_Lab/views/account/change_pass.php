<?php

require_once __DIR__ . '/../../core/core.php';

require_login();

require_once __DIR__ . '/../../controllers/CustomerController.php';

$controller = new CustomerController();

$customer = $controller->getCustomerById((int) $_SESSION['customer_id']);

if (!$customer) {
    redirect(BASE_URL . '/logout.php');
}

$countries = ['Ghana', 'Nigeria', 'Togo', 'Ivory Coast'];

require_once __DIR__ . '/../layout/header.php';

?>

<div class="form-container">

    <h1>Edit Account</h1>

    <form id="editAccountForm" method="POST" action="<?= BASE_URL ?>/actions/update_account_action.php" novalidate>

        <div class="form-group">
            <label for="customer_email">Email</label>
            <input type="email" id="customer_email" value="<?= htmlspecialchars($customer['customer_email']) ?>" disabled>
        </div>

        <div class="form-group">
            <label for="customer_name">Full Name</label>
            <input type="text" id="customer_name" name="customer_name" value="<?= htmlspecialchars($customer['customer_name']) ?>">
            <div class="error-message" id="nameError"></div>
        </div>

        <div class="form-group">
            <label for="customer_country">Country</label>
            <select id="customer_country" name="customer_country">
                <?php foreach ($countries as $country): ?>
                    <option value="<?= htmlspecialchars($country) ?>" <?= $customer['customer_country'] === $country ? 'selected' : '' ?>>
                        <?= htmlspecialchars($country) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="customer_city">City</label>
            <input type="text" id="customer_city" name="customer_city" value="<?= htmlspecialchars($customer['customer_city']) ?>">
            <div class="error-message" id="cityError"></div>
        </div>

        <div class="form-group">
            <label for="customer_contact">Contact Number</label>
            <input type="text" id="customer_contact" name="customer_contact" value="<?= htmlspecialchars($customer['customer_contact']) ?>">
            <div class="error-message" id="contactError"></div>
        </div>

        <button type="submit" class="btn">Save Changes</button>

    </form>

    <a href="<?= BASE_URL ?>/views/account/my_account.php" class="form-link">Back to My Account</a>

</div>

<script src="<?= BASE_URL ?>/js/account.js"></script>

<?php

require_once __DIR__ . '/../layout/footer.php';

?>
