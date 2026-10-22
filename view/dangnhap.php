<div class="container my-5 auth-form" style="max-width: 480px;">
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 65px; height: 65px;">
                <i class="bi bi-person-circle fs-1 text-bloom"></i>
            </div>
            <h3 class="fw-bold mb-1" style="color: #604b41;">Đăng nhập</h3>
            <p class="text-muted small">Chào mừng bạn quay trở lại với BloomShop</p>
        </div>

        <form method="POST" action="index.php?act=dangnhap">
            <div class="mb-3">
                <label class="form-label fw-semibold small">Địa chỉ Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control border-start-0 ps-0" name="email" placeholder="email@example.com" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control border-start-0 ps-0" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" name="dangnhap" class="btn btn-bloom w-100 py-2 fw-semibold mb-3">
                <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập ngay
            </button>

            <div class="text-center small text-muted">
                Chưa có tài khoản? <a href="index.php?act=dangky" class="fw-semibold text-bloom text-decoration-none">Đăng ký thành viên</a>
            </div>
        </form>
    </div>
</div>
