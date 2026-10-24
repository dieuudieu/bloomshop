<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1" style="color: #604b41;">
                <i class="bi bi-folder2-open"></i> Quản lý danh mục hoa
            </h2>
            <p class="text-muted small mb-0">Thêm, sửa, xóa các danh mục phân loại sản phẩm (Ràng buộc khóa ngoại an toàn)</p>
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
                        <th class="text-center" style="width: 180px;">Số hoa trong mục</th>
                        <th class="text-center" style="width: 160px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($danhsach)) { foreach ($danhsach as $dm) { 
                    $spCount = (int)($dm['total_products'] ?? 0);
                ?>
                    <tr>
                        <td class="fw-bold text-muted">#<?= $dm['id'] ?></td>
                        <td>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($dm['name']) ?></span>
                        </td>
                        <td class="text-center">
                            <?php if ($spCount > 0) { ?>
                                <span class="badge bg-primary" title="Đang chứa <?= $spCount ?> sản phẩm hoa">
                                    <i class="bi bi-flower1 me-1"></i><?= $spCount ?> sản phẩm
                                </span>
                            <?php } else { ?>
                                <span class="badge bg-light text-muted border">0 sản phẩm</span>
                            <?php } ?>
                        </td>
                        <td class="text-center">
                            <a class="btn btn-warning btn-sm me-1" href="index.php?act=suadm&id=<?= $dm['id'] ?>" title="Sửa danh mục">
                                <i class="bi bi-pencil-square"></i> Sửa
                            </a>
                            <?php if ($spCount > 0) { ?>
                                <button type="button" 
                                        class="btn btn-outline-secondary btn-sm" 
                                        onclick="alert('Ràng buộc toàn vẹn dữ liệu: Danh mục \'<?= htmlspecialchars(addslashes($dm['name'])) ?>\' đang chứa <?= $spCount ?> sản phẩm hoa!\n\nBạn phải chuyển hoặc xóa các sản phẩm thuộc danh mục này trước khi có thể xóa danh mục.')" 
                                        title="Không thể xóa vì đang chứa sản phẩm">
                                    <i class="bi bi-lock-fill"></i> Đã khóa
                                </button>
                            <?php } else { ?>
                                <form method="POST" action="index.php?act=xoadm" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                    <input type="hidden" name="id" value="<?= $dm['id'] ?>">
                                    <button type="submit" name="xoa" class="btn btn-danger btn-sm" title="Xóa danh mục">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php }} else { ?>
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Chưa có danh mục nào.</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
