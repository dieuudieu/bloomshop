<div class="container my-5">
    <!-- Tiêu đề -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1" style="color: #604b41;">
                <i class="bi bi-bag-check"></i> Lịch sử mua hàng
            </h2>
            <p class="text-muted small mb-0">Theo dõi trạng thái và quản lý các đơn hàng bạn đã đặt</p>
        </div>
        <a href="index.php?act=sanpham" class="btn btn-bloom btn-sm">
            <i class="bi bi-cart-plus"></i> Tiếp tục mua hàng
        </a>
    </div>

    <?php if (!empty($danhsach_donhang)) { ?>
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th>Người nhận</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($danhsach_donhang as $dh) { 
                            $st = $dh['status'];
                            $badge_class = 'bg-secondary';
                            $status_text = 'Không xác định';

                            switch ($st) {
                                case 'cho_xac_nhan':
                                    $badge_class = 'bg-warning text-dark';
                                    $status_text = 'Chờ xác nhận';
                                    break;
                                case 'xac_nhan':
                                    $badge_class = 'bg-info text-dark';
                                    $status_text = 'Đã xác nhận';
                                    break;
                                case 'dang_giao':
                                    $badge_class = 'bg-primary';
                                    $status_text = 'Đang giao hàng';
                                    break;
                                case 'da_giao':
                                    $badge_class = 'bg-info text-white';
                                    $status_text = 'Đã giao hàng';
                                    break;
                                case 'thanh_cong':
                                    $badge_class = 'bg-success';
                                    $status_text = 'Thành công';
                                    break;
                                case 'yeu_cau_tra': $badge_class='bg-warning text-dark'; $status_text='Chờ duyệt trả hàng'; break;
                                case 'da_tra_hang': $badge_class='bg-secondary'; $status_text='Đã trả hàng'; break;
                                case 'tu_choi_tra': $badge_class='bg-danger'; $status_text='Từ chối trả hàng'; break;
                                case 'da_huy':
                                    $badge_class = 'bg-danger';
                                    $status_text = 'Đã hủy';
                                    break;
                            }
                        ?>
                            <tr>
                                <td class="fw-bold">#<?= $dh['id'] ?></td>
                                <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($dh['created_at'])) ?></td>
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($dh['customer_name']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($dh['customer_phone']) ?></small>
                                </td>
                                <td>
                                    <span class="fw-bold price-text"><?= number_format($dh['total_price'], 0, ',', '.') ?>đ</span>
                                    <?php if ($dh['discount_amount'] > 0) { ?>
                                        <div class="small text-success">Đã giảm: <?= number_format($dh['discount_amount'], 0, ',', '.') ?>đ</div>
                                    <?php } ?>
                                </td>
                                <td>
                                    <span class="badge <?= $badge_class ?> px-2 py-1"><?= $status_text ?></span>
                                </td>
                                
                                <td style="min-width: 280px; width: 300px;">
                                    <div class="d-flex flex-column align-items-center gap-2">

                                        <!-- Hàng 1: Nút xem đơn hàng -->
                                        <div class="d-flex gap-2 justify-content-center w-100">
                                            <a href="index.php?act=theodoigiaohang&id=<?= (int)$dh['id'] ?>"
                                            class="btn btn-outline-primary btn-sm text-nowrap">
                                                <i class="bi bi-truck"></i> Tiến trình
                                            </a>

                                            <a href="index.php?act=chitietdonhang&id=<?= (int)$dh['id'] ?>"
                                            class="btn btn-outline-secondary btn-sm text-nowrap">
                                                <i class="bi bi-eye"></i> Chi tiết
                                            </a>
                                        </div>

                                        <!-- Hàng 2: Yêu cầu trả hàng -->
                                        <?php if (in_array($st, ['da_giao', 'thanh_cong', 'tu_choi_tra'], true)): ?>
                                            <form method="POST"
                                                action="index.php?act=yeucautrahang"
                                                class="w-100"
                                                onsubmit="return confirm('Gửi yêu cầu trả hàng cho Admin?')">

                                                <input type="hidden"
                                                    name="order_id"
                                                    value="<?= (int)$dh['id'] ?>">

                                                <input type="text"
                                                    name="return_reason"
                                                    minlength="5"
                                                    maxlength="1000"
                                                    placeholder="Nhập lý do trả hàng..."
                                                    class="form-control form-control-sm mb-2"
                                                    required>

                                                <button type="submit"
                                                        class="btn btn-outline-warning btn-sm w-100">
                                                    <i class="bi bi-arrow-return-left"></i>
                                                    Yêu cầu trả hàng
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Hủy đơn hàng -->
                                        <?php if ($st == 'cho_xac_nhan'): ?>
                                            <form method="POST"
                                                action="index.php?act=huydonhang"
                                                class="w-100"
                                                onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">

                                                <input type="hidden"
                                                    name="order_id"
                                                    value="<?= (int)$dh['id'] ?>">

                                                <button type="submit"
                                                        name="huydonhang"
                                                        class="btn btn-outline-danger btn-sm w-100">
                                                    <i class="bi bi-x-circle"></i> Hủy đơn hàng
                                                </button>
                                            </form>

                                        <?php elseif (in_array($st, ['xac_nhan', 'dang_giao'], true)): ?>
                                            <span class="badge bg-light text-muted border">
                                                <i class="bi bi-lock-fill me-1"></i>
                                                Không thể hủy
                                            </span>
                                        <?php endif; ?>

                                    </div>
                                </td>

                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } else { ?>
        <div class="card border-0 shadow-sm p-5 text-center rounded-4 bg-white">
            <i class="bi bi-bag-x fs-1 text-muted mb-3" style="font-size: 3.5rem !important;"></i>
            <h4 class="fw-bold text-secondary">Bạn chưa có đơn hàng nào</h4>
            <p class="text-muted">Khám phá các bó hoa tuyệt đẹp tại BloomShop ngay hôm nay!</p>
            <div class="mt-2">
                <a href="index.php?act=sanpham" class="btn btn-bloom">Xem danh mục hoa</a>
            </div>
        </div>
    <?php } ?>
</div>
