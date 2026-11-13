<?php
require_once __DIR__ . '/../controllers/ProductController.php';

require_once __DIR__ . '/layout/header.php';


$controller = new ProductController();

$heading = 'Featured Products';
$filtered = false;

$catId = filter_input(INPUT_GET, 'cat', FILTER_VALIDATE_INT);
$brandId = filter_input(INPUT_GET, 'brand', FILTER_VALIDATE_INT);


if ($catId && $catId > 0) {

    $products = $controller->getProductsByCategory($catId);
    $category = $controller->getCategoryById($catId);
    $heading = $category ? $category['cat_name'] : 'Category';
    $filtered = true;

} elseif ($brandId && $brandId > 0) {

    $products = $controller->getProductsByBrand($brandId);
    $brand = $controller->getBrandById($brandId);
    $heading = $brand ? $brand['brand_name'] : 'Brand';
    $filtered = true;

} else {

    $products = $controller->getFeaturedProducts();
}
?>

<?php if (!$filtered): ?>

    <section class="hero">

        <img src="<?= BASE_URL ?>/images/ad_banner.gif"
             alt="shoppn Advertisement Banner"
             class="hero-banner">

    </section>


    <div class="ticker" aria-label="Store highlights">

        <div class="ticker-track">

            <span>Welcome to shoppn</span>
            <span>Shop smarter. Live better.</span>
            <span>Browse by category and brand</span>
            <span>Add to cart in one click</span>

            <span aria-hidden="true">Welcome to shoppn</span>
            <span aria-hidden="true">Shop smarter. Live better.</span>
            <span aria-hidden="true">Browse by category and brand</span>
            <span aria-hidden="true">Add to cart in one click</span>

        </div>

    </div>


    <section class="welcome-section">

        <h1>Welcome to shoppn</h1>

        <p>
            Shop smarter. Live better.
        </p>

    </section>

<?php endif; ?>


<section class="products-section">

    <h2><?= htmlspecialchars($heading) ?></h2>

    <?php require __DIR__ . '/layout/product_grid.php'; ?>

</section>


<?php
require_once __DIR__ . '/layout/footer.php';
?>
