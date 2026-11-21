<?php

require_once __DIR__ . '/../../core/core.php';

require_admin();

require_once __DIR__ . '/../../controllers/CartController.php';

require_once __DIR__ . '/../layout/header.php';

$hours = filter_input(INPUT_GET, 'hours', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 720]]);

if ($hours === false || $hours === null) {
    $hours = 24;
}

$controller = new CartController();

$carts = $controller->getAbandonedCarts($hours);

$choices = [0 => 'any time', 1 => '1 hour', 24 => '24 hours', 72 => '3 days', 168 => '7 days'];

?>

<section class="cart-section">

    <h1>Abandoned Carts</h1>

    <form method="GET" class="reminder-filter">

        <label for="hours">Left in the cart for at least</label>

        <select id="hours" name="hours" onchange="this.form.submit()">
            <?php foreach ($choices as $value => $label): ?>
                <option value="<?= (int) $value ?>" <?= $hours === $value ? 'selected' : '' ?>>
                    <?= htmlspecialchars($label) ?>
                </option>
            <?php endforeach; ?>
        </select>

    </form>


    <?php if (empty($carts)): ?>

        <div class="empty-state">
            <p>No customers have unreminded carts waiting that long.</p>
        </div>

    <?php else: ?>

        <table class="brand-table cart-table">

            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Items</th>
                    <th>Cart Value</th>
                    <th>Waiting Since</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($carts as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td><?= htmlspecialchars($row['customer_email']) ?></td>
                        <td><?= (int) $row['items'] ?></td>
                        <td><?= money($row['total']) ?></td>
                        <td><?= htmlspecialchars($row['since']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>

        <form method="POST" action="<?= BASE_URL ?>/actions/send_cart_reminders_action.php">

            <input type="hidden" name="hours" value="<?= (int) $hours ?>">

            <button type="submit" class="btn">
                Send reminder emails (<?= count($carts) ?>)
            </button>

        </form>

    <?php endif; ?>

</section>


<?php

require_once __DIR__ . '/../layout/footer.php';

?>
