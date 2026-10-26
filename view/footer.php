<?php
if (!isset($asset_dir)) {
    $asset_dir = file_exists('view/css/style.css') ? 'view' : (file_exists('../view/css/style.css') ? '../view' : 'view');
}
?>
<footer class="site-footer mt-5 py-4 text-center">BloomShop &copy; <?= date('Y') ?> - Cửa hàng hoa tươi BloomShop</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $asset_dir ?>/js/main.js?v=<?= file_exists(__DIR__ . '/js/main.js') ? filemtime(__DIR__ . '/js/main.js') : '1.0' ?>"></script>
</body>
</html>