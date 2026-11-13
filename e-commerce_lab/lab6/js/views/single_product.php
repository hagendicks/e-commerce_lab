<?php
require_once __DIR__ . '/../core/core.php';

require_once __DIR__ . '/../controllers/ProductController.php';


$proId = filter_input(INPUT_GET, 'pro_id', FILTER_VALIDATE_INT);

if (!$proId || $proId <= 0) {
    redirect(BASE_URL . '/index.php');
}

$controller = new ProductController();

$product = $controller->getProductById($proId);

if (!$product) {
    redirect(BASE_URL . '/index.php');
}

$img = product_image_url($product['product_image']);

require_once __DIR__ . '/layout/header.php';
?>

<section class="product-detail">

    <div class="product-detail-image">
        <?php if ($img): ?>
            <img
                src="<?= htmlspecialchars($img) ?>"
                alt="<?= htmlspecialchars($product['product_title']) ?>"
            >
        <?php else: ?>
            <span class="no-image">No image</span>
        <?php endif; ?>
    </div>


    <div class="product-detail-info">

        <h1><?= htmlspecialchars($product['product_title']) ?></h1>

        <p class="product-price product-price-large">
            $<?= number_format((float) $product['product_price'], 2) ?>
        </p>

        <ul class="product-meta">
            <li>
                <strong>Category:</strong>
                <a href="<?= BASE_URL ?>/index.php?cat=<?= (int) $product['product_cat'] ?>">
                    <?= htmlspecialchars($product['cat_name']) ?>
                </a>
            </li>
            <li>
                <strong>Brand:</strong>
                <a href="<?= BASE_URL ?>/index.php?brand=<?= (int) $product['product_brand'] ?>">
                    <?= htmlspecialchars($product['brand_name']) ?>
                </a>
            </li>
            <?php if (!empty($product['product_keywords'])): ?>
                <li>
                    <strong>Keywords:</strong>
                    <?= htmlspecialchars($product['product_keywords']) ?>
                </li>
            <?php endif; ?>
        </ul>

        <h3>Description</h3>

        <p class="product-description">
            <?= nl2br(htmlspecialchars($product['product_desc'] ?? 'No description available.')) ?>
        </p>

        <a
            class="btn"
            href="<?= BASE_URL ?>/actions/add_to_cart_action.php?add_cart=<?= (int) $product['product_id'] ?>"
        >Add to Cart</a>

        <a href="<?= BASE_URL ?>/index.php" class="form-link">&larr; Continue shopping</a>

    </div>

</section>


<?php
require_once __DIR__ . '/layout/footer.php';
?>
