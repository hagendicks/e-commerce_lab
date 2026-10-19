<?php

require_once __DIR__ . '/../core/core.php';

require_once __DIR__ . '/../controllers/CartController.php';

require_once __DIR__ . '/layout/header.php';


$cartController = new CartController();

$items = $cartController->getCartItems(get_ip());

$subtotal = 0.0;

?>

<section class="cart-section">

    <h1>Shopping Cart</h1>


    <?php if (empty($items)): ?>

        <div class="empty-state">

            <p>Your cart is empty.</p>

            <a href="<?= BASE_URL ?>/index.php" class="btn cart-link-btn">Continue Shopping</a>

        </div>

    <?php else: ?>

        <form id="removeForm" method="POST" action="<?= BASE_URL ?>/actions/remove_from_cart_action.php"></form>

        <form id="updateForm" method="POST" action="<?= BASE_URL ?>/actions/update_qty_action.php"></form>


        <table class="brand-table cart-table">

            <thead>
                <tr>
                    <th>Remove</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($items as $item): ?>

                    <?php
                    $lineTotal = (int) $item['qty'] * (float) $item['product_price'];
                    $subtotal += $lineTotal;
                    $img = product_image_url($item['product_image']);
                    ?>

                    <tr data-product-id="<?= (int) $item['p_id'] ?>">

                        <td>
                            <input
                                type="checkbox"
                                form="removeForm"
                                name="remove[]"
                                value="<?= (int) $item['p_id'] ?>"
                                aria-label="Select <?= htmlspecialchars($item['product_title']) ?> for removal"
                            >
                        </td>

                        <td>
                            <div class="cart-product">

                                <?php if ($img): ?>
                                    <img src="<?= htmlspecialchars($img) ?>" class="table-thumb" alt="">
                                <?php else: ?>
                                    <span class="table-thumb table-thumb-empty">No image</span>
                                <?php endif; ?>

                                <a href="<?= BASE_URL ?>/views/single_product.php?pro_id=<?= (int) $item['p_id'] ?>">
                                    <?= htmlspecialchars($item['product_title']) ?>
                                </a>

                            </div>
                        </td>

                        <td>
                            <input
                                type="text"
                                form="updateForm"
                                name="qty[]"
                                class="qty-input"
                                value="<?= (int) $item['qty'] ?>"
                                inputmode="numeric"
                                aria-label="Quantity for <?= htmlspecialchars($item['product_title']) ?>"
                            >
                            <input
                                type="hidden"
                                form="updateForm"
                                name="update[]"
                                value="<?= (int) $item['p_id'] ?>"
                            >
                        </td>

                        <td>$<?= number_format((float) $item['product_price'], 2) ?></td>

                        <td class="line-total">$<?= number_format($lineTotal, 2) ?></td>

                        <td>
                            <form
                                class="inline-form"
                                method="POST"
                                action="<?= BASE_URL ?>/actions/remove_from_cart_action.php"
                                onsubmit="return confirm('Remove this item from your cart?');"
                            >
                                <input type="hidden" name="remove[]" value="<?= (int) $item['p_id'] ?>">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

            <tfoot>
                <tr class="subtotal-row">
                    <td colspan="4">Subtotal</td>
                    <td colspan="2" id="cartSubtotal">$<?= number_format($subtotal, 2) ?></td>
                </tr>
            </tfoot>

        </table>


        <div class="cart-actions">

            <button
                type="submit"
                form="removeForm"
                class="delete-btn"
                onclick="return confirm('Remove the selected items from your cart?');"
            >Remove from Cart</button>

            <button type="submit" form="updateForm" class="btn">Update Quantity</button>

            <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline">Continue Shopping</a>

            <a href="<?= BASE_URL ?>/views/checkout.php" class="btn">Checkout</a>

        </div>

    <?php endif; ?>

</section>


<?php

require_once __DIR__ . '/layout/footer.php';

?>
