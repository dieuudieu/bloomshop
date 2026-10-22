<div class="container my-5 auth-form" style="max-width: 500px;">
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 65px; height: 65px;">
                <i class="bi bi-person-plus-fill fs-1 text-bloom"></i>
            </div>
            <h3 class="fw-bold mb-1" style="color: #604b41;">Đăng ký tài khoản</h3>
            <p class="text-muted small">Trở thành thành viên BloomShop để nhận ưu đãi VIP</p>
        </div>

        <form method="POST" action="index.php?act=dangky">
            <div class="mb-3">
                <label class="form-label fw-semibold small">Họ và tên <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" name="name" placeholder="Nguyễn Văn A" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Địa chỉ Email <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control border-start-0 ps-0" name="email" placeholder="email@example.com" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small">Mật khẩu (tối thiểu 6 ký tự) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control border-start-0 ps-0" name="password" minlength="6" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" name="dangky" class="btn btn-bloom w-100 py-2 fw-semibold mb-3">
                <i class="bi bi-check2-circle me-1"></i> Hoàn tất đăng ký
            </button>

            <div class="text-center small text-muted">
                Đã có tài khoản? <a href="index.php?act=dangnhap" class="fw-semibold text-bloom text-decoration-none">Đăng nhập tại đây</a>
            </div>
        </form>
    </div>
</div>
