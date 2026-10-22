<div class="container my-5 form-page">
    <div class="card border-0 shadow-sm p-4 rounded-3 bg-white">
        <h2 class="fw-bold mb-4" style="color: #604b41;">
            <i class="bi bi-ticket-perforated me-2"></i> Tạo Voucher mới
        </h2>

        <form method="POST" action="index.php?act=themvoucher">
            <div class="mb-3">
                <label class="form-label fw-semibold">Mã giảm giá (Code) <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control text-uppercase" placeholder="Ví dụ: BLOOM20, GIAM50K" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">Hình thức giảm <span class="text-danger">*</span></label>
                    <select name="discount_type" class="form-select">
                        <option value="fixed">Giảm tiền mặt cố định (VNĐ)</option>
                        <option value="percent">Giảm theo phần trăm (%)</option>
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">Mức giảm <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="1" name="discount_value" class="form-control" placeholder="Ví dụ: 50000 hoặc 10" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">Đơn hàng tối thiểu (VNĐ)</label>
                    <input type="number" min="0" name="min_order_value" class="form-control" value="0">
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">Số lượng phát hành <span class="text-danger">*</span></label>
                    <input type="number" min="1" name="quantity" class="form-control" value="100" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Thời hạn sử dụng (Hết hạn lúc 23:59) <span class="text-danger">*</span></label>
                <input type="date" name="expiry_date" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="them" class="btn btn-success px-4">
                    <i class="bi bi-check-circle me-1"></i> Lưu voucher
                </button>
                <a href="index.php?act=quanlyvoucher" class="btn btn-secondary px-3">Quay lại</a>
            </div>
        </form>
    </div>
</div>
