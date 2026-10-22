<div class="container my-5 form-page" style="max-width: 600px;">
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
        <h3 class="fw-bold mb-4" style="color: #604b41;">
            <i class="bi bi-pencil-square text-warning me-2"></i> Chỉnh sửa danh mục #<?= $dm['id'] ?>
        </h3>
        <form method="POST" action="index.php?act=capnhatdm">
            <input type="hidden" name="id" value="<?= $dm['id'] ?>">
            <div class="mb-3">
                <label class="form-label fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
                <input name="name" class="form-control" value="<?= htmlspecialchars($dm['name']) ?>" required autofocus>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" name="capnhat" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i> Cập nhật
                </button>
                <a class="btn btn-secondary px-3" href="index.php?act=danhmuc">Quay lại</a>
            </div>
        </form>
    </div>
</div>
