<div class="container my-5 form-page">
    <div class="card border-0 shadow-sm p-4 rounded-3 bg-white">
        <h2 class="fw-bold mb-4" style="color: #604b41;">
            <i class="bi bi-person-plus-fill me-2"></i> Thêm nhân viên mới
        </h2>

        <form method="POST" action="index.php?act=themnv">
            <div class="mb-3">
                <label class="form-label fw-semibold">Họ và tên nhân viên <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Nguyễn Văn B" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email đăng nhập <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" placeholder="nhanvien@bloomshop.vn" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Mật khẩu khởi tạo <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" minlength="6" placeholder="Tối thiểu 6 ký tự" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Số điện thoại</label>
                <input type="tel" name="phone" class="form-control" placeholder="0908765432">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="themnv" class="btn btn-success px-4">
                    <i class="bi bi-check-circle me-1"></i> Tạo nhân viên
                </button>
                <a href="index.php?act=quanlynv" class="btn btn-secondary px-3">Quay lại</a>
            </div>
        </form>
    </div>
</div>
