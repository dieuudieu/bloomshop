<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1" style="color: #604b41;">
                <i class="bi bi-ticket-perforated-fill"></i> Quản lý Voucher & Khuyến mãi
            </h2>
            <p class="text-muted small mb-0">Quản lý số lượng, thời hạn và mức chiết khấu của mã giảm giá</p>
        </div>
        <a href="index.php?act=themvoucher" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> + Tạo Voucher mới
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Mã Voucher</th>
                        <th>Mức giảm</th>
                        <th>Đơn tối thiểu</th>
                        <th>Số lượng</th>
                        <th>Đã dùng</th>
                        <th>Còn lại</th>
                        <th>Thời hạn</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($danhsach_vc)) { foreach ($danhsach_vc as $vc) { 
                        $is_expired = (strtotime($vc['expiry_date']) < strtotime(date('Y-m-d')));
                        $remaining = max(0, $vc['quantity'] - $vc['used_count']);
                    ?>
                        <tr>
                            <td>#<?= $vc['id'] ?></td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 fs-6 font-monospace">
                                    <?= htmlspecialchars($vc['code']) ?>
                                </span>
                            </td>
                            <td class="fw-bold text-success">
                                <?= ($vc['discount_type'] == 'percent') ? (int)$vc['discount_value'] . '%' : number_format($vc['discount_value'], 0, ',', '.') . 'đ' ?>
                            </td>
                            <td><?= number_format($vc['min_order_value'], 0, ',', '.') ?>đ</td>
                            <td><?= $vc['quantity'] ?></td>
                            <td><?= $vc['used_count'] ?></td>
                            <td>
                                <span class="fw-semibold <?= ($remaining == 0) ? 'text-danger' : 'text-primary' ?>">
                                    <?= $remaining ?>
                                </span>
                            </td>
                            <td>
                                <span class="<?= $is_expired ? 'text-danger fw-bold' : '' ?>">
                                    <?= date('d/m/Y', strtotime($vc['expiry_date'])) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($vc['status'] == 0) { ?>
                                    <span class="badge bg-secondary">Tạm khóa</span>
                                <?php } elseif ($is_expired) { ?>
                                    <span class="badge bg-danger">Hết hạn</span>
                                <?php } elseif ($remaining == 0) { ?>
                                    <span class="badge bg-warning text-dark">Hết lượt dùng</span>
                                <?php } else { ?>
                                    <span class="badge bg-success">Khả dụng</span>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <a href="index.php?act=suavoucher&id=<?= $vc['id'] ?>" class="btn btn-warning btn-sm" title="Sửa voucher">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </a>
                                <form method="POST" action="index.php?act=xoavoucher" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa voucher này?')">
                                    <input type="hidden" name="id" value="<?= $vc['id'] ?>">
                                    <button type="submit" name="xoavoucher" class="btn btn-danger btn-sm" title="Xóa voucher">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php }} else { ?>
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">Chưa có mã giảm giá nào.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
