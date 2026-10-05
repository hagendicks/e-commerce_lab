<?php
require_once __DIR__ . '/../../core/core.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>shoppn</title>

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/css/style.css">

</head>

<body>

<header class="site-header">

    <div class="container nav-container">

        <a href="<?= BASE_URL ?>/index.php" class="logo-link">

            <img src="<?= BASE_URL ?>/images/logo.gif"
                 alt="shoppn Logo"
                 class="logo">

        </a>

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
                    Welcome <?= htmlspecialchars($_SESSION['customer_name'] ?? '') ?>
                </span>

                <a href="<?= BASE_URL ?>/views/account/my_account.php">
                    My Account
                </a>

                <?php if (is_admin()): ?>

                    <a href="<?= BASE_URL ?>/views/admin/brand.php">
                        Brands
                    </a>

                <?php endif; ?>

                <a href="<?= BASE_URL ?>/logout.php">
                    Logout
                </a>

            <?php endif; ?>

        </nav>

    </div>

</header>

<main class="container">
