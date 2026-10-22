<div class="container my-5 form-page" style="max-width: 900px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?act=giohang">Giỏ hàng</a></li>
            <li class="breadcrumb-item active">Thanh toán</li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-4" style="color: #604b41;">
        <i class="bi bi-credit-card"></i> Thông tin thanh toán & Đặt hàng
    </h2>

    <?php 
        $subtotal = 0;
        foreach ($_SESSION['cart'] as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        $discount = 0;
        $voucher = $_SESSION['voucher'] ?? null;
        if ($voucher) {
            if ($voucher['discount_type'] == 'percent') {
                $discount = ($subtotal * $voucher['discount_value']) / 100;
            } else {
                $discount = $voucher['discount_value'];
            }
            if ($discount > $subtotal) $discount = $subtotal;
        }
        $final_total = max(0, $subtotal - $discount);
    ?>

    <form method="POST" action="index.php?act=xacnhandathang">
        <div class="row g-4">
            <!-- Cột thông tin người nhận -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm p-4 rounded-3 bg-white mb-4">
                    <h5 class="fw-bold mb-3" style="color: #604b41;">1. Địa chỉ nhận hoa</h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Họ và tên người nhận <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" class="form-control" value="<?= htmlspecialchars($_SESSION['name'] ?? '') ?>" required placeholder="Ví dụ: Nguyễn Văn A">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" name="customer_phone" class="form-control" value="<?= htmlspecialchars($current_user['phone'] ?? '') ?>" required placeholder="0901234567">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Email nhận hóa đơn <span class="text-danger">*</span></label>
                            <input type="email" name="customer_email" class="form-control" value="<?= htmlspecialchars($current_user['email'] ?? '') ?>" required placeholder="email@gmail.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Địa chỉ giao hàng chi tiết <span class="text-danger">*</span></label>
                        <input type="text" name="customer_address" class="form-control" value="<?= htmlspecialchars($current_user['address'] ?? '') ?>" required placeholder="Số nhà, tên đường, phường/xã, quận/huyện...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ghi chú đơn hàng / Lời nhắn viết thiệp</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Ghi chú thời gian nhận, thông điệp viết thiệp tặng hoa..."></textarea>
                    </div>
                </div>

                <div class="card border-0 shadow-sm p-4 rounded-3 bg-white">
                    <h5 class="fw-bold mb-3" style="color: #604b41;">2. Phương thức thanh toán</h5>
                    
                    <div class="form-check p-3 border rounded mb-2 bg-light">
                        <input class="form-check-input" type="radio" name="payment_method" id="pay_cod" value="COD" checked>
                        <label class="form-check-label fw-semibold" for="pay_cod">
                            <i class="bi bi-cash-coin text-success fs-5 me-2"></i> Thanh toán khi nhận hàng (COD)
                        </label>
                        <div class="small text-muted ms-4 ps-2">Nhận hoa tận tay, kiểm tra tươi đẹp rồi mới thanh toán tiền mặt.</div>
                    </div>

                    <div class="form-check p-3 border rounded">
                        <input class="form-check-input" type="radio" name="payment_method" id="pay_bank" value="Chuyển khoản ngân hàng">
                        <label class="form-check-label fw-semibold" for="pay_bank">
                            <i class="bi bi-bank text-primary fs-5 me-2"></i> Chuyển khoản ngân hàng (VietQR / STK)
                        </label>
                        <div class="small text-muted ms-4 ps-2 mt-1">
                            STK: <strong>190368889999</strong> - MB Bank<br>
                            Chủ tài khoản: <strong>BLOOMSHOP FLOWERS</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột tóm tắt đơn hàng -->
            <div class="col-md-5">
                <div class="card border-0 shadow-sm p-4 rounded-3 bg-white sticky-top" style="top: 90px;">
                    <h5 class="fw-bold mb-3" style="color: #604b41;">Đơn hàng của bạn (<?= count($_SESSION['cart']) ?> món)</h5>

                    <div class="order-items-list mb-3" style="max-height: 260px; overflow-y: auto;">
                        <?php foreach ($_SESSION['cart'] as $sp) { ?>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="/bloomshop/view/image/sanpham/<?= htmlspecialchars($sp['image']) ?>" width="45" height="45" class="rounded object-fit-cover" alt="" onerror="this.src='/bloomshop/view/image/banner.jpg'">
                                    <div>
                                        <div class="small fw-semibold"><?= htmlspecialchars($sp['name']) ?></div>
                                        <div class="small text-muted">x <?= $sp['qty'] ?></div>
                                    </div>
                                </div>
                                <div class="small fw-bold text-end">
                                    <?= number_format($sp['price'] * $sp['qty'], 0, ',', '.') ?>đ
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tạm tính:</span>
                        <span class="fw-semibold"><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
                    </div>

                    <?php if ($voucher && $discount > 0) { ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Voucher (<?= htmlspecialchars($voucher['code']) ?>):</span>
                            <span class="fw-bold">-<?= number_format($discount, 0, ',', '.') ?>đ</span>
                        </div>
                    <?php } ?>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Phí giao hàng:</span>
                        <span class="text-success fw-semibold">Miễn phí</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="fs-5 fw-bold">Tổng thanh toán:</span>
                        <span class="fs-4 fw-bold price-text"><?= number_format($final_total, 0, ',', '.') ?>đ</span>
                    </div>

                    <button type="submit" name="dathang" class="btn btn-bloom w-100 py-2 fs-6 fw-bold">
                        <i class="bi bi-bag-check-fill me-1"></i> XÁC NHẬN ĐẶT HÀNG
                    </button>
                    
                    <p class="text-center text-muted small mt-3 mb-0">
                        <i class="bi bi-shield-check text-success"></i> Hóa đơn VAT điện tử sẽ được tự động gửi qua email và thông báo đến Quản trị viên.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
