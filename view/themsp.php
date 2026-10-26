<div class="container my-5 form-page">
    <div class="card border-0 shadow-sm p-4 rounded-3 bg-white">
        <h2 class="fw-bold mb-4" style="color: #604b41;">
            <i class="bi bi-flower1 me-2"></i> Thêm sản phẩm hoa mới
        </h2>

        <form method="POST" action="index.php?act=themsp" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label fw-semibold">Tên sản phẩm <span class="text-danger">*</span></label>
                <input name="name" class="form-control" placeholder="Ví dụ: Bó hoa hồng đỏ lãng mạn" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">Giá bán gốc (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" min="0" step="1000" name="price" class="form-control" placeholder="Ví dụ: 350000" required>
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">Danh mục hoa <span class="text-danger">*</span></label>
                    <select name="id_dm" class="form-select" required>
                        <?php foreach ($danhsach as $dm) { ?>
                            <option value="<?= $dm['id'] ?>"><?= htmlspecialchars($dm['name']) ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3 p-3 bg-light rounded border">
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">Giá khuyến mãi (nếu có)</label>
                    <input type="number" min="0" step="1000" name="sale_price" class="form-control" placeholder="Ví dụ: 299000">
                </div>
                <div class="col-sm-6 d-flex align-items-center pt-sm-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_sale" value="1" id="is_sale">
                        <label class="form-check-label fw-semibold" for="is_sale">Bật chương trình khuyến mãi cho sản phẩm này</label>
                    </div>
                </div>
            </div>

            <div class="mb-3"><label class="form-label fw-semibold">Số lượng tồn kho</label><input type="number" name="so_luong" min="0" value="0" class="form-control" required></div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Mô tả chi tiết</label>
                <textarea name="description" rows="3" class="form-control" placeholder="Ý nghĩa của hoa, số lượng cành, dịp tặng phù hợp..."></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Ảnh sản phẩm (JPG, PNG, WebP) <span class="text-danger">*</span></label>
                <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="them" class="btn btn-success px-4">
                    <i class="bi bi-check-circle me-1"></i> Thêm sản phẩm
                </button>
                <a href="index.php?act=quanlysp" class="btn btn-secondary px-3">Quay lại</a>
            </div>
        </form>
    </div>
</div>
