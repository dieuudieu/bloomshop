<div class="container my-5 form-page" style="max-width: 600px;">
    <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-white">
        <h3 class="fw-bold mb-3 text-center" style="color: #604b41;">
            <i class="bi bi-search-heart text-bloom"></i> Tra cứu đơn hàng
        </h3>
        <p class="text-muted text-center small mb-4">
            Nhập Mã đơn hàng và Số điện thoại đặt hàng để kiểm tra tiến trình giao hoa
        </p>

        <form method="POST" action="index.php?act=xulytracuu">
            <div class="mb-3">
                <label class="form-label fw-semibold">Mã đơn hàng <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">#</span>
                    <input type="number" name="order_id" class="form-control" placeholder="Ví dụ: 1001" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Số điện thoại nhận hàng <span class="text-danger">*</span></label>
                <input type="tel" name="phone" class="form-control" placeholder="0901234567" required>
            </div>

            <button type="submit" name="tracuu" class="btn btn-bloom w-100 py-2 fw-semibold">
                <i class="bi bi-search me-1"></i> Kiểm tra đơn hàng
            </button>
        </form>
    </div>
</div>
