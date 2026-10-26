<div class="container my-5 form-page">
    <div class="card border-0 shadow-sm p-4 rounded-3 bg-white">
        <h2 class="fw-bold mb-4" style="color: #604b41;">
            <i class="bi bi-pencil-square me-2"></i> Chỉnh sửa nhân viên #<?= $nv['id'] ?>
        </h2>

        <form method="POST" action="index.php?act=capnhatnv">
            <input type="hidden" name="id" value="<?= $nv['id'] ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold">Họ và tên nhân viên <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($nv['name']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email đăng nhập <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($nv['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Mật khẩu mới (bỏ trống nếu không muốn đổi)</label>
                <input type="password" name="password" class="form-control" minlength="6" placeholder="Nhập mật khẩu mới nếu cần đổi">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Số điện thoại</label>
                <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($nv['phone'] ?? '') ?>">
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Trạng thái tài khoản</label>
                <select name="status" class="form-select">
                    <option value="1" <?= ($nv['status'] == 1) ? 'selected' : '' ?>>Hoạt động bình thường</option>
                    <option value="0" <?= ($nv['status'] == 0) ? 'selected' : '' ?>>Khóa tài khoản (Mất toàn bộ quyền truy cập)</option>
                </select>
                <div class="form-text text-muted">Lưu ý: Khi chọn Khóa tài khoản, nhân viên sẽ lập tức bị tước quyền truy cập và bị hủy phiên làm việc.</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="capnhatnv" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i> Cập nhật nhân viên
                </button>
                <a href="index.php?act=quanlynv" class="btn btn-secondary px-3">Quay lại</a>
            </div>
        </form>
    </div>
</div>
