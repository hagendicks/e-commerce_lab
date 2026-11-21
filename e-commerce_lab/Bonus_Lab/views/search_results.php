<?php
require_once __DIR__ . '/../controllers/ProductController.php';

require_once __DIR__ . '/layout/header.php';


$query = trim(strip_tags($_GET['user_query'] ?? ''));

$products = [];

if ($query !== '') {
    $controller = new ProductController();
    $products = $controller->searchProducts($query);
}
?>

<section class="products-section">

    <?php if ($query === ''): ?>

        <h2>Search</h2>

        <div class="empty-state">
            <p>Type something in the search box to find products.</p>
        </div>

    <?php else: ?>

        <h2>Search results for "<?= htmlspecialchars($query) ?>"</h2>

        <?php require __DIR__ . '/layout/product_grid.php'; ?>

    <?php endif; ?>

</section>


<?php
require_once __DIR__ . '/layout/footer.php';
?>
