<div class="container my-5" style="max-width: 850px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?act=lichsudonhang">Lịch sử mua hàng</a></li>
            <li class="breadcrumb-item active">Chi tiết đơn hàng #<?= $order['id'] ?></li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="fw-bold mb-0" style="color: #604b41;">
            Chi tiết đơn hàng #<?= $order['id'] ?>
        </h2>
        <div class="d-flex gap-2">
            <a href="index.php?act=theodoigiaohang&id=<?= $order['id'] ?>" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-truck"></i> Tiến trình giao
            </a>
            <a href="/bloomshop/invoices/hoa_don_<?= $order['id'] ?>.html" target="_blank" class="btn btn-bloom-outline btn-sm">
                <i class="bi bi-printer"></i> In hóa đơn
            </a>
        </div>
    </div>

    <!-- Trạng thái đơn -->
    <div class="card border-0 shadow-sm p-3 mb-4 bg-white rounded-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="text-muted small">Thời gian đặt:</span>
                <span class="fw-semibold"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
            </div>
            <div>
                <span class="text-muted small me-2">Trạng thái hiện tại:</span>
                <?php 
                    $st = $order['status'];
                    $badge = 'bg-secondary'; $txt = $st;
                    if ($st == 'cho_xac_nhan') { $badge = 'bg-warning text-dark'; $txt = 'Chờ xác nhận'; }
                    elseif ($st == 'xac_nhan') { $badge = 'bg-info text-dark'; $txt = 'Đã xác nhận'; }
                    elseif ($st == 'dang_giao') { $badge = 'bg-primary'; $txt = 'Đang giao hàng'; }
                    elseif ($st == 'da_giao') { $badge = 'bg-info text-white'; $txt = 'Đã giao hàng'; }
                    elseif ($st == 'thanh_cong') { $badge = 'bg-success'; $txt = 'Thành công'; }
                    elseif ($st == 'da_huy') { $badge = 'bg-danger'; $txt = 'Đã hủy'; }
                ?>
                <span class="badge <?= $badge ?> px-3 py-2 fs-6"><?= $txt ?></span>
            </div>
        </div>
    </div>

    <!-- Thông tin người nhận -->
    <div class="card border-0 shadow-sm p-4 mb-4 bg-white rounded-3">
        <h5 class="fw-bold mb-3" style="color: #604b41;">Thông tin giao nhận</h5>
        <div class="row g-2 small">
            <div class="col-sm-6"><strong>Người nhận:</strong> <?= htmlspecialchars($order['customer_name']) ?></div>
            <div class="col-sm-6"><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['customer_phone']) ?></div>
            <div class="col-sm-6"><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></div>
            <div class="col-sm-6"><strong>Phương thức:</strong> <?= htmlspecialchars($order['payment_method']) ?></div>
            <div class="col-12"><strong>Địa chỉ nhận hoa:</strong> <?= htmlspecialchars($order['customer_address']) ?></div>
            <?php if (!empty($order['note'])) { ?>
                <div class="col-12 mt-1"><strong>Ghi chú:</strong> <em class="text-secondary"><?= htmlspecialchars($order['note']) ?></em></div>
            <?php } ?>
        </div>
    </div>

    <!-- Danh sách sản phẩm -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white mb-4">
        <div class="p-3 bg-light fw-bold">Danh sách hoa đã đặt</div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="small text-muted">
                    <tr>
                        <th>Sản phẩm</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-end">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $it) { ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <?php if (!empty($it['product_image'])) { ?>
                                        <img src="/bloomshop/view/image/sanpham/<?= htmlspecialchars($it['product_image']) ?>" width="55" height="55" class="rounded object-fit-cover" alt="" onerror="this.src='/bloomshop/view/image/banner.jpg'">
                                    <?php } ?>
                                    <div class="fw-semibold"><?= htmlspecialchars($it['product_name']) ?></div>
                                </div>
                            </td>
                            <td class="text-center"><?= $it['quantity'] ?></td>
                            <td class="text-end"><?= number_format($it['price'], 0, ',', '.') ?>đ</td>
                            <td class="text-end fw-bold"><?= number_format($it['total'], 0, ',', '.') ?>đ</td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end">Tạm tính:</td>
                        <td class="text-end fw-semibold"><?= number_format($order['subtotal'], 0, ',', '.') ?>đ</td>
                    </tr>
                    <?php if ($order['discount_amount'] > 0) { ?>
                        <tr class="text-success">
                            <td colspan="3" class="text-end">Voucher giảm giá (<?= htmlspecialchars($order['voucher_code']) ?>):</td>
                            <td class="text-end fw-bold">-<?= number_format($order['discount_amount'], 0, ',', '.') ?>đ</td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="3" class="text-end fs-6 fw-bold">TỔNG CỘNG:</td>
                        <td class="text-end fs-5 fw-bold price-text"><?= number_format($order['total_price'], 0, ',', '.') ?>đ</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Tác vụ hủy đơn nếu đang ở trạng thái chờ xác nhận -->
    <?php if ($order['status'] == 'cho_xac_nhan') { ?>
        <div class="card border-0 shadow-sm p-4 bg-white rounded-3 text-center">
            <h6 class="text-danger fw-bold mb-2">Bạn muốn hủy đơn hàng này?</h6>
            <p class="small text-muted mb-3">Đơn hàng đang ở trạng thái Chờ xác nhận, bạn có thể chủ động hủy mà không mất phí.</p>
            <form method="POST" action="index.php?act=huydonhang" onsubmit="return confirm('Xác nhận bạn muốn hủy đơn hàng này?')">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <button type="submit" name="huydonhang" class="btn btn-outline-danger">
                    <i class="bi bi-x-circle me-1"></i> Hủy đơn hàng ngay
                </button>
            </form>
        </div>
    <?php } ?>
</div>
