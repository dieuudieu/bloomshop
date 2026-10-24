<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1" style="color: #604b41;">
                <i class="bi bi-tag-fill"></i> Quản lý Khuyến mãi Sản phẩm
            </h2>
            <p class="text-muted small mb-0">Cài đặt giá khuyến mãi, bật/tắt ưu đãi giảm giá hiển thị trên website</p>
        </div>
        <a href="index.php?act=quanlysp" class="btn btn-outline-secondary">
            <i class="bi bi-flower1"></i> Danh sách tất cả sản phẩm
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá gốc</th>
                        <th>Giá khuyến mãi</th>
                        <th>Trạng thái Sale</th>
                        <th class="text-center" style="width: 220px;">Cập nhật nhanh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($danhsach_sp)) { foreach ($danhsach_sp as $sp) { 
                        $has_sale = (!empty($sp['is_sale']) && !empty($sp['sale_price']));
                    ?>
                        <tr>
                            <td>#<?= $sp['id'] ?></td>
                            <td>
                                <img src="<?= $asset_dir ?>/image/sanpham/<?= htmlspecialchars($sp['image']) ?>" width="60" height="60" class="rounded object-fit-cover" alt="" onerror="this.src='<?= $asset_dir ?>/image/banner.jpg'">
                            </td>
                            <td class="fw-semibold"><?= htmlspecialchars($sp['name']) ?></td>
                            <td><?= htmlspecialchars($sp['tendanhmuc']) ?></td>
                            <td class="fw-bold"><?= number_format($sp['price'], 0, ',', '.') ?>đ</td>
                            
                            <!-- Form cập nhật nhanh khuyến mãi cho từng sản phẩm -->
                            <form method="POST" action="index.php?act=capnhatkhuyenmai">
                                <input type="hidden" name="id" value="<?= $sp['id'] ?>">
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" min="0" step="1000" name="sale_price" class="form-control" value="<?= $sp['sale_price'] ?>" placeholder="Nhập giá sale">
                                        <span class="input-group-text">đ</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_sale" value="1" id="sale_<?= $sp['id'] ?>" <?= (!empty($sp['is_sale'])) ? 'checked' : '' ?>>
                                        <label class="form-check-label small" for="sale_<?= $sp['id'] ?>">
                                            <?= (!empty($sp['is_sale'])) ? '<span class="text-danger fw-bold">Đang Sale</span>' : '<span class="text-muted">Tắt Sale</span>' ?>
                                        </label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button type="submit" name="capnhatkm" class="btn btn-bloom btn-sm">
                                        <i class="bi bi-save me-1"></i> Lưu KM
                                    </button>
                                </td>
                            </form>
                        </tr>
                    <?php }} else { ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Chưa có sản phẩm nào.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
