<div class="container my-5 form-page">
    <h2 class="mb-4">Sửa sản phẩm</h2>
    <form method="POST" action="index.php?act=capnhatsp" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $sp['id'] ?>">
        <div class="mb-3"><label class="form-label">Tên sản phẩm</label><input name="name" class="form-control" value="<?= htmlspecialchars($sp['name']) ?>" required></div>
        <div class="mb-3"><label class="form-label">Giá</label><input type="number" min="0" name="price" class="form-control" value="<?= $sp['price'] ?>" required></div>
        <div class="mb-3"><label class="form-label">Danh mục</label>
            <select name="id_dm" class="form-select">
                <?php foreach ($danhmuc as $dm) { ?>
                    <option value="<?= $dm['id'] ?>" <?= $dm['id'] == $sp['id_dm'] ? 'selected' : '' ?>><?= htmlspecialchars($dm['name']) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3"><label class="form-label">Ảnh hiện tại</label><div><img width="120" src="/bloomshop/view/image/sanpham/<?= htmlspecialchars($sp['image']) ?>" alt=""></div></div>
        <div class="mb-3"><label class="form-label">Ảnh mới (không bắt buộc)</label><input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp"></div>
        <button type="submit" name="capnhat" class="btn btn-success">Cập nhật</button>
        <a href="index.php?act=quanlysp" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
