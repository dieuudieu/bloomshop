<?php 
// Khởi tạo và bảo vệ các biến truyền từ Controller
$sp = $sp ?? [];
$danhsach_danhgia = $danhsach_danhgia ?? [];
$avg_rating = $avg_rating ?? 5.0;
$sp_lienquan = $sp_lienquan ?? [];
$asset_dir = $asset_dir ?? (file_exists('view/css/style.css') ? 'view' : (file_exists('../view/css/style.css') ? '../view' : 'view'));

// Nếu không có dữ liệu sản phẩm hợp lệ
if (empty($sp) || empty($sp['id'])) {
    echo '<div class="container my-5 text-center">
            <div class="alert alert-warning py-4 shadow-sm rounded-3">
                <i class="bi bi-exclamation-triangle-fill fs-3 text-warning d-block mb-2"></i>
                <h5 class="fw-bold">Không tìm thấy sản phẩm hoa tươi</h5>
                <p class="text-muted mb-3">Sản phẩm này có thể đã bị xóa hoặc tạm ngưng kinh doanh.</p>
                <a href="index.php?act=sanpham" class="btn btn-bloom btn-sm">Xem danh sách hoa tươi khác</a>
            </div>
          </div>';
    return;
}

// Xử lý tính toán giá và khuyến mãi an toàn (chống lỗi chia cho 0)
$price = (float)($sp['price'] ?? 0);
$sale_price = (float)($sp['sale_price'] ?? 0);
$has_sale = (!empty($sp['is_sale']) && $sale_price > 0 && $sale_price < $price);
$current_price = $has_sale ? $sale_price : $price;
$discount_pct = ($has_sale && $price > 0) ? round((($price - $sale_price) / $price) * 100) : 0;
$count_reviews = count($danhsach_danhgia);
?>
<div class="container my-5">
    <!-- Breadcrumb điều hướng -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?act=trangchu" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="index.php?act=sanpham" class="text-decoration-none">Sản phẩm</a></li>
            <?php if (!empty($sp['id_dm']) && !empty($sp['tendanhmuc'])) { ?>
                <li class="breadcrumb-item"><a href="index.php?act=sanpham&id_dm=<?= $sp['id_dm'] ?>" class="text-decoration-none"><?= htmlspecialchars($sp['tendanhmuc']) ?></a></li>
            <?php } ?>
            <li class="breadcrumb-item active text-truncate" style="max-width: 300px;"><?= htmlspecialchars($sp['name'] ?? '') ?></li>
        </ol>
    </nav>

    <div class="row g-4 g-lg-5">
        <!-- Ảnh sản phẩm -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                <?php if ($has_sale) { ?>
                    <span class="badge-sale fs-6 px-3 py-2">-<?= $discount_pct ?>% GIẢM</span>
                <?php } ?>
                <img src="<?= $asset_dir ?>/image/sanpham/<?= htmlspecialchars($sp['image'] ?? '') ?>" 
                     class="img-fluid w-100" 
                     style="max-height: 480px; object-fit: cover;" 
                     alt="<?= htmlspecialchars($sp['name'] ?? '') ?>" 
                     onerror="this.src='<?= $asset_dir ?>/image/banner.jpg'">
            </div>
        </div>

        <!-- Thông tin sản phẩm & Đặt hàng -->
        <div class="col-md-6">
            <?php if (!empty($sp['id_dm']) && !empty($sp['tendanhmuc'])) { ?>
                <a href="index.php?act=sanpham&id_dm=<?= $sp['id_dm'] ?>" class="badge bg-light text-secondary border text-decoration-none mb-2">
                    <i class="bi bi-tag me-1"></i><?= htmlspecialchars($sp['tendanhmuc']) ?>
                </a>
            <?php } ?>

            <h1 class="fw-bold mb-3" style="color: #604b41;"><?= htmlspecialchars($sp['name'] ?? '') ?></h1>

            <!-- Đánh giá sao tóm tắt -->
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="star-rating">
                    <?php 
                    $avg = ($count_reviews > 0 && !empty($avg_rating)) ? round((float)$avg_rating, 1) : 5.0;
                    for ($s = 1; $s <= 5; $s++) {
                        echo ($s <= round($avg)) ? '★' : '☆';
                    }
                    ?>
                </div>
                <?php if ($count_reviews > 0) { ?>
                    <span class="fw-bold text-dark"><?= number_format($avg, 1) ?></span>
                    <span class="text-muted small">(<?= $count_reviews ?> đánh giá)</span>
                <?php } else { ?>
                    <span class="text-muted small">(Chưa có đánh giá)</span>
                <?php } ?>
            </div>

            <!-- Khung giá bán -->
            <div class="mb-4 p-3 rounded-3" style="background-color: #fdf8f5; border: 1px dashed #ebdcd5;">
                <span class="fs-2 fw-bold price-text"><?= number_format($current_price, 0, ',', '.') ?>đ</span>
                <?php if ($has_sale) { ?>
                    <span class="fs-5 text-muted text-decoration-line-through ms-2"><?= number_format($price, 0, ',', '.') ?>đ</span>
                    <span class="badge bg-danger ms-2">Tiết kiệm <?= number_format($price - $sale_price, 0, ',', '.') ?>đ</span>
                <?php } ?>
            </div>

            <!-- Mô tả sản phẩm -->
            <div class="mb-4">
                <h6 class="fw-bold text-muted text-uppercase small">Mô tả sản phẩm</h6>
                <p class="text-secondary" style="line-height: 1.7;">
                    <?= nl2br(htmlspecialchars($sp['description'] ?? 'Hoa tươi tự nhiên được tuyển chọn kỹ lưỡng mỗi sớm mai từ các nhà vườn uy tín. Bó hoa cẩn thận, giữ hoa tươi lâu và tỏa ngát hương thơm.')) ?>
                </p>
            </div>

            <!-- Form Mua hàng / Thêm vào giỏ hàng -->
            <form method="POST" action="index.php?act=themgiohang" class="mb-4">
                <input type="hidden" name="id" value="<?= $sp['id'] ?>">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <label class="form-label mb-0 fw-semibold">Số lượng:</label>
                    <div class="input-group" style="width: 130px;">
                        <button class="btn btn-outline-secondary" type="button" onclick="changeQty(-1)">-</button>
                        <input type="number" id="buy_qty" name="qty" class="form-control text-center" value="1" min="1" max="99">
                        <button class="btn btn-outline-secondary" type="button" onclick="changeQty(1)">+</button>
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" name="themgiohang" value="1" class="btn btn-bloom-outline btn-lg flex-fill">
                        <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ
                    </button>
                    <button type="submit" name="muangay" value="1" class="btn btn-bloom btn-lg flex-fill">
                        <i class="bi bi-lightning-charge me-1"></i> Mua ngay
                    </button>
                </div>
            </form>

            <!-- Cam kết dịch vụ -->
            <div class="card bg-light border-0 p-3 small text-muted rounded-3">
                <div class="d-flex gap-2 mb-2"><i class="bi bi-truck text-success fs-5"></i> <span>Giao hoa hỏa tốc trong 2 giờ tại khu vực nội thành.</span></div>
                <div class="d-flex gap-2 mb-2"><i class="bi bi-award text-primary fs-5"></i> <span>Cam kết hoa tươi trên 3 ngày hoặc đổi mẫu hoa mới 100%.</span></div>
                <div class="d-flex gap-2"><i class="bi bi-envelope-paper-heart text-danger fs-5"></i> <span>Tặng kèm thiệp chúc mừng và banner thiết kế sang trọng.</span></div>
            </div>
        </div>
    </div>

    <!-- KHU VỰC BÌNH LUẬN & ĐÁNH GIÁ SẢN PHẨM -->
    <div class="row mt-5 pt-4 border-top">
        <div class="col-lg-8">
            <h3 class="fw-bold mb-4" style="color: #604b41;">
                <i class="bi bi-chat-heart me-2"></i>Đánh giá & Bình luận (<?= $count_reviews ?>)
            </h3>

            <!-- Form gửi đánh giá -->
            <div class="card border-0 shadow-sm p-4 mb-4 rounded-3" style="background: #ffffff;">
                <h5 class="fw-semibold mb-3">Gửi đánh giá của bạn</h5>
                <form method="POST" action="index.php?act=guidanhgia">
                    <input type="hidden" name="product_id" value="<?= $sp['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label d-block fw-semibold small">Chọn số sao đánh giá:</label>
                        <div class="rating-input">
                            <input type="radio" id="star5" name="rating" value="5" checked><label for="star5" title="5 sao">★</label>
                            <input type="radio" id="star4" name="rating" value="4"><label for="star4" title="4 sao">★</label>
                            <input type="radio" id="star3" name="rating" value="3"><label for="star3" title="3 sao">★</label>
                            <input type="radio" id="star2" name="rating" value="2"><label for="star2" title="2 sao">★</label>
                            <input type="radio" id="star1" name="rating" value="1"><label for="star1" title="1 sao">★</label>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Họ và tên</label>
                            <input type="text" name="user_name" class="form-control" placeholder="Nguyễn Văn A" value="<?= htmlspecialchars($_SESSION['name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nội dung nhận xét</label>
                        <textarea name="content" rows="3" class="form-control" placeholder="Chia sẻ cảm nhận của bạn về chất lượng hoa, dịch vụ giao hàng..." required></textarea>
                    </div>

                    <button type="submit" name="guidanhgia" class="btn btn-bloom">
                        <i class="bi bi-send me-1"></i> Gửi đánh giá
                    </button>
                </form>
            </div>

            <!-- Danh sách đánh giá đã có -->
            <div class="reviews-list">
                <?php if (!empty($danhsach_danhgia)) { ?>
                    <?php foreach ($danhsach_danhgia as $dg) { ?>
                        <div class="card border-0 shadow-sm p-3 mb-3 rounded-3 bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong class="text-dark"><?= htmlspecialchars($dg['user_name'] ?? 'Khách hàng') ?></strong>
                                    <div class="star-rating small d-inline-block ms-2">
                                        <?php for ($i = 1; $i <= 5; $i++) {
                                            echo ($i <= (int)($dg['rating'] ?? 5)) ? '★' : '☆';
                                        } ?>
                                    </div>
                                </div>
                                <small class="text-muted"><?= !empty($dg['created_at']) ? date('d/m/Y H:i', strtotime($dg['created_at'])) : '' ?></small>
                            </div>
                            <p class="text-secondary mb-0"><?= nl2br(htmlspecialchars($dg['content'] ?? '')) ?></p>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="text-center py-4 text-muted bg-light rounded-3">
                        <i class="bi bi-chat-dots fs-3 d-block mb-1"></i>
                        <span>Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên chia sẻ cảm nhận nhé!</span>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Cột phụ: Gợi ý hoa tươi cùng danh mục -->
        <div class="col-lg-4">
            <h5 class="fw-bold mb-3" style="color: #604b41;">
                <i class="bi bi-flower2 me-1"></i>Gợi ý cho bạn
            </h5>
            <?php if (!empty($sp_lienquan)) { foreach ($sp_lienquan as $lq) { 
                $lq_price = (float)($lq['price'] ?? 0);
                $lq_sale = (float)($lq['sale_price'] ?? 0);
                $lq_has_sale = (!empty($lq['is_sale']) && $lq_sale > 0 && $lq_sale < $lq_price);
                $lq_current = $lq_has_sale ? $lq_sale : $lq_price;
            ?>
                <div class="card mb-3 border-0 shadow-sm hover-shadow transition">
                    <div class="row g-0 align-items-center">
                        <div class="col-4">
                            <a href="index.php?act=chitiettsp&id=<?= $lq['id'] ?>">
                                <img src="<?= $asset_dir ?>/image/sanpham/<?= htmlspecialchars($lq['image'] ?? '') ?>" 
                                     class="img-fluid rounded-start" 
                                     style="height: 85px; width: 100%; object-fit: cover;" 
                                     alt="<?= htmlspecialchars($lq['name'] ?? '') ?>"
                                     onerror="this.src='<?= $asset_dir ?>/image/banner.jpg'">
                            </a>
                        </div>
                        <div class="col-8">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1 small">
                                    <a href="index.php?act=chitiettsp&id=<?= $lq['id'] ?>" class="text-dark text-decoration-none fw-semibold product-title-link">
                                        <?= htmlspecialchars($lq['name'] ?? '') ?>
                                    </a>
                                </h6>
                                <div>
                                    <span class="price-text fw-bold small"><?= number_format($lq_current, 0, ',', '.') ?>đ</span>
                                    <?php if ($lq_has_sale) { ?>
                                        <span class="old-price small ms-1 text-muted text-decoration-line-through" style="font-size: 11px;"><?= number_format($lq_price, 0, ',', '.') ?>đ</span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }} else { ?>
                <div class="text-muted small fst-italic py-3 bg-light rounded-3 text-center">
                    Đang cập nhật thêm mẫu hoa cùng loại...
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
function changeQty(delta) {
    var input = document.getElementById('buy_qty');
    if (input) {
        var val = parseInt(input.value) || 1;
        val += delta;
        if (val < 1) val = 1;
        if (val > 99) val = 99;
        input.value = val;
    }
}
</script>
