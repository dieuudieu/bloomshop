<!-- ==============================================================
     BLOOMSHOP - TRANG CHỦ CHUYÊN NGHIỆP & TINH GỌN (STUDIO BANNER)
     1. Hero Banner Studio Slider: Tinh gọn, sang trọng, chuyển động mượt mà
     2. Khối hoa 1: Hoa nổi bật & bán chạy
     3. Banner chuyển động giữa các khối hoa: Flash Sale & Dịch vụ VIP
     4. Khối hoa 2: Bộ sưu tập hoa mới về
     ============================================================== -->

<!-- 1. HERO SECTION: STUDIO HERO BANNER SLIDER TINH GỌN & SANG TRỌNG -->
<section class="hero-banner-section py-4 py-lg-5">
    <div class="container">
        <!-- Khung Hero Banner Studio Slider (Tự động chuyển mỗi 4s khi không tương tác) -->
        <div id="bloomHeroCarousel" class="carousel slide carousel-fade hero-card-container position-relative overflow-hidden" data-bs-ride="carousel" data-bs-interval="4000" data-bs-pause="false">
            
            <div class="carousel-inner">
                <!-- SLIDE 1: HOA HỒNG TÌNH YÊU -->
                <div class="carousel-item active">
                    <div class="hero-slide-wrapper slide-bg-rose">
                        <div class="row align-items-center g-4 p-4 p-md-5 w-100 m-0">
                            <div class="col-lg-6 hero-slide-text">
                                <div class="hero-pill-badge mb-3">
                                    <i class="bi bi-stars"></i>
                                    <span>BỘ SƯU TẬP HOA TÌNH YÊU 2026</span>
                                </div>
                                <h1 class="hero-display-title">Trao hoa tươi,<br><span class="hero-gradient-text">gửi trọn yêu thương</span></h1>
                                <p class="hero-lead-desc">Những đóa hồng Ecuador tuyển chọn trong ngày, kết hợp cùng hoa lá phụ cao cấp mang đến xúc cảm chân thành và tinh tế nhất.</p>
                                
                                <div class="hero-highlights d-flex gap-3 mb-4 flex-wrap">
                                    <div class="highlight-item"><i class="bi bi-patch-check-fill text-bloom"></i> 100% Hoa tươi mới</div>
                                    <div class="highlight-item"><i class="bi bi-truck text-bloom"></i> Giao hỏa tốc 2h</div>
                                    <div class="highlight-item"><i class="bi bi-envelope-heart-fill text-bloom"></i> Tặng thiệp thiết kế</div>
                                </div>

                                <div class="d-flex gap-3 flex-wrap align-items-center">
                                    <a href="index.php?act=sanpham&id_dm=1" class="btn btn-bloom hero-action-btn">
                                        <i class="bi bi-bag-heart me-1"></i> KHÁM PHÁ HOA HỒNG
                                    </a>
                                    <a href="index.php?act=sanpham&sort=sale" class="btn hero-outline-btn">
                                        <i class="bi bi-tag me-1"></i> ƯU ĐÃI HÔM NAY
                                    </a>
                                </div>
                            </div>

                            <div class="col-lg-6 hero-slide-media text-center position-relative">
                                <div class="hero-media-card">
                                    <img src="<?= $asset_dir ?>/image/sanpham/rose1.webp" class="img-fluid hero-banner-photo" alt="Hoa hồng quyến rũ">
                                    <!-- Floating Glassmorphism Tag siêu sang trọng (thay thế hộp xám cũ) -->
                                    <div class="glass-product-tag">
                                        <div class="glass-tag-icon"><i class="bi bi-flower1"></i></div>
                                        <div class="glass-tag-info text-start">
                                            <div class="glass-tag-name">Hoa Hồng Đỏ Quyến Rũ</div>
                                            <div class="glass-tag-meta">
                                                <span class="glass-tag-price">250.000đ</span>
                                                <span class="glass-tag-rating"><i class="bi bi-star-fill text-warning"></i> 5.0 (200+ đã mua)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SLIDE 2: HOA TULIP & HƯỚNG DƯƠNG -->
                <div class="carousel-item">
                    <div class="hero-slide-wrapper slide-bg-tulip">
                        <div class="row align-items-center g-4 p-4 p-md-5 w-100 m-0">
                            <div class="col-lg-6 hero-slide-text">
                                <div class="hero-pill-badge badge-tulip mb-3">
                                    <i class="bi bi-sun-fill"></i>
                                    <span>TULIP HÀ LAN & HƯỚNG DƯƠNG RỰC RỠ</span>
                                </div>
                                <h1 class="hero-display-title">Sắc hoa tươi sáng,<br><span class="hero-gradient-text-orange">ngập tràn năng lượng</span></h1>
                                <p class="hero-lead-desc">Tuyển tập hoa Tulip Hà Lan thanh tao và Hướng Dương ấm áp, món quà tuyệt vời nhất để chúc mừng sinh nhật, khai trương và thăng tiến.</p>
                                
                                <div class="hero-highlights d-flex gap-3 mb-4 flex-wrap">
                                    <div class="highlight-item"><i class="bi bi-patch-check-fill text-warning"></i> Nhập khẩu chính ngạch</div>
                                    <div class="highlight-item"><i class="bi bi-award-fill text-warning"></i> Độ bền tươi 5-7 ngày</div>
                                    <div class="highlight-item"><i class="bi bi-ticket-perforated-fill text-warning"></i> Giảm 30% hôm nay</div>
                                </div>

                                <div class="d-flex gap-3 flex-wrap align-items-center">
                                    <a href="index.php?act=sanpham&id_dm=2" class="btn btn-warning text-dark fw-bold hero-action-btn">
                                        <i class="bi bi-flower2 me-1"></i> XEM HOA TULIP
                                    </a>
                                    <a href="index.php?act=sanpham&id_dm=3" class="btn hero-outline-btn">
                                        <i class="bi bi-sun me-1"></i> HOA HƯỚNG DƯƠNG
                                    </a>
                                </div>
                            </div>

                            <div class="col-lg-6 hero-slide-media text-center position-relative">
                                <div class="hero-media-card">
                                    <img src="<?= $asset_dir ?>/image/sanpham/tulip1.webp" class="img-fluid hero-banner-photo" alt="Hoa tulip Hà Lan">
                                    <div class="glass-product-tag">
                                        <div class="glass-tag-icon text-warning"><i class="bi bi-flower2"></i></div>
                                        <div class="glass-tag-info text-start">
                                            <div class="glass-tag-name">Tulip Hà Lan Tinh Khôi</div>
                                            <div class="glass-tag-meta">
                                                <span class="glass-tag-price">150.000đ</span>
                                                <span class="glass-tag-rating"><i class="bi bi-star-fill text-warning"></i> 4.9 (150+ đã mua)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SLIDE 3: HOA BÓ MIX NGHỆ THUẬT -->
                <div class="carousel-item">
                    <div class="hero-slide-wrapper slide-bg-mixed">
                        <div class="row align-items-center g-4 p-4 p-md-5 w-100 m-0">
                            <div class="col-lg-6 hero-slide-text">
                                <div class="hero-pill-badge badge-mixed mb-3">
                                    <i class="bi bi-gem"></i>
                                    <span>THIẾT KẾ HOA NGHỆ THUẬT THEO YÊU CẦU</span>
                                </div>
                                <h1 class="hero-display-title">Hoa bó nghệ thuật,<br><span class="hero-gradient-text-purple">độc bản & tinh tế</span></h1>
                                <p class="hero-lead-desc">Đội ngũ Florist 10+ năm kinh nghiệm sẵn sàng lắng nghe mọi ý tưởng và ngân sách để tạo nên tác phẩm hoa mang dấu ấn riêng của bạn.</p>
                                
                                <div class="hero-highlights d-flex gap-3 mb-4 flex-wrap">
                                    <div class="highlight-item"><i class="bi bi-camera-fill text-primary"></i> Gửi ảnh chụp trước khi giao</div>
                                    <div class="highlight-item"><i class="bi bi-box2-heart-fill text-primary"></i> Đóng gói quà tặng cao cấp</div>
                                    <div class="highlight-item"><i class="bi bi-shield-check text-primary"></i> Đổi trả nếu dập nát</div>
                                </div>

                                <div class="d-flex gap-3 flex-wrap align-items-center">
                                    <a href="index.php?act=sanpham&id_dm=4" class="btn btn-bloom hero-action-btn">
                                        <i class="bi bi-palette me-1"></i> HOA BÓ MIX NGHỆ THUẬT
                                    </a>
                                    <a href="tel:0901234567" class="btn hero-outline-btn">
                                        <i class="bi bi-telephone-fill me-1 text-danger"></i> TƯ VẤN: 0901.234.567
                                    </a>
                                </div>
                            </div>

                            <div class="col-lg-6 hero-slide-media text-center position-relative">
                                <div class="hero-media-card">
                                    <img src="<?= $asset_dir ?>/image/sanpham/mixed2.jpg" class="img-fluid hero-banner-photo" alt="Hoa bó mix nghệ thuật">
                                    <div class="glass-product-tag">
                                        <div class="glass-tag-icon text-primary"><i class="bi bi-palette"></i></div>
                                        <div class="glass-tag-info text-start">
                                            <div class="glass-tag-name">Bó Hoa Mix Nghệ Thuật</div>
                                            <div class="glass-tag-meta">
                                                <span class="glass-tag-price">645.000đ</span>
                                                <span class="glass-tag-rating"><i class="bi bi-star-fill text-warning"></i> 5.0 (VIP Flower)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sleek Minimalist Controls (Nút chuyển trang & thanh chỉ số bấm chuyển ngay) -->
            <div class="hero-carousel-controls" aria-label="Điều hướng banner">
                <button class="hero-arrow-btn" id="heroPrevBtn" type="button" data-bs-target="#bloomHeroCarousel" data-bs-slide="prev" aria-label="Slide trước" title="Slide trước">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <div class="hero-dash-indicators" id="heroDashIndicators">
                    <button type="button" data-bs-target="#bloomHeroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Chuyển đến Slide 1" title="Slide 1"></button>
                    <button type="button" data-bs-target="#bloomHeroCarousel" data-bs-slide-to="1" aria-label="Chuyển đến Slide 2" title="Slide 2"></button>
                    <button type="button" data-bs-target="#bloomHeroCarousel" data-bs-slide-to="2" aria-label="Chuyển đến Slide 3" title="Slide 3"></button>
                </div>
                <button class="hero-arrow-btn" id="heroNextBtn" type="button" data-bs-target="#bloomHeroCarousel" data-bs-slide="next" aria-label="Slide tiếp theo" title="Slide tiếp theo">
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- 2. CAM KẾT DỊCH VỤ -->
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

