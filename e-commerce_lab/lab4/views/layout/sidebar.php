<?php
$sidebarPages = ['index.php', 'single_product.php', 'search_results.php'];

if (!in_array(basename($_SERVER['PHP_SELF']), $sidebarPages, true)) {
    return;
}

require_once __DIR__ . '/../../controllers/ProductController.php';

$sidebarController = new ProductController();

$sidebarCategories = $sidebarController->getAllCategories();
$sidebarBrands = $sidebarController->getAllBrands();

$activeCat = filter_input(INPUT_GET, 'cat', FILTER_VALIDATE_INT);
$activeBrand = filter_input(INPUT_GET, 'brand', FILTER_VALIDATE_INT);
$onHome = basename($_SERVER['PHP_SELF']) === 'index.php' && !$activeCat && !$activeBrand;
?>
<aside class="sidebar">

    <h3>Shop</h3>

    <ul>
        <li><a class="<?= $onHome ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php">Home</a></li>
    </ul>


    <h3>Categories</h3>

    <ul>
        <?php foreach ($sidebarCategories as $c): ?>
            <li>
                <a
                    class="<?= ((int) $c['cat_id'] === (int) $activeCat) ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>/index.php?cat=<?= (int) $c['cat_id'] ?>"
                ><?= htmlspecialchars($c['cat_name']) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>


    <h3>Brands</h3>

    <ul>
        <?php foreach ($sidebarBrands as $b): ?>
            <li>
                <a
                    class="<?= ((int) $b['brand_id'] === (int) $activeBrand) ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>/index.php?brand=<?= (int) $b['brand_id'] ?>"
                ><?= htmlspecialchars($b['brand_name']) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>


    <?php if (is_admin()): ?>
        <h3>Admin</h3>
        <ul>
            <li><a href="<?= BASE_URL ?>/views/admin/brand.php">Brand</a></li>
            <li><a href="<?= BASE_URL ?>/views/admin/category.php">Category</a></li>
            <li><a href="<?= BASE_URL ?>/views/admin/product.php">Product</a></li>
        </ul>
    <?php endif; ?>

</aside>
