<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../core/paystack_config.php';
require_once __DIR__ . '/../controllers/CartController.php';

if (!is_logged_in()) {
    set_flash('error', 'Please log in to check out.');
    redirect(BASE_URL . '/views/login.php');
}

$cartController = new CartController();

$items = $cartController->getCartItems(get_ip());

if (empty($items)) {
    set_flash('error', 'Your cart is empty.');
    redirect(BASE_URL . '/views/cart.php');
}

$total = 0.0;

foreach ($items as $item) {
    $total += (int) $item['qty'] * (float) $item['product_price'];
}

$amountPesewas = (int) round($total * 100);

require_once __DIR__ . '/layout/header.php';

?>
<link rel="stylesheet" href="<?= BASE_URL ?>/css/payment.css?v=<?= @filemtime(__DIR__ . '/../css/payment.css') ?: 1 ?>">
<section class="cart-section pay-page">

    <h1>Checkout</h1>

    <table class="brand-table cart-table">

        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($items as $item): ?>
                <?php $line = (int) $item['qty'] * (float) $item['product_price']; ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_title']) ?></td>
                    <td><?= (int) $item['qty'] ?></td>
                    <td><?= money($item['product_price']) ?></td>
                    <td><?= money($line) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr class="subtotal-row">
                <td colspan="3">Total</td>
                <td><?= money($total) ?></td>
            </tr>
        </tfoot>

    </table>

    <div class="cart-actions">
        <a href="<?= BASE_URL ?>/views/cart.php" class="btn btn-outline">Back to Cart</a>
        <button type="button" class="btn pay-btn" id="payBtn">Pay <?= money($total) ?> with Paystack</button>
    </div>

    <p class="pay-secure"><span class="lock">🔒</span> Secured by Paystack. Your card details never touch our server.</p>

</section>

<div class="pay-overlay" id="payOverlay" role="status" aria-live="polite">
    <div class="pay-orbit"><span></span></div>
    <p>Confirming your payment<i>...</i></p>
</div>

<script src="https://js.paystack.co/v2/inline.js"></script>
<script>
    document.getElementById('payBtn').addEventListener('click', function () {

        var btn = this;
        btn.disabled = true;

        var popup = new PaystackPop();

        popup.newTransaction({
            key: <?= json_encode(PAYSTACK_PUBLIC_KEY) ?>,
            email: <?= json_encode($_SESSION['customer_email'] ?? '') ?>,
            amount: <?= $amountPesewas ?>,
            currency: <?= json_encode(PAYSTACK_CURRENCY) ?>,
            onSuccess: function (response) {
                document.getElementById('payOverlay').classList.add('show');
                window.location = <?= json_encode(BASE_URL . '/actions/process_payment_action.php') ?>
                    + '?ref=' + encodeURIComponent(response.reference);
            },
            onCancel: function () {
                btn.disabled = false;
                alert('Payment cancelled.');
            }
        });
    });
</script>

<?php

require_once __DIR__ . '/layout/footer.php';

?>
