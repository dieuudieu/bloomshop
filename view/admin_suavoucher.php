<div class="container my-5 form-page">
    <div class="card border-0 shadow-sm p-4 rounded-3 bg-white">
        <h2 class="fw-bold mb-4" style="color: #604b41;">
            <i class="bi bi-pencil-square me-2"></i> Chỉnh sửa Voucher #<?= $vc['id'] ?>
        </h2>

        <form method="POST" action="index.php?act=capnhatvoucher">
            <input type="hidden" name="id" value="<?= $vc['id'] ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold">Mã giảm giá (Code) <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control text-uppercase" value="<?= htmlspecialchars($vc['code']) ?>" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">Hình thức giảm <span class="text-danger">*</span></label>
                    <select name="discount_type" class="form-select">
                        <option value="fixed" <?= ($vc['discount_type'] == 'fixed') ? 'selected' : '' ?>>Giảm tiền mặt cố định (VNĐ)</option>
                        <option value="percent" <?= ($vc['discount_type'] == 'percent') ? 'selected' : '' ?>>Giảm theo phần trăm (%)</option>
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">Mức giảm <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="1" name="discount_value" class="form-control" value="<?= $vc['discount_value'] ?>" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-sm-4">
                    <label class="form-label fw-semibold">Đơn tối thiểu (VNĐ)</label>
                    <input type="number" min="0" name="min_order_value" class="form-control" value="<?= $vc['min_order_value'] ?>">
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-semibold">Tổng số lượng <span class="text-danger">*</span></label>
                    <input type="number" min="1" name="quantity" class="form-control" value="<?= $vc['quantity'] ?>" required>
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-semibold">Đã sử dụng</label>
                    <input type="number" class="form-control" value="<?= $vc['used_count'] ?>" readonly>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">Thời hạn sử dụng <span class="text-danger">*</span></label>
                    <input type="date" name="expiry_date" class="form-control" value="<?= $vc['expiry_date'] ?>" required>
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= ($vc['status'] == 1) ? 'selected' : '' ?>>Kích hoạt (Khả dụng)</option>
                        <option value="0" <?= ($vc['status'] == 0) ? 'selected' : '' ?>>Khóa (Tạm ngừng)</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="capnhat" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i> Lưu thay đổi
                </button>
                <a href="index.php?act=quanlyvoucher" class="btn btn-secondary px-3">Quay lại</a>
            </div>
        </form>
    </div>
</div>
