<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1" style="color: #604b41;">
                <i class="bi bi-person-badge"></i> Quản lý nhân viên
            </h2>
            <p class="text-muted small mb-0">Thêm, sửa thông tin và phân quyền nhân viên bán hàng</p>
        </div>
        <a href="index.php?act=themnv" class="btn btn-success">
            <i class="bi bi-person-plus-fill me-1"></i> + Thêm nhân viên
        </a>
    </div>

    <!-- Thông báo chính sách phân quyền Admin -->
    <div class="alert alert-info border-0 shadow-sm rounded-3 py-2 px-3 mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-shield-lock text-primary fs-5"></i>
        <div class="small">
            <strong>Chính sách phân quyền Admin:</strong> Hệ thống không cho phép xóa tài khoản nhân viên nhằm bảo toàn toàn vẹn dữ liệu đơn hàng và lịch sử tác vụ. Khi cần dừng quyền làm việc, Quản trị viên sử dụng thao tác <strong>Khóa quyền</strong>. Tài khoản bị khóa sẽ lập tức mất quyền truy cập hệ thống.
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($danhsach_nv)) { foreach ($danhsach_nv as $nv) { ?>
                        <tr class="<?= ($nv['status'] == 0) ? 'table-light text-muted' : '' ?>">
                            <td>#<?= $nv['id'] ?></td>
                            <td class="fw-semibold">
                                <i class="bi bi-person text-secondary me-1"></i>
                                <?= htmlspecialchars($nv['name']) ?>
                            </td>
                            <td><?= htmlspecialchars($nv['email']) ?></td>
                            <td><?= htmlspecialchars($nv['phone'] ?? 'Chưa cập nhật') ?></td>
                            <td>
                                <?php if ($nv['status'] == 1) { ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Hoạt động</span>
                                <?php } else { ?>
                                    <span class="badge bg-danger"><i class="bi bi-lock-fill me-1"></i>Đã khóa (Mất quyền)</span>
                                <?php } ?>
                            </td>
                            <td class="small text-muted"><?= !empty($nv['created_at']) ? date('d/m/Y', strtotime($nv['created_at'])) : date('d/m/Y') ?></td>
                            <td class="text-center">
                                <a href="index.php?act=suanv&id=<?= $nv['id'] ?>" class="btn btn-warning btn-sm me-1" title="Sửa thông tin">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </a>
                                <?php if (isset($_SESSION['id']) && $_SESSION['id'] == $nv['id']) { ?>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="alert('Bạn không thể tự khóa tài khoản của chính mình khi đang đăng nhập!')" title="Không thể tự khóa chính mình">
                                        <i class="bi bi-shield-lock-fill"></i> Đang dùng
                                    </button>
                                <?php } else { ?>
                                    <form method="POST" action="index.php?act=khoanv" class="d-inline" onsubmit="return confirm('<?= ($nv['status'] == 1) ? 'Bạn có chắc chắn muốn KHÓA tài khoản nhân viên này?\n\nNhân viên sẽ MẤT TOÀN BỘ QUYỀN TRUY CẬP hệ thống ngay lập tức.' : 'Bạn muốn MỞ KHÓA tài khoản cho nhân viên này?' ?>')">
                                        <input type="hidden" name="id" value="<?= $nv['id'] ?>">
                                        <input type="hidden" name="status" value="<?= ($nv['status'] == 1) ? 0 : 1 ?>">
                                        <button type="submit" class="btn btn-sm <?= ($nv['status'] == 1) ? 'btn-outline-danger' : 'btn-outline-success' ?>" title="<?= ($nv['status'] == 1) ? 'Khóa quyền nhân viên' : 'Mở khóa tài khoản' ?>">
                                            <i class="bi <?= ($nv['status'] == 1) ? 'bi-lock-fill' : 'bi-unlock-fill' ?>"></i>
                                            <?= ($nv['status'] == 1) ? 'Khóa quyền' : 'Mở khóa' ?>
                                        </button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php }} else { ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Chưa có nhân viên nào trong hệ thống.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
