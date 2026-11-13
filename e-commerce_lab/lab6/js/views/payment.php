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
<section class="cart-section">

    <h1>Checkout</h1>

    <table class="brand-table cart-table">
        <thead>
            <tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <?php $line = (int) $item['qty'] * (float) $item['product_price']; ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_title']) ?></td>
                    <td><?= (int) $item['qty'] ?></td>
                    <td><?= CURRENCY_SYMBOL ?><?= number_format((float) $item['product_price'], 2) ?></td>
                    <td><?= CURRENCY_SYMBOL ?><?= number_format($line, 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="subtotal-row">
                <td colspan="3">Total</td>
                <td><?= CURRENCY_SYMBOL ?><?= number_format($total, 2) ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="cart-actions">
        <a href="<?= BASE_URL ?>/views/cart.php" class="btn btn-outline">Back to Cart</a>
        <button type="button" class="btn" id="payBtn">Pay <?= CURRENCY_SYMBOL ?><?= number_format($total, 2) ?> with Paystack</button>
    </div>

</section>

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

<?php require_once __DIR__ . '/layout/footer.php'; ?>
