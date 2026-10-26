<div class="container my-5 form-page">
    <div class="card border-0 shadow-sm p-4 rounded-3 bg-white">
        <h2 class="fw-bold mb-4" style="color: #604b41;">
            <i class="bi bi-pencil-square me-2"></i> Chỉnh sửa sản phẩm #<?= $sp['id'] ?>
        </h2>

        <form method="POST" action="index.php?act=capnhatsp" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $sp['id'] ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold">Tên sản phẩm <span class="text-danger">*</span></label>
                <input name="name" class="form-control" value="<?= htmlspecialchars($sp['name']) ?>" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">Giá bán gốc (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" min="0" step="1000" name="price" class="form-control" value="<?= $sp['price'] ?>" required>
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">Danh mục <span class="text-danger">*</span></label>
                    <select name="id_dm" class="form-select">
                        <?php foreach ($danhmuc as $dm) { ?>
                            <option value="<?= $dm['id'] ?>" <?= $dm['id'] == $sp['id_dm'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dm['name']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3 p-3 bg-light rounded border">
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">Giá khuyến mãi (nếu có)</label>
                    <input type="number" min="0" step="1000" name="sale_price" class="form-control" value="<?= $sp['sale_price'] ?? '' ?>" placeholder="Để trống nếu không giảm giá">
                </div>
                <div class="col-sm-6 d-flex align-items-center pt-sm-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_sale" value="1" id="is_sale" <?= (!empty($sp['is_sale'])) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="is_sale">Bật chương trình khuyến mãi cho sản phẩm này</label>
                    </div>
                </div>
            </div>

            <div class="mb-3"><label class="form-label fw-semibold">Số lượng tồn kho</label><input type="number" name="so_luong" min="0" value="<?= (int)($sp['so_luong'] ?? 0) ?>" class="form-control" required></div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Mô tả chi tiết</label>
                <textarea name="description" rows="3" class="form-control"><?= htmlspecialchars($sp['description'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Ảnh hiện tại</label>
                <div>
                    <img width="120" height="120" class="rounded object-fit-cover border" src="<?= $asset_dir ?>/image/sanpham/<?= htmlspecialchars($sp['image']) ?>" alt="" onerror="this.src='<?= $asset_dir ?>/image/banner.jpg'">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Thay đổi ảnh mới (tùy chọn)</label>
                <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="capnhat" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i> Cập nhật sản phẩm
                </button>
                <a href="index.php?act=quanlysp" class="btn btn-secondary px-3">Quay lại</a>
            </div>
        </form>
    </div>
</div>
