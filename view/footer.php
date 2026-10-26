
<?php
if (!isset($asset_dir)) {
    $asset_dir = file_exists('view/css/style.css')
        ? 'view'
        : (file_exists('../view/css/style.css') ? '../view' : 'view');
}
?>

<footer class="site-footer bloom-footer mt-5">
    <div class="container">
        <div class="row g-4 py-5">

            <!-- Giới thiệu cửa hàng -->
            <div class="col-lg-3 col-md-6">
                <h4 class="footer-brand mb-3">
                    Bloom<span>Shop</span> 🌸
                </h4>
                <p class="footer-description">
                    BloomShop mang đến những bó hoa tươi thắm,
                    tinh tế và đầy ý nghĩa. Mỗi đóa hoa là một
                    lời yêu thương gửi đến người bạn trân quý.
                </p>
                <div class="footer-social d-flex gap-3">
                    <a href="https://www.facebook.com/"
                       target="_blank" rel="noopener noreferrer"
                       aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://www.instagram.com/"
                       target="_blank" rel="noopener noreferrer"
                       aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="https://www.tiktok.com/"
                       target="_blank" rel="noopener noreferrer"
                       aria-label="TikTok">
                        <i class="bi bi-tiktok"></i>
                    </a>
                </div>
            </div>

            <!-- Liên kết nhanh -->
            <div class="col-lg-2 col-md-6">
                <h5 class="footer-heading">Khám phá</h5>
                <ul class="footer-links">
                    <li>
                        <a href="index.php?act=trangchu">
                            Trang chủ
                        </a>
                    </li>
                    <li>
                        <a href="index.php?act=sanpham">
                            Sản phẩm
                        </a>
                    </li>
                    <li>
                        <a href="index.php?act=giohang">
                            Giỏ hàng
                        </a>
                    </li>
                    <li>
                        <a href="index.php?act=tracuudonhang">
                            Tra cứu đơn hàng
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Thông tin liên hệ -->
            <div class="col-lg-3 col-md-6">
                <h5 class="footer-heading">Liên hệ</h5>

                <div class="footer-contact-item">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>
                        613 Âu Cơ, Tân Phú, Hồ Chí Minh
                    </span>
                </div>

                <div class="footer-contact-item">
                    <i class="bi bi-telephone-fill"></i>
                    <span>
                        0767 144 663
                    </span>
                </div>

                <div class="footer-contact-item">
                    <i class="bi bi-envelope-fill"></i>
                    <span>
                        contact@bloomshop.store
                    </span>
                </div>

                <div class="footer-contact-item">
                    <i class="bi bi-clock-fill"></i>
                    <span>08:00 – 21:00 mỗi ngày</span>
                </div>
            </div>

            <!-- Google Maps -->
            <div class="col-lg-4 col-md-6">
                <h5 class="footer-heading">Tìm chúng tôi</h5>
                <div class="footer-map">
                    <iframe
                        src="https://www.google.com/maps?q=Ho+Chi+Minh+City+Vietnam&output=embed"
                        title="Bản đồ vị trí BloomShop"
                        width="100%"
                        height="220"
                        style="border:0;"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen>
                    </iframe>
                </div>
                <a class="footer-map-link"
                   href="https://maps.app.goo.gl/FW12hFGfmYsoJuKK6"
                   target="_blank" rel="noopener noreferrer">
                    <i class="bi bi-box-arrow-up-right"></i>
                    Xem trên Google Maps
                </a>
            </div>

        </div>

        <!-- Bản quyền -->
        <div class="footer-bottom text-center py-3">
            <p class="mb-1">
                &copy; <?= date('Y') ?> BloomShop.
                All rights reserved.
            </p>
            <small>
                🌷 Trao hoa tươi – Gửi yêu thương 🌷
            </small>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $asset_dir ?>/js/main.js?v=<?= file_exists(__DIR__ . '/js/main.js') ? filemtime(__DIR__ . '/js/main.js') : '1.0' ?>"></script>
</body>
</html>
