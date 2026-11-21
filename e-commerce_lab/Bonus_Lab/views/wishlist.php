<?php

require_once __DIR__ . '/../core/core.php';

require_login();

require_once __DIR__ . '/../controllers/WishlistController.php';

require_once __DIR__ . '/layout/header.php';

$wishlistController = new WishlistController();

$products = $wishlistController->getItems((int) $_SESSION['customer_id']);

?>

<section class="products-section" data-wishlist-page>

    <h2>My Wishlist</h2>

    <?php if (empty($products)): ?>

        <div class="empty-state">
            <p>You have not saved anything yet. Tap the heart on a product to save it here.</p>
            <a href="<?= BASE_URL ?>/views/all_products.php" class="btn cart-link-btn">Browse Products</a>
        </div>

    <?php else: ?>

        <?php require __DIR__ . '/layout/product_grid.php'; ?>

    <?php endif; ?>

</section>


<?php

require_once __DIR__ . '/layout/footer.php';

?>
