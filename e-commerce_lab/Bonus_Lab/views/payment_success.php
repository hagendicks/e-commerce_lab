<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../core/paystack_config.php';
require_once __DIR__ . '/../controllers/CartController.php';

if (!is_logged_in()) {
    set_flash('error', 'Please log in to view your order.');
    redirect(BASE_URL . '/views/login.php');
}

$orderId = filter_var($_GET['order_id'] ?? '', FILTER_VALIDATE_INT);

$cartController = new CartController();
$order = $orderId ? $cartController->getOrderById($orderId, (int) $_SESSION['customer_id']) : null;

if (!$order) {
    set_flash('error', 'Order not found.');
    redirect(BASE_URL . '/index.php');
}

require_once __DIR__ . '/layout/header.php';
?>
<section class="cart-section">

    <h1>Payment Successful</h1>
    <p>Thank you, <?= htmlspecialchars($_SESSION['customer_name'] ?? '') ?>! Your order has been confirmed.</p>

    <p>
        <strong>Invoice No:</strong> <?= (int) $order['invoice_no'] ?><br>
        <strong>Order ID:</strong> <?= (int) $order['order_id'] ?><br>
        <strong>Date:</strong> <?= htmlspecialchars($order['order_date']) ?><br>
        <strong>Status:</strong> <?= htmlspecialchars(ucfirst($order['order_status'])) ?><br>
        <?php if (!empty($order['payment_ref'])): ?>
            <strong>Payment Ref:</strong> <?= htmlspecialchars($order['payment_ref']) ?>
        <?php endif; ?>
    </p>

    <table class="brand-table cart-table">
        <thead>
            <tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr>
        </thead>
        <tbody>
            <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_title']) ?></td>
                    <td><?= (int) $item['qty'] ?></td>
                    <td><?= CURRENCY_SYMBOL ?><?= number_format((float) $item['product_price'], 2) ?></td>
                    <td><?= CURRENCY_SYMBOL ?><?= number_format($item['line_total'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="subtotal-row">
                <td colspan="3">Total Paid</td>
                <td><?= CURRENCY_SYMBOL ?><?= number_format($order['total'], 2) ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="cart-actions">
        <button type="button" class="btn btn-outline" onclick="window.print()">Print Invoice</button>
        <a href="<?= BASE_URL ?>/index.php" class="btn">Continue Shopping</a>
    </div>

</section>
<?php require_once __DIR__ . '/layout/footer.php'; ?>
