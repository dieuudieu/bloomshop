<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
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
                        <tr>
                            <td>#<?= $nv['id'] ?></td>
                            <td class="fw-semibold">
                                <i class="bi bi-person text-secondary me-1"></i>
                                <?= htmlspecialchars($nv['name']) ?>
                            </td>
                            <td><?= htmlspecialchars($nv['email']) ?></td>
                            <td><?= htmlspecialchars($nv['phone'] ?? 'Chưa cập nhật') ?></td>
                            <td>
                                <?php if ($nv['status'] == 1) { ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php } else { ?>
                                    <span class="badge bg-danger">Bị khóa</span>
                                <?php } ?>
                            </td>
                            <td class="small text-muted"><?= date('d/m/Y', strtotime($nv['created_at'])) ?></td>
                            <td class="text-center">
                                <a href="index.php?act=suanv&id=<?= $nv['id'] ?>" class="btn btn-warning btn-sm" title="Sửa thông tin">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </a>
                                <form method="POST" action="index.php?act=xoanv" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa nhân viên này?')">
                                    <input type="hidden" name="id" value="<?= $nv['id'] ?>">
                                    <button type="submit" name="xoanv" class="btn btn-danger btn-sm" title="Xóa nhân viên">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </form>
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
