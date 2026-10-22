<div class="container my-5">
    <h2 class="fw-bold mb-4" style="color: #604b41;">
        <i class="bi bi-cart3"></i> Giỏ hàng của bạn
    </h2>

    <?php if (!empty($_SESSION['cart'])) { 
        $subtotal = 0;
        foreach ($_SESSION['cart'] as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        // Tính giảm giá nếu đã áp voucher
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
        <div class="row g-4">
            <!-- Danh sách sản phẩm trong giỏ -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-center" style="width: 140px;">Số lượng</th>
                                    <th class="text-end" style="width: 130px;">Đơn giá</th>
                                    <th class="text-end" style="width: 130px;">Thành tiền</th>
                                    <th class="text-center" style="width: 60px;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['cart'] as $pid => $sp) { 
                                    $item_total = $sp['price'] * $sp['qty'];
                                ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="/bloomshop/view/image/sanpham/<?= htmlspecialchars($sp['image']) ?>" width="65" height="65" class="rounded object-fit-cover" alt="" onerror="this.src='/bloomshop/view/image/banner.jpg'">
                                                <div>
                                                    <a href="index.php?act=chitiettsp&id=<?= $pid ?>" class="text-dark fw-semibold text-decoration-none d-block">
                                                        <?= htmlspecialchars($sp['name']) ?>
                                                    </a>
                                                    <small class="text-muted">Mã SP: #<?= $pid ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <form method="POST" action="index.php?act=capnhatgiohang" class="d-flex align-items-center justify-content-center">
                                                <input type="hidden" name="id" value="<?= $pid ?>">
                                                <div class="input-group input-group-sm">
                                                    <button class="btn btn-outline-secondary" type="submit" name="action" value="decrease">-</button>
                                                    <input type="text" class="form-control text-center px-1" value="<?= $sp['qty'] ?>" readonly style="max-width: 45px;">
                                                    <button class="btn btn-outline-secondary" type="submit" name="action" value="increase">+</button>
                                                </div>
                                            </form>
                                        </td>
                                        <td class="text-end price-text"><?= number_format($sp['price'], 0, ',', '.') ?>đ</td>
                                        <td class="text-end fw-bold text-dark"><?= number_format($item_total, 0, ',', '.') ?>đ</td>
                                        <td class="text-center">
                                            <a href="index.php?act=xoagiohang&id=<?= $pid ?>" class="text-danger" title="Xóa món này" onclick="return confirm('Bạn muốn xóa sản phẩm này khỏi giỏ?')">
                                                <i class="bi bi-trash fs-5"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="index.php?act=sanpham" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Tiếp tục chọn hoa
                        </a>
                        <a href="index.php?act=xoatatcagiohang" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bạn có chắc muốn làm trống giỏ hàng?')">
                            <i class="bi bi-trash3"></i> Xóa tất cả giỏ hàng
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cột thanh toán & Áp mã Voucher -->
            <div class="col-lg-4">
                <!-- KHỐI VOUCHER -->
                <div class="card border-0 shadow-sm p-4 rounded-3 mb-4 bg-white">
                    <h5 class="fw-bold mb-3" style="color: #604b41;">
                        <i class="bi bi-ticket-perforated text-bloom"></i> Áp dụng Voucher
                    </h5>
                    
                    <?php if ($voucher) { ?>
                        <div class="alert alert-success d-flex justify-content-between align-items-center py-2 px-3 mb-3">
                            <div>
                                <strong class="d-block">Mã: <?= htmlspecialchars($voucher['code']) ?></strong>
                                <small class="text-success">Giảm: -<?= number_format($discount, 0, ',', '.') ?>đ</small>
                            </div>
                            <a href="index.php?act=bovoucher" class="btn btn-sm btn-outline-danger" title="Bỏ voucher">Bỏ chọn</a>
                        </div>
                    <?php } else { ?>
                        <form method="POST" action="index.php?act=apvoucher" class="mb-3">
                            <div class="input-group">
                                <input type="text" name="voucher_code" class="form-control text-uppercase" placeholder="Nhập mã giảm giá..." required>
                                <button class="btn btn-bloom" type="submit" name="apvoucher">Áp dụng</button>
                            </div>
                        </form>
                    <?php } ?>

                    <!-- Gợi ý Voucher khả dụng -->
                    <?php if (!empty($ds_voucher)) { ?>
                        <div class="small">
                            <span class="text-muted fw-semibold">Mã giảm giá đang có:</span>
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                <?php foreach ($ds_voucher as $vc) { ?>
                                    <span class="badge bg-light text-dark border p-2" style="cursor: pointer;" onclick="document.querySelector('input[name=voucher_code]').value='<?= $vc['code'] ?>';">
                                        🏷️ <strong><?= $vc['code'] ?></strong>: <?= ($vc['discount_type']=='percent') ? (int)$vc['discount_value'].'%' : number_format($vc['discount_value'], 0, ',', '.').'đ' ?>
                                    </span>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <!-- TỔNG KẾT TIỀN ĐƠN HÀNG -->
                <div class="card border-0 shadow-sm p-4 rounded-3 bg-white">
                    <h5 class="fw-bold mb-3" style="color: #604b41;">Tóm tắt đơn hàng</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tạm tính:</span>
                        <span class="fw-semibold"><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
                    </div>

                    <?php if ($voucher && $discount > 0) { ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Giảm giá voucher:</span>
                            <span class="fw-bold">-<?= number_format($discount, 0, ',', '.') ?>đ</span>
                        </div>
                    <?php } ?>

                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fs-5 fw-bold">Tổng thanh toán:</span>
                        <span class="fs-4 fw-bold price-text"><?= number_format($final_total, 0, ',', '.') ?>đ</span>
                    </div>

                    <a href="index.php?act=thanhtoan" class="btn btn-bloom w-100 py-2 fs-6 fw-bold">
                        TIẾN HÀNH ĐẶT HÀNG <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <!-- Giỏ hàng rỗng -->
        <div class="card border-0 shadow-sm p-5 text-center rounded-4 bg-white">
            <div class="mb-3">
                <i class="bi bi-cart-x fs-1 text-muted" style="font-size: 4rem !important;"></i>
            </div>
            <h4 class="fw-bold text-secondary">Giỏ hàng của bạn đang trống</h4>
            <p class="text-muted">Hãy chọn ngay những đóa hoa tươi rạng rỡ để gửi trao yêu thương nhé!</p>
            <div class="mt-3">
                <a href="index.php?act=sanpham" class="btn btn-bloom px-4 py-2">
                    <i class="bi bi-flower1"></i> Mua sắm ngay
                </a>
            </div>
        </div>
    <?php } ?>
</div>
