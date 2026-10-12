<?php

require_once __DIR__ . '/../../core/core.php';

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

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/css/style.css?v=<?= $cssVersion ?>"
    >

</head>

<body>

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
            >

            <button type="submit" class="btn">Search</button>

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

        </nav>

    </div>

</header>


<main class="container">

    <?php
    require_once __DIR__ . '/sidebar.php';
    ?>
