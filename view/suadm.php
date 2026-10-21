<div class="container my-5 form-page">
    <h2 class="mb-4">Sửa danh mục</h2>
    <form method="POST" action="index.php?act=capnhatdm">
        <input type="hidden" name="id" value="<?= $dm['id'] ?>">
        <div class="mb-3"><label class="form-label">Tên danh mục</label><input name="name" class="form-control" value="<?= htmlspecialchars($dm['name']) ?>" required></div>
        <button type="submit" name="capnhat" class="btn btn-success">Cập nhật</button>
        <a class="btn btn-secondary" href="index.php?act=danhmuc">Quay lại</a>
    </form>
</div>
