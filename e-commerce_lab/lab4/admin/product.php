<?php

require_once __DIR__ . '/../../core/core.php';

require_admin();

require_once __DIR__ . '/../../controllers/ProductController.php';

$controller = new ProductController();

$categories = $controller->getAllCategories();
$brands     = $controller->getAllBrands();
$products   = $controller->getAllProducts();


$editId  = 0;
$product = null;
$loadError = '';

if (
    isset($_GET['edit_id']) &&
    filter_var($_GET['edit_id'], FILTER_VALIDATE_INT)
) {

    $editId = (int) $_GET['edit_id'];

    if ($editId > 0) {

        $product = $controller->getProductById($editId);

        if (!$product) {
            $loadError = 'Product not found.';
            $editId = 0;
        }

    } else {
        $editId = 0;
    }
}

$editing = $editId > 0;

require_once __DIR__ . '/../layout/header.php';

?>

<div class="brand-container">

    <h1><?= $editing ? 'Edit Product' : 'Add Product' ?></h1>


    <div id="productMessage">
        <?php if ($loadError !== ''): ?>
            <div class="server-error"><?= htmlspecialchars($loadError) ?></div>
        <?php endif; ?>
    </div>


    <form
        id="productForm"
        enctype="multipart/form-data"
        data-editing="<?= $editing ? 'true' : 'false' ?>"
    >

        <?php if ($editing): ?>
            <input type="hidden" name="product_id" value="<?= $editId ?>">
        <?php endif; ?>


        <div class="form-group">
            <label for="product_title">Product Title</label>
            <input
                type="text"
                id="product_title"
                name="product_title"
                maxlength="200"
                placeholder="Enter product title"
                value="<?= $editing ? htmlspecialchars($product['product_title']) : '' ?>"
            >
            <div class="error-message" id="titleError"></div>
        </div>


        <div class="form-row">

            <div class="form-group">
                <label for="product_cat">Category</label>
                <select id="product_cat" name="product_cat">
                    <option value="">Select category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option
                            value="<?= (int) $cat['cat_id'] ?>"
                            <?= ($editing && (int) $product['product_cat'] === (int) $cat['cat_id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($cat['cat_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="error-message" id="catError"></div>
            </div>


            <div class="form-group">
                <label for="product_brand">Brand</label>
                <select id="product_brand" name="product_brand">
                    <option value="">Select brand</option>
                    <?php foreach ($brands as $brand): ?>
                        <option
                            value="<?= (int) $brand['brand_id'] ?>"
                            <?= ($editing && (int) $product['product_brand'] === (int) $brand['brand_id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($brand['brand_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="error-message" id="brandError"></div>
            </div>

        </div>


        <div class="form-group">
            <label for="product_price">Price</label>
            <input
                type="number"
                id="product_price"
                name="product_price"
                step="0.01"
                min="0.01"
                placeholder="0.00"
                value="<?= $editing ? htmlspecialchars($product['product_price']) : '' ?>"
            >
            <div class="error-message" id="priceError"></div>
        </div>


        <div class="form-group">
            <label for="product_desc">Description</label>
            <textarea
                id="product_desc"
                name="product_desc"
                rows="4"
                maxlength="500"
                placeholder="Describe the product"
            ><?= $editing ? htmlspecialchars($product['product_desc'] ?? '') : '' ?></textarea>
            <div class="error-message" id="descError"></div>
        </div>


        <div class="form-group">
            <label for="product_keywords">Keywords</label>
            <input
                type="text"
                id="product_keywords"
                name="product_keywords"
                maxlength="100"
                placeholder="e.g. phone, android, smartphone"
                value="<?= $editing ? htmlspecialchars($product['product_keywords'] ?? '') : '' ?>"
            >
            <div class="error-message" id="keywordsError"></div>
        </div>


        <div class="form-group">
            <label for="product_image">
                Product Image<?= $editing ? ' (leave empty to keep the current image)' : '' ?>
            </label>

            <input
                type="file"
                id="product_image"
                name="product_image"
                accept="image/jpeg,image/png,image/gif,image/webp"
            >
            <div class="error-message" id="imageError"></div>

            <?php
            $currentImage = $editing ? product_image_url($product['product_image']) : null;
            ?>

            <img
                id="imagePreview"
                class="image-preview"
                alt="Image preview"
                <?= $currentImage ? 'src="' . htmlspecialchars($currentImage) . '"' : 'hidden' ?>
            >
        </div>


        <button type="submit" class="btn" id="productButton">
            <?= $editing ? 'Update Product' : 'Add Product' ?>
        </button>


        <?php if ($editing): ?>
            <a href="<?= BASE_URL ?>/views/admin/product.php" class="form-link">
                Cancel Edit
            </a>
        <?php endif; ?>

    </form>


    <h2 style="margin-top: 40px;">Existing Products</h2>


    <?php if (empty($products)): ?>

        <p>No products have been added yet.</p>

    <?php else: ?>

        <table class="brand-table">

            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($products as $row): ?>

                    <?php $thumb = product_image_url($row['product_image']); ?>

                    <tr>
                        <td>
                            <?php if ($thumb): ?>
                                <img src="<?= htmlspecialchars($thumb) ?>" class="table-thumb" alt="">
                            <?php else: ?>
                                <span class="table-thumb table-thumb-empty">No image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['product_title']) ?></td>
                        <td><?= htmlspecialchars($row['cat_name']) ?></td>
                        <td><?= htmlspecialchars($row['brand_name']) ?></td>
                        <td>$<?= number_format((float) $row['product_price'], 2) ?></td>
                        <td>
                            <a
                                class="edit-btn"
                                href="<?= BASE_URL ?>/views/admin/product.php?edit_id=<?= (int) $row['product_id'] ?>"
                            >Edit</a>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

</div>


<script>
    window.APP_BASE = <?= json_encode(BASE_URL) ?>;
</script>

<script src="<?= BASE_URL ?>/js/product.js"></script>


<?php

require_once __DIR__ . '/../layout/footer.php';

?>
