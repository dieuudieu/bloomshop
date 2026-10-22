<div class="container my-5" style="max-width: 750px;">
    <div class="card border-0 shadow-sm p-4 p-md-5 text-center rounded-4 bg-white">
        <div class="mb-3">
            <span class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 75px; height: 75px; font-size: 38px;">
                <i class="bi bi-check-lg"></i>
            </span>
        </div>

        <h2 class="fw-bold text-success mb-2">Đặt hàng thành công!</h2>
        <p class="text-secondary mb-4">
            Cảm ơn bạn đã lựa chọn BloomShop. Đơn hàng của bạn mang mã số <strong>#<?= $order['id'] ?></strong> đã được chuyển tới bộ phận xử lý.
        </p>

        <!-- Thông báo hóa đơn gửi mail -->
        <div class="alert alert-info text-start d-flex align-items-center gap-3 mb-4">
            <i class="bi bi-envelope-check-fill fs-2 text-primary"></i>
            <div>
                <strong>Hóa đơn điện tử đã được tạo tự động!</strong><br>
                <span>Hệ thống đã gửi hóa đơn chi tiết tới Quản trị viên (admin@bloomshop.vn) và khách hàng (<?= htmlspecialchars($order['customer_email']) ?>).</span>
            </div>
        </div>

        <!-- Chi tiết tóm tắt -->
        <div class="bg-light p-3 rounded-3 text-start mb-4">
            <div class="row g-2 small">
                <div class="col-sm-6"><strong>Mã đơn hàng:</strong> #<?= $order['id'] ?></div>
                <div class="col-sm-6"><strong>Thời gian đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
                <div class="col-sm-6"><strong>Người nhận:</strong> <?= htmlspecialchars($order['customer_name']) ?> (<?= htmlspecialchars($order['customer_phone']) ?>)</div>
                <div class="col-sm-6"><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['customer_address']) ?></div>
                <div class="col-sm-6"><strong>Phương thức:</strong> <?= htmlspecialchars($order['payment_method']) ?></div>
                <div class="col-sm-6"><strong>Tổng thanh toán:</strong> <span class="fw-bold price-text fs-6"><?= number_format($order['total_price'], 0, ',', '.') ?>đ</span></div>
            </div>
        </div>

        <!-- Các nút tác vụ -->
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <a href="index.php?act=theodoigiaohang&id=<?= $order['id'] ?>" class="btn btn-bloom px-3">
                <i class="bi bi-truck me-1"></i> Theo dõi quá trình giao hàng
            </a>
            <a href="/bloomshop/invoices/hoa_don_<?= $order['id'] ?>.html" target="_blank" class="btn btn-outline-primary px-3">
                <i class="bi bi-file-earmark-text me-1"></i> Xem hóa đơn điện tử
            </a>
            <a href="index.php?act=lichsudonhang" class="btn btn-outline-secondary px-3">
                <i class="bi bi-clock-history me-1"></i> Lịch sử mua hàng
            </a>
            <a href="index.php?act=trangchu" class="btn btn-light px-3">
                <i class="bi bi-house me-1"></i> Về trang chủ
            </a>
        </div>
    </div>
</div>
