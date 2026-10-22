<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 hero-content">
                <p class="hero-subtitle">BLOOMSHOP FLOWERS</p>
                <h1 class="hero-title">Trao hoa tươi, gửi trọn yêu thương</h1>
                <p class="hero-description">Những bó hoa được thiết kế tinh tế, mang trọn xúc cảm chân thành đến người bạn yêu quý.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="index.php?act=sanpham" class="btn hero-button">KHÁM PHÁ HOA TƯƠI</a>
                    <a href="index.php?act=sanpham&sort=sale" class="btn btn-outline-dark">ƯU ĐÃI KHUYẾN MÃI</a>
                </div>
            </div>
            <div class="col-md-6 hero-image mt-4 mt-md-0">
                <img src="/bloomshop/view/image/banner.jpg" alt="Hoa tươi BloomShop" onerror="this.src='https://images.unsplash.com/photo-1561181286-d3fee7d55364?auto=format&fit=crop&w=800&q=80'">
            </div>
        </div>
    </div>
</section>

<!-- Cam kết dịch vụ -->
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6">
                <div class="p-2">
                    <i class="bi bi-truck fs-2 text-bloom mb-2 d-block"></i>
                    <h6 class="fw-bold mb-1">Giao hoa 2 giờ</h6>
                    <small class="text-muted">Nhanh chóng & đúng hẹn</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-2">
                    <i class="bi bi-flower2 fs-2 text-bloom mb-2 d-block"></i>
                    <h6 class="fw-bold mb-1">100% Hoa tươi mới</h6>
                    <small class="text-muted">Tuyển chọn trong ngày</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-2">
                    <i class="bi bi-ticket-perforated fs-2 text-bloom mb-2 d-block"></i>
                    <h6 class="fw-bold mb-1">Voucher giảm giá</h6>
                    <small class="text-muted">Áp dụng mã tiết kiệm</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-2">
                    <i class="bi bi-gem fs-2 text-bloom mb-2 d-block"></i>
                    <h6 class="fw-bold mb-1">Tích điểm VIP</h6>
                    <small class="text-muted">Silver, Gold & Diamond</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sản phẩm nổi bật -->
<section class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0" style="color: #604b41;">Sản phẩm nổi bật</h2>
            <p class="text-muted small mb-0">Các mẫu hoa được yêu thích nhất tuần qua</p>
        </div>
        <a href="index.php?act=sanpham" class="text-decoration-none fw-semibold">
            Xem tất cả hoa <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <?php include __DIR__ . '/_product_cards.php'; ?>
</section>
