<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1" style="color: #604b41;">
                <i class="bi bi-chat-heart"></i> Quản lý đánh giá & bình luận
            </h2>
            <p class="text-muted small mb-0">Kiểm duyệt nhận xét và chấm sao của khách hàng</p>
        </div>
        
        <!-- Bộ lọc số sao -->
        <form method="GET" action="index.php" class="d-flex gap-2 align-items-center">
            <input type="hidden" name="act" value="quanlydanhgia">
            <select name="star" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả số sao</option>
                <option value="5" <?= (($_GET['star'] ?? '') == '5') ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ (5 sao)</option>
                <option value="4" <?= (($_GET['star'] ?? '') == '4') ? 'selected' : '' ?>>⭐⭐⭐⭐ (4 sao)</option>
                <option value="3" <?= (($_GET['star'] ?? '') == '3') ? 'selected' : '' ?>>⭐⭐⭐ (3 sao)</option>
                <option value="2" <?= (($_GET['star'] ?? '') == '2') ? 'selected' : '' ?>>⭐⭐ (2 sao)</option>
                <option value="1" <?= (($_GET['star'] ?? '') == '1') ? 'selected' : '' ?>>⭐ (1 sao)</option>
            </select>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Sản phẩm</th>
                        <th>Khách hàng</th>
                        <th>Đánh giá</th>
                        <th>Nội dung bình luận</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($danhsach_dg)) { foreach ($danhsach_dg as $dg) { ?>
                        <tr>
                            <td>#<?= $dg['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= $asset_dir ?>/image/sanpham/<?= htmlspecialchars($dg['product_image']) ?>" width="45" height="45" class="rounded object-fit-cover" alt="" onerror="this.src='<?= $asset_dir ?>/image/banner.jpg'">
                                    <span class="small fw-semibold"><?= htmlspecialchars($dg['product_name']) ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold"><?= htmlspecialchars($dg['user_name']) ?></span>
                            </td>
                            <td>
                                <div class="star-rating small">
                                    <?php for ($i = 1; $i <= 5; $i++) {
                                        echo ($i <= $dg['rating']) ? '★' : '☆';
                                    } ?>
                                </div>
                            </td>
                            <td>
                                <div class="small" style="max-width: 280px;"><?= nl2br(htmlspecialchars($dg['content'])) ?></div>
                            </td>
                            <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($dg['created_at'])) ?></td>
                            <td>
                                <?php if ($dg['status'] == 1) { ?>
                                    <span class="badge bg-success">Đang hiển thị</span>
                                <?php } else { ?>
                                    <span class="badge bg-secondary">Đã ẩn</span>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <form method="POST" action="index.php?act=doitrangthaidanhgia" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $dg['id'] ?>">
                                        <input type="hidden" name="status" value="<?= ($dg['status'] == 1) ? 0 : 1 ?>">
                                        <button type="submit" class="btn btn-sm <?= ($dg['status'] == 1) ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= ($dg['status'] == 1) ? 'Ẩn đánh giá' : 'Hiện đánh giá' ?>">
                                            <?= ($dg['status'] == 1) ? 'Ẩn' : 'Hiện' ?>
                                        </button>
                                    </form>
                                    <form method="POST" action="index.php?act=xoadanhgia" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này?')">
                                        <input type="hidden" name="id" value="<?= $dg['id'] ?>">
                                        <button type="submit" name="xoadanhgia" class="btn btn-outline-danger btn-sm" title="Xóa đánh giá">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php }} else { ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Không có đánh giá nào phù hợp.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
