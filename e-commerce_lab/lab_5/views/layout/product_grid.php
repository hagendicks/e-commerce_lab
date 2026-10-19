<?php if (empty($products)): ?>

    <div class="empty-state">
        <p>No products found.</p>
    </div>

<?php else: ?>

    <div class="product-grid">

        <?php foreach ($products as $index => $item): ?>

            <?php $img = product_image_url($item['product_image']); ?>

            <article class="product-card" style="--i: <?= (int) $index ?>">

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
                        $<?= number_format((float) $item['product_price'], 2) ?>
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
