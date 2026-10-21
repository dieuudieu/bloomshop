<div class="container my-5 form-page">
    <h2 class="mb-4">Thêm sản phẩm</h2>
    <form method="POST" action="index.php?act=themsp" enctype="multipart/form-data">
        <div class="mb-3"><label class="form-label">Tên sản phẩm</label><input name="name" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Giá sản phẩm</label><input type="number" min="0" name="price" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Danh mục</label>
            <select name="id_dm" class="form-select" required>
                <?php foreach ($danhsach as $dm) { ?>
                    <option value="<?= $dm['id'] ?>"><?= htmlspecialchars($dm['name']) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3"><label class="form-label">Ảnh sản phẩm (JPG, PNG, WebP, tối đa 3 MB)</label><input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp" required></div>
        <button type="submit" name="them" class="btn btn-success">Thêm sản phẩm</button>
        <a href="index.php?act=quanlysp" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
