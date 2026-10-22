<div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?act=trangchu">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="index.php?act=sanpham">Sản phẩm</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($sp['name']) ?></li>
        </ol>
    </nav>

    <?php 
        $has_sale = (!empty($sp['is_sale']) && !empty($sp['sale_price']) && $sp['sale_price'] < $sp['price']);
        $current_price = $has_sale ? $sp['sale_price'] : $sp['price'];
        $discount_pct = $has_sale ? round((($sp['price'] - $sp['sale_price']) / $sp['price']) * 100) : 0;
    ?>

    <div class="row g-5">
        <!-- Ảnh sản phẩm -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                <?php if ($has_sale) { ?>
                    <span class="badge-sale fs-6 px-3 py-2">-<?= $discount_pct ?>% GIẢM</span>
                <?php } ?>
                <img src="/bloomshop/view/image/sanpham/<?= htmlspecialchars($sp['image']) ?>" class="img-fluid w-100" style="max-height: 480px; object-fit: cover;" alt="<?= htmlspecialchars($sp['name']) ?>" onerror="this.src='/bloomshop/view/image/banner.jpg'">
            </div>
        </div>

        <!-- Thông tin sản phẩm & Đặt hàng -->
        <div class="col-md-6">
            <div class="badge bg-light text-secondary border mb-2"><?= htmlspecialchars($sp['tendanhmuc'] ?? 'Hoa tươi') ?></div>
            <h1 class="fw-bold mb-3" style="color: #604b41;"><?= htmlspecialchars($sp['name']) ?></h1>

            <!-- Đánh giá sao tóm tắt -->
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="star-rating">
                    <?php 
                    $avg = $avg_rating ? round($avg_rating, 1) : 5.0;
                    for ($s = 1; $s <= 5; $s++) {
                        echo ($s <= round($avg)) ? '★' : '☆';
                    }
                    ?>
                </div>
                <span class="fw-bold text-dark"><?= number_format($avg, 1) ?></span>
                <span class="text-muted small">(<?= count($danhsach_danhgia) ?> đánh giá)</span>
            </div>

            <!-- Giá -->
            <div class="mb-4 p-3 rounded-3" style="background-color: #fdf8f5; border: 1px dashed #ebdcd5;">
                <span class="fs-2 fw-bold price-text"><?= number_format($current_price, 0, ',', '.') ?>đ</span>
                <?php if ($has_sale) { ?>
                    <span class="fs-5 text-muted text-decoration-line-through ms-2"><?= number_format($sp['price'], 0, ',', '.') ?>đ</span>
                    <span class="badge bg-danger ms-2">Tiết kiệm <?= number_format($sp['price'] - $sp['sale_price'], 0, ',', '.') ?>đ</span>
                <?php } ?>
            </div>

            <!-- Mô tả -->
            <div class="mb-4">
                <h6 class="fw-bold text-muted text-uppercase small">Mô tả sản phẩm</h6>
                <p class="text-secondary" style="line-height: 1.7;">
                    <?= nl2br(htmlspecialchars($sp['description'] ?? 'Hoa tươi tự nhiên được tuyển chọn kỹ lưỡng mỗi sớm mai từ các nhà vườn uy tín. Gói hoa cẩn thận, giữ hoa tươi lâu và thơm ngát.')) ?>
                </p>
            </div>

            <!-- Form Mua hàng / Thêm vào giỏ -->
            <form method="POST" action="index.php?act=themgiohang" class="mb-4">
                <input type="hidden" name="id" value="<?= $sp['id'] ?>">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <label class="form-label mb-0 fw-semibold">Số lượng:</label>
                    <div class="input-group" style="width: 130px;">
                        <button class="btn btn-outline-secondary" type="button" onclick="let q=document.getElementById('buy_qty'); if(q.value>1) q.value--;">-</button>
                        <input type="number" id="buy_qty" name="qty" class="form-control text-center" value="1" min="1" max="99">
                        <button class="btn btn-outline-secondary" type="button" onclick="let q=document.getElementById('buy_qty'); q.value++;">+</button>
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" name="themgiohang" class="btn btn-bloom-outline btn-lg flex-fill">
                        <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ
                    </button>
                    <button type="submit" name="muangay" value="1" class="btn btn-bloom btn-lg flex-fill">
                        <i class="bi bi-lightning-charge me-1"></i> Mua ngay
                    </button>
                </div>
            </form>

            <div class="card bg-light border-0 p-3 small text-muted">
                <div class="d-flex gap-2 mb-2"><i class="bi bi-truck text-success fs-5"></i> <span>Giao hoa hỏa tốc trong 2 giờ tại nội thành.</span></div>
                <div class="d-flex gap-2 mb-2"><i class="bi bi-award text-primary fs-5"></i> <span>Cam kết hoa tươi trên 3 ngày hoặc 1 đổi 1.</span></div>
                <div class="d-flex gap-2"><i class="bi bi-envelope-paper-heart text-danger fs-5"></i> <span>Tặng kèm thiệp chúc mừng và banner thiết kế riêng.</span></div>
            </div>
        </div>
    </div>

    <!-- KHU VỰC BÌNH LUẬN & ĐÁNH GIÁ SẢN PHẨM -->
    <div class="row mt-5 pt-4 border-top">
        <div class="col-lg-8">
            <h3 class="fw-bold mb-4" style="color: #604b41;">Đánh giá & Bình luận (<?= count($danhsach_danhgia) ?>)</h3>

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
                                    <strong class="text-dark"><?= htmlspecialchars($dg['user_name']) ?></strong>
                                    <div class="star-rating small d-inline-block ms-2">
                                        <?php for ($i = 1; $i <= 5; $i++) {
                                            echo ($i <= $dg['rating']) ? '★' : '☆';
                                        } ?>
                                    </div>
                                </div>
                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($dg['created_at'])) ?></small>
                            </div>
                            <p class="text-secondary mb-0"><?= nl2br(htmlspecialchars($dg['content'])) ?></p>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <p class="text-muted text-center py-4">Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên chia sẻ cảm nhận nhé!</p>
                <?php } ?>
            </div>
        </div>

        <!-- Cột phụ: Sản phẩm tương tự -->
        <div class="col-lg-4">
            <h5 class="fw-bold mb-3" style="color: #604b41;">Gợi ý cho bạn</h5>
            <?php if (!empty($sp_lienquan)) { foreach ($sp_lienquan as $lq) { ?>
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="row g-0 align-items-center">
                        <div class="col-4">
                            <img src="/bloomshop/view/image/sanpham/<?= htmlspecialchars($lq['image']) ?>" class="img-fluid rounded-start" style="height: 80px; width: 100%; object-fit: cover;" alt="<?= htmlspecialchars($lq['name']) ?>">
                        </div>
                        <div class="col-8">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1 small">
                                    <a href="index.php?act=chitiettsp&id=<?= $lq['id'] ?>" class="text-dark text-decoration-none">
                                        <?= htmlspecialchars($lq['name']) ?>
                                    </a>
                                </h6>
                                <span class="price-text small"><?= number_format($lq['sale_price'] ?: $lq['price'], 0, ',', '.') ?>đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }} ?>
        </div>
    </div>
</div>
