<?php

require_once __DIR__ . '/../../core/core.php';

require_once __DIR__ . '/../../controllers/CartController.php';

$headerCart = new CartController();

$cartCount = $headerCart->getCartCount(get_ip());
$cartTotal = $headerCart->getCartTotal(get_ip());

$flash = pull_flash();

$cssFile = __DIR__ . '/../../css/style.css';
$cssVersion = file_exists($cssFile) ? filemtime($cssFile) : time();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>shoppn</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&family=Poppins:wght@500;600&family=Raleway:wght@600;700&display=swap"
    >

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/css/style.css?v=<?= $cssVersion ?>"
    >

</head>

<body>

<div class="page-loader" id="pageLoader" aria-hidden="true">
    <span class="spinner"></span>
</div>

<header class="site-header">

    <div class="container nav-container">

        <a
            href="<?= BASE_URL ?>/index.php"
            class="logo-link"
        >

            <img
                src="<?= BASE_URL ?>/images/logo.gif"
                alt="shoppn Logo"
                class="logo"
            >

        </a>


        <form
            class="header-search"
            action="<?= BASE_URL ?>/views/search_results.php"
            method="GET"
        >

            <input
                type="search"
                name="user_query"
                placeholder="Search products..."
                value="<?= htmlspecialchars($_GET['user_query'] ?? '') ?>"
                aria-label="Search products"
                autocomplete="off"
            >

            <button type="submit" class="btn">Search</button>

            <div class="search-suggestions" id="searchSuggestions" hidden></div>

        </form>


        <nav>


            <a href="<?= BASE_URL ?>/index.php">
                Home
            </a>


            <?php if (!is_logged_in()): ?>


                <a href="<?= BASE_URL ?>/views/register.php">
                    Register
                </a>


                <a href="<?= BASE_URL ?>/views/login.php">
                    Login
                </a>


            <?php else: ?>

                <span class="welcome">
                    Welcome
                    <?= htmlspecialchars(
                        $_SESSION['customer_name'] ?? ''
                    ) ?>
                </span>


                <a
                    href="<?= BASE_URL ?>/views/account/my_account.php"
                >
                    My Account
                </a>


                <a href="<?= BASE_URL ?>/logout.php">
                    Logout
                </a>

            <?php endif; ?>


            <a
                href="<?= BASE_URL ?>/views/cart.php"
                class="cart-icon"
                aria-label="Shopping cart"
                title="Go to cart"
            >

                <svg
                    viewBox="0 0 24 24"
                    width="26"
                    height="26"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <circle cx="9" cy="20" r="1.4"></circle>
                    <circle cx="18" cy="20" r="1.4"></circle>
                    <path d="M2 3h3l2.6 12.2a1.6 1.6 0 0 0 1.6 1.3h8.6a1.6 1.6 0 0 0 1.6-1.2L21 8H6"></path>
                </svg>

                <span class="cart-badge" id="cartCount" data-count="<?= (int) $cartCount ?>"><?= (int) $cartCount ?></span>

                <span class="cart-total" id="cartTotal">$<?= number_format($cartTotal, 2) ?></span>

            </a>

        </nav>

    </div>

</header>


<main class="container">

    <?php
    require_once __DIR__ . '/sidebar.php';
    ?>


    <?php if ($flash): ?>

        <div class="flash-banner <?= $flash['type'] === 'success' ? 'success-message' : 'server-error' ?>">
            <?= htmlspecialchars($flash['text']) ?>
        </div>

    <?php endif; ?>
