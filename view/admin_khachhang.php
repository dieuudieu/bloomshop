<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1" style="color: #604b41;">
                <i class="bi bi-people-fill"></i> Quản lý khách hàng & Phân hạng
            </h2>
            <p class="text-muted small mb-0">Theo dõi thông tin, tổng chi tiêu, cấp bậc hội viên và phân quyền khóa tài khoản</p>
        </div>

        <!-- Bộ lọc Cấp bậc -->
        <form method="GET" action="index.php" class="d-flex gap-2 align-items-center">
            <input type="hidden" name="act" value="quanlykh">
            <select name="tier" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả hạng thành viên</option>
                <option value="diamond" <?= (($_GET['tier'] ?? '') == 'diamond') ? 'selected' : '' ?>>💎 Diamond (Chi tiêu > 5tr)</option>
                <option value="gold" <?= (($_GET['tier'] ?? '') == 'gold') ? 'selected' : '' ?>>🥇 Gold (Chi tiêu 1tr - 5tr)</option>
                <option value="silver" <?= (($_GET['tier'] ?? '') == 'silver') ? 'selected' : '' ?>>🥈 Silver (Chi tiêu < 1tr)</option>
            </select>
        </form>
    </div>

    <!-- Bảng thông tin phân hạng chi tiêu -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-white border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted fw-bold">💎 HẠNG DIAMOND</div>
                        <div class="text-secondary small">Tổng chi tiêu: <strong>&ge; 5.000.000đ</strong></div>
                    </div>
                    <span class="fs-4 fw-bold text-primary"><?= $count_diamond ?? 0 ?> KH</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-white border-start border-4 border-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted fw-bold">🥇 HẠNG GOLD</div>
                        <div class="text-secondary small">Tổng chi tiêu: <strong>1.000.000đ - 5.000.000đ</strong></div>
                    </div>
                    <span class="fs-4 fw-bold text-warning"><?= $count_gold ?? 0 ?> KH</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-white border-start border-4 border-secondary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted fw-bold">🥈 HẠNG SILVER</div>
                        <div class="text-secondary small">Tổng chi tiêu: <strong>&lt; 1.000.000đ</strong></div>
                    </div>
                    <span class="fs-4 fw-bold text-secondary"><?= $count_silver ?? 0 ?> KH</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Khách hàng</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Tổng chi tiêu</th>
                        <th>Hạng thành viên</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($danhsach_kh)) { foreach ($danhsach_kh as $kh) { ?>
                        <tr>
                            <td>#<?= $kh['id'] ?></td>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($kh['name']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($kh['address'] ?? '') ?></small>
                            </td>
                            <td><?= htmlspecialchars($kh['email']) ?></td>
                            <td><?= htmlspecialchars($kh['phone'] ?? 'Chưa cập nhật') ?></td>
                            <td class="fw-bold price-text">
                                <?= number_format($kh['total_spent'], 0, ',', '.') ?>đ
                            </td>
                            <td>
                                <?php if ($kh['tier'] == 'diamond') { ?>
                                    <span class="badge badge-tier badge-diamond">💎 Diamond</span>
                                <?php } elseif ($kh['tier'] == 'gold') { ?>
                                    <span class="badge badge-tier badge-gold">🥇 Gold</span>
                                <?php } else { ?>
                                    <span class="badge badge-tier badge-silver">🥈 Silver</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($kh['status'] == 1) { ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php } else { ?>
                                    <span class="badge bg-danger">Đã khóa</span>
                                <?php } ?>
                            </td>
                            <td class="small text-muted"><?= date('d/m/Y', strtotime($kh['created_at'])) ?></td>
                            <td class="text-center">
                                <form method="POST" action="index.php?act=khoakh" class="d-inline" onsubmit="return confirm('<?= ($kh['status'] == 1) ? 'Bạn muốn khóa tài khoản này? Khách hàng sẽ không thể đăng nhập.' : 'Bạn muốn mở khóa tài khoản này?' ?>')">
                                    <input type="hidden" name="id" value="<?= $kh['id'] ?>">
                                    <input type="hidden" name="status" value="<?= ($kh['status'] == 1) ? 0 : 1 ?>">
                                    <button type="submit" class="btn btn-sm <?= ($kh['status'] == 1) ? 'btn-outline-danger' : 'btn-outline-success' ?>">
                                        <i class="bi <?= ($kh['status'] == 1) ? 'bi-lock-fill' : 'bi-unlock-fill' ?>"></i>
                                        <?= ($kh['status'] == 1) ? 'Khóa' : 'Mở khóa' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php }} else { ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Không có khách hàng nào.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
