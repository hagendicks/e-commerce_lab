<?php
require_once __DIR__ . '/layout/header.php';
?>

<section class="hero">

    <img src="<?= BASE_URL ?>/images/ad_banner.gif"
         alt="shoppn Advertisement Banner"
         class="hero-banner">

</section>


<section class="welcome-section">

    <h1>Welcome to shoppn</h1>

    <p>
        Shop smarter. Live better.
    </p>

</section>


<section class="categories-section">

    <h2>Shop by Category</h2>

    <div class="category-grid">

        <div class="category-card">
            <h3>Electronics</h3>
        </div>

        <div class="category-card">
            <h3>Fashion</h3>
        </div>

        <div class="category-card">
            <h3>Home & Living</h3>
        </div>

        <div class="category-card">
            <h3>Beauty</h3>
        </div>

        <div class="category-card">
            <h3>Sports</h3>
        </div>

    </div>

</section>


<?php
require_once __DIR__ . '/layout/footer.php';
?>