<!-- ==============================================================
     3. KHỐI HOA THỨ NHẤT: SẢN PHẨM NỔI BẬT & BÁN CHẠY
     ============================================================== -->
<section class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0" style="color: #604b41;">
                <i class="bi bi-flower1" style="color: var(--bloom-primary);"></i> Sản phẩm nổi bật
            </h2>
            <p class="text-muted small mb-0">Các mẫu hoa tươi được khách hàng yêu thích và đặt nhiều nhất tuần qua</p>
        </div>
        <a href="index.php?act=sanpham" class="text-decoration-none fw-semibold" style="color: var(--bloom-primary);">
            Xem tất cả hoa <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <?php 
    $sanpham = $hoa_noibat ?? [];
    include __DIR__ . '/_product_cards.php'; 
    ?>
</section>

<!-- ==============================================================
     4. ✨ BANNER CHUYỂN ĐỘNG GIỮA CÁC HOA (YÊU CẦU GIẢNG VIÊN) ✨
     Slider chuyển động tự động giữa Khối hoa 1 và Khối hoa 2
     ============================================================== -->
<section class="container my-5">
    <div id="middlePromoCarousel" class="carousel slide shadow-sm rounded-4 overflow-hidden middle-promo-slider" data-bs-ride="carousel" data-bs-interval="4000" data-bs-pause="false">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#middlePromoCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
            <button type="button" data-bs-target="#middlePromoCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#middlePromoCarousel" data-bs-slide-to="2"></button>
        </div>

        <div class="carousel-inner">
            <!-- Slide 1: Flash Sale -->
            <div class="carousel-item active">
                <div class="middle-slide-box middle-gradient-ruby p-4 p-lg-5 d-flex align-items-center justify-content-between flex-wrap gap-4">
                    <div class="middle-content text-white col-lg-7">
                        <div class="d-inline-flex align-items-center gap-2 mb-2 bg-white bg-opacity-20 px-3 py-1 rounded-pill small fw-bold">
                            <span class="live-dot"></span> FLASH SALE GIỜ VÀNG
                        </div>
                        <h2 class="fw-bold mb-2 display-6">Ưu Đãi Hoa Tươi - Giảm Đến 30%</h2>
                        <p class="mb-3 opacity-90">Nhập mã ưu đãi <span class="badge bg-white text-danger fs-6 px-2 py-1 fw-bold">BLOOM30</span> khi thanh toán để được giảm ngay 30% đơn hoa tình yêu & sinh nhật.</p>
                        <a href="index.php?act=sanpham&sort=sale" class="btn btn-light text-danger fw-bold px-4 py-2 rounded-pill shadow-sm">
                            <i class="bi bi-lightning-charge-fill me-1"></i> Săn Ưu Đãi Ngay
                        </a>
                    </div>
                    <div class="col-lg-4 text-center d-none d-lg-block">
                        <img src="<?= $asset_dir ?>/image/sanpham/mixed1.webp" class="rounded-4 shadow middle-img-card" alt="Flash Sale">
                    </div>
                </div>
            </div>

            <!-- Slide 2: Giao Hỏa Tốc -->
            <div class="carousel-item">
                <div class="middle-slide-box middle-gradient-violet p-4 p-lg-5 d-flex align-items-center justify-content-between flex-wrap gap-4">
                    <div class="middle-content text-white col-lg-7">
                        <div class="d-inline-flex align-items-center gap-2 mb-2 bg-white bg-opacity-20 px-3 py-1 rounded-pill small fw-bold">
                            <i class="bi bi-truck"></i> DỊCH VỤ HỎA TỐC
                        </div>
                        <h2 class="fw-bold mb-2 display-6">Giao Hoa Tận Tay Trong 2 Giờ</h2>
                        <p class="mb-3 opacity-90">Cam kết giao đúng giờ 100% tại nội thành. Miễn phí thiệp viết tay và banner thiết kế sang trọng cho mọi đơn hàng.</p>
                        <a href="index.php?act=tracuudonhang" class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm">
                            <i class="bi bi-geo-alt-fill me-1"></i> Tra Cứu Đơn Hàng
                        </a>
                    </div>
                    <div class="col-lg-4 text-center d-none d-lg-block">
                        <img src="<?= $asset_dir ?>/image/banner.jpg" class="rounded-4 shadow middle-img-card" alt="Giao hoa hỏa tốc">
                    </div>
                </div>
            </div>

            <!-- Slide 3: Thiết Kế Theo Ý -->
            <div class="carousel-item">
                <div class="middle-slide-box middle-gradient-emerald p-4 p-lg-5 d-flex align-items-center justify-content-between flex-wrap gap-4">
                    <div class="middle-content text-white col-lg-7">
                        <div class="d-inline-flex align-items-center gap-2 mb-2 bg-white bg-opacity-20 px-3 py-1 rounded-pill small fw-bold">
                            <i class="bi bi-gem"></i> FLORIST BLOOMSHOP
                        </div>
                        <h2 class="fw-bold mb-2 display-6">Thiết Kế Hoa Nghệ Thuật Riêng</h2>
                        <p class="mb-3 opacity-90">Chuyên viên cắm hoa 10+ năm kinh nghiệm tư vấn miễn phí theo ngân sách và thông điệp bạn muốn nhắn gửi.</p>
                        <a href="tel:0901234567" class="btn btn-light text-dark fw-bold px-4 py-2 rounded-pill shadow-sm">
                            <i class="bi bi-telephone-fill me-1 text-danger"></i> Hotline: 0901.234.567
                        </a>
                    </div>
                    <div class="col-lg-4 text-center d-none d-lg-block">
                        <img src="<?= $asset_dir ?>/image/sanpham/mixed2.jpg" class="rounded-4 shadow middle-img-card" alt="Thiết kế hoa riêng">
                    </div>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#middlePromoCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#middlePromoCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>

<!-- ==============================================================
     5. KHỐI HOA THỨ HAI: BỘ SƯU TẬP HOA MỚI NHẤT
     ============================================================== -->
<section class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0" style="color: #604b41;">
                <i class="bi bi-flower2" style="color: var(--bloom-primary);"></i> Mẫu hoa mới trong ngày
            </h2>
            <p class="text-muted small mb-0">Tuyển tập những mẫu hoa tươi mới nhất vừa cập bến BloomShop</p>
        </div>
        <a href="index.php?act=sanpham" class="text-decoration-none fw-semibold" style="color: var(--bloom-primary);">
            Xem tất cả hoa <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <?php 
    $sanpham = $hoa_moive ?? [];
    include __DIR__ . '/_product_cards.php'; 
    ?>

    <div class="text-center mt-5">
        <a href="index.php?act=sanpham" class="btn btn-bloom px-5 py-3 fw-bold rounded-pill shadow-sm">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i> XEM TOÀN BỘ DANH MỤC HOA TƯƠI
        </a>
    </div>
</section>
