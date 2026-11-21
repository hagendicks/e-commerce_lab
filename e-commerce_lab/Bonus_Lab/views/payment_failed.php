<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/layout/header.php';
?>
<section class="cart-section">
    <h1>Payment Failed</h1>
    <div class="empty-state">
        <p>We could not complete your order and your cart is unchanged. If you were charged, please contact support with the payment reference shown above.</p>
        <a href="<?= BASE_URL ?>/views/payment.php" class="btn cart-link-btn">Try Again</a>
        <a href="<?= BASE_URL ?>/views/cart.php" class="btn btn-outline">Back to Cart</a>
    </div>
</section>
<?php require_once __DIR__ . '/layout/footer.php'; ?>
