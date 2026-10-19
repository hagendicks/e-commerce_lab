</main>

<footer class="site-footer">

    <div class="container">

        <p>&copy; <?= date('Y') ?> shoppn. All rights reserved.</p>

    </div>

</footer>

<script>
    window.APP_BASE_URL = <?= json_encode(BASE_URL) ?>;
</script>

<script src="<?= BASE_URL ?>/js/app.js"></script>

<script src="<?= BASE_URL ?>/js/cart.js"></script>

</body>

</html>
