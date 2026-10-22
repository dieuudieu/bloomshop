<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1" style="color: #604b41;">
                <i class="bi bi-folder2-open"></i> Quản lý danh mục hoa
            </h2>
            <p class="text-muted small mb-0">Thêm, sửa, xóa các danh mục phân loại sản phẩm</p>
        </div>
        <a href="index.php?act=themdm" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> + Thêm danh mục mới
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Tên danh mục</th>
                        <th class="text-center" style="width: 160px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($danhsach)) { foreach ($danhsach as $dm) { ?>
                    <tr>
                        <td class="fw-bold text-muted">#<?= $dm['id'] ?></td>
                        <td>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($dm['name']) ?></span>
                        </td>
                        <td class="text-center">
                            <a class="btn btn-warning btn-sm me-1" href="index.php?act=suadm&id=<?= $dm['id'] ?>" title="Sửa danh mục">
                                <i class="bi bi-pencil-square"></i> Sửa
                            </a>
                            <form method="POST" action="index.php?act=xoadm" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                <input type="hidden" name="id" value="<?= $dm['id'] ?>">
                                <button type="submit" name="xoa" class="btn btn-danger btn-sm" title="Xóa danh mục">
                                    <i class="bi bi-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php }} else { ?>
                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted">Chưa có danh mục nào.</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
