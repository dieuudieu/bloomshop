<div class="container my-5 auth-form">
    <h2 class="mb-4">Đăng ký tài khoản</h2>
    <form method="POST" action="index.php?act=dangky">
        <div class="mb-3"><label class="form-label">Họ tên</label><input class="form-control" name="name" required></div>
        <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
        <div class="mb-3"><label class="form-label">Mật khẩu (tối thiểu 6 ký tự)</label><input type="password" class="form-control" name="password" minlength="6" required></div>
        <button type="submit" name="dangky" class="btn btn-bloom">Đăng ký</button>
        <a href="index.php?act=dangnhap" class="ms-3">Đã có tài khoản?</a>
    </form>
</div>
