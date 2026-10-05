<?php

require_once __DIR__ . '/../../core/core.php';

require_login();

require_once __DIR__ . '/../layout/header.php';

?>

<div class="form-container">

    <h1>My Account</h1>

    <p>
        Welcome,
        <strong>
            <?= htmlspecialchars($_SESSION['customer_name']) ?>
        </strong>
    </p>

    <p>
        Email:
        <?= htmlspecialchars($_SESSION['customer_email']) ?>
    </p>

    <?php if (is_admin()): ?>

        <p>
            Account Type: Administrator
        </p>

    <?php else: ?>

        <p>
            Account Type: Customer
        </p>

    <?php endif; ?>

</div>


<?php

require_once __DIR__ . '/../layout/footer.php';

?>
