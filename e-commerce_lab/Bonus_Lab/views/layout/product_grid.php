<?php

$wishIds = [];

if (is_logged_in()) {

    require_once __DIR__ . '/../../controllers/WishlistController.php';

    $wishIds = (new WishlistController())->getProductIds((int) $_SESSION['customer_id']);
}

?>
<?php if (empty($products)): ?>

    <div class="empty-state">
        <p>No products found.</p>
    </div>

<?php else: ?>

    <div class="product-grid">

        <?php foreach ($products as $index => $item): ?>

            <?php
            $img = product_image_url($item['product_image']);
            $wished = in_array((int) $item['product_id'], $wishIds, true);
            ?>

            <article class="product-card" style="--i: <?= (int) $index ?>">

                <a
                    href="<?= BASE_URL ?>/actions/wishlist_action.php?toggle=<?= (int) $item['product_id'] ?>"
                    class="wish-btn <?= $wished ? 'active' : '' ?>"
                    data-product-id="<?= (int) $item['product_id'] ?>"
                    aria-label="Save to wishlist"
                    title="Save to wishlist"
                ><span aria-hidden="true">&#9829;</span></a>

                <a
                    href="<?= BASE_URL ?>/views/single_product.php?pro_id=<?= (int) $item['product_id'] ?>"
                    class="product-image"
                >
                    <?php if ($img): ?>
                        <img
                            src="<?= htmlspecialchars($img) ?>"
                            alt="<?= htmlspecialchars($item['product_title']) ?>"
                        >
                    <?php else: ?>
                        <span class="no-image">No image</span>
                    <?php endif; ?>
                </a>

                <div class="product-body">

                    <h3><?= htmlspecialchars($item['product_title']) ?></h3>

                    <p class="product-price">
                        <?= money($item['product_price']) ?>
                    </p>

                    <div class="product-actions">

                        <a
                            class="details-link"
                            href="<?= BASE_URL ?>/views/single_product.php?pro_id=<?= (int) $item['product_id'] ?>"
                        >Details</a>

                        <a
                            class="btn btn-small"
                            href="<?= BASE_URL ?>/actions/add_to_cart_action.php?add_cart=<?= (int) $item['product_id'] ?>"
                        >Add to Cart</a>

                    </div>

                </div>

            </article>

        <?php endforeach; ?>

    </div>

<?php endif; ?>
