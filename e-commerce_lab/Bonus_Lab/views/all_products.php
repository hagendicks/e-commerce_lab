<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_once __DIR__ . '/layout/header.php';

$controller = new ProductController();

$products = $controller->getAllProducts();

?>

<section class="products-section">

    <h2>All Products</h2>

    <?php require __DIR__ . '/layout/product_grid.php'; ?>

</section>


<?php

require_once __DIR__ . '/layout/footer.php';

?>
