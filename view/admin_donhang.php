<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1" style="color: #604b41;">
                <i class="bi bi-receipt-cutoff"></i> Quản lý Đơn hàng
            </h2>
            <p class="text-muted small mb-0">
                Quy trình chuyển tiếp trạng thái: 
                <strong>Chờ xác nhận &rarr; Xác nhận đơn &rarr; Đang giao &rarr; Đã giao &rarr; Thành công</strong>
            </p>
        </div>

        <!-- Bộ lọc trạng thái đơn -->
        <form method="GET" action="index.php" class="d-flex gap-2 align-items-center">
            <input type="hidden" name="act" value="quanlydonhang">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="cho_xac_nhan" <?= (($_GET['status'] ?? '') == 'cho_xac_nhan') ? 'selected' : '' ?>>Chờ xác nhận</option>
                <option value="xac_nhan" <?= (($_GET['status'] ?? '') == 'xac_nhan') ? 'selected' : '' ?>>Xác nhận đơn hàng</option>
                <option value="dang_giao" <?= (($_GET['status'] ?? '') == 'dang_giao') ? 'selected' : '' ?>>Đang giao</option>
                <option value="da_giao" <?= (($_GET['status'] ?? '') == 'da_giao') ? 'selected' : '' ?>>Đã giao</option>
                <option value="thanh_cong" <?= (($_GET['status'] ?? '') == 'thanh_cong') ? 'selected' : '' ?>>Thành công</option>
                <option value="da_huy" <?= (($_GET['status'] ?? '') == 'da_huy') ? 'selected' : '' ?>>Đã hủy</option>
            </select>
        </form>
    </div>

    <!-- Thanh chuyển tiếp nhanh gợi ý -->
    <div class="card border-0 shadow-sm p-3 mb-4 bg-light rounded-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 small">
            <span class="fw-bold text-secondary">Quy trình xử lý chuẩn:</span>
            <span class="badge bg-warning text-dark">1. Chờ xác nhận</span> &rarr;
            <span class="badge bg-info text-dark">2. Xác nhận đơn</span> &rarr;
            <span class="badge bg-primary">3. Đang giao</span> &rarr;
            <span class="badge bg-info text-white">4. Đã giao</span> &rarr;
            <span class="badge bg-success">5. Thành công (Tích điểm VIP)</span>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Khách hàng</th>
                        <th>SĐT & Địa chỉ</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái hiện tại</th>
                        <th style="min-width: 200px;">Cập nhật trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($danhsach_dh)) { foreach ($danhsach_dh as $dh) { 
                        $st = $dh['status'];
                    ?>
                        <tr>
                            <td class="fw-bold">#<?= $dh['id'] ?></td>
                            <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($dh['created_at'])) ?></td>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($dh['customer_name']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($dh['customer_email']) ?></small>
                            </td>
                            <td>
                                <div><i class="bi bi-telephone text-muted me-1"></i> <?= htmlspecialchars($dh['customer_phone']) ?></div>
                                <div class="small text-muted" style="max-width: 200px;"><?= htmlspecialchars($dh['customer_address']) ?></div>
                            </td>
                            <td>
                                <span class="fw-bold price-text"><?= number_format($dh['total_price'], 0, ',', '.') ?>đ</span>
                                <div class="small text-muted"><?= htmlspecialchars($dh['payment_method']) ?></div>
                            </td>
                            <td>
                                <?php 
                                    $badge = 'bg-secondary'; $txt = $st;
                                    if ($st == 'cho_xac_nhan') { $badge = 'bg-warning text-dark'; $txt = 'Chờ xác nhận'; }
                                    elseif ($st == 'xac_nhan') { $badge = 'bg-info text-dark'; $txt = 'Xác nhận đơn hàng'; }
                                    elseif ($st == 'dang_giao') { $badge = 'bg-primary'; $txt = 'Đang giao'; }
                                    elseif ($st == 'da_giao') { $badge = 'bg-info text-white'; $txt = 'Đã giao'; }
                                    elseif ($st == 'thanh_cong') { $badge = 'bg-success'; $txt = 'Thành công'; }
                                    elseif ($st == 'da_huy') { $badge = 'bg-danger'; $txt = 'Đã hủy'; }
                                ?>
                                <span class="badge <?= $badge ?> px-2 py-1"><?= $txt ?></span>
                            </td>
                            <td>
                                <form method="POST" action="index.php?act=doitrangthaidonhang" class="d-flex gap-1 align-items-center">
                                    <input type="hidden" name="order_id" value="<?= $dh['id'] ?>">
                                    <select name="new_status" class="form-select form-select-sm" style="font-size: 13px;">
                                        <option value="cho_xac_nhan" <?= ($st == 'cho_xac_nhan') ? 'selected' : '' ?>>Chờ xác nhận</option>
                                        <option value="xac_nhan" <?= ($st == 'xac_nhan') ? 'selected' : '' ?>>Xác nhận đơn hàng</option>
                                        <option value="dang_giao" <?= ($st == 'dang_giao') ? 'selected' : '' ?>>Đang giao</option>
                                        <option value="da_giao" <?= ($st == 'da_giao') ? 'selected' : '' ?>>Đã giao</option>
                                        <option value="thanh_cong" <?= ($st == 'thanh_cong') ? 'selected' : '' ?>>Thành công</option>
                                        <option value="da_huy" <?= ($st == 'da_huy') ? 'selected' : '' ?>>Đã hủy</option>
                                    </select>
                                    <button type="submit" name="capnhat_tt" class="btn btn-outline-primary btn-sm" title="Lưu trạng thái">
                                        Lưu
                                    </button>
                                </form>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="index.php?act=chitietdonhang&id=<?= $dh['id'] ?>" class="btn btn-outline-secondary btn-sm" title="Xem chi tiết đơn">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="index.php?act=theodoigiaohang&id=<?= $dh['id'] ?>" class="btn btn-outline-info btn-sm" title="Xem hành trình">
                                        <i class="bi bi-geo-alt"></i>
                                    </a>
                                    <a href="/bloomshop/invoices/hoa_don_<?= $dh['id'] ?>.html" target="_blank" class="btn btn-outline-success btn-sm" title="In / Xem hóa đơn">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php }} else { ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Không có đơn hàng nào phù hợp.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
