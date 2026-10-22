<?php
// controller/c_taikhoan.php - Xử lý Đăng ký, Đăng nhập, Đăng xuất

// 1. Đăng ký tài khoản mới
if ($act == 'dangky' && isset($_POST['dangky'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "SELECT * FROM users WHERE email = :email";
    $ds = $data->readitem($sql, [':email' => $email]);

    if (count($ds) > 0) {
        $loi = 'Email này đã được đăng ký trên hệ thống.';
    } else {
        $sql = "INSERT INTO users (name, email, password, role, status, tier, total_spent)
                VALUES (:name, :email, :password, 'user', 1, 'silver', 0)";
        $data->execute_item($sql, [
            ':name' => $name, ':email' => $email, ':password' => $password
        ]);
        header("Location: index.php?act=dangnhap&msg=dangky_thanhcong");
        exit;
    }
}

// 2. Đăng nhập hệ thống
if ($act == 'dangnhap' && isset($_POST['dangnhap'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE email = :email";
    $ds = $data->readitem($sql, [':email' => $email]);

    if (count($ds) > 0 && password_verify($password, $ds[0]['password'])) {
        $user = $ds[0];
        if (isset($user['status']) && $user['status'] == 0) {
            $loi = 'Tài khoản của bạn đã bị khóa bởi Quản trị viên. Vui lòng liên hệ hotline để được hỗ trợ.';
        } else {
            session_regenerate_id(true);
            $_SESSION['id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['tier'] = $user['tier'] ?? 'silver';
            header("Location: index.php?act=trangchu");
            exit;
        }
    } else {
        $loi = 'Email hoặc mật khẩu không chính xác.';
    }
}

// 3. Đăng xuất
if ($act == 'dangxuat') {
    session_unset();
    session_destroy();
    header("Location: index.php?act=trangchu");
    exit;
}

// 4. Hiển thị trang đăng nhập hoặc đăng ký
if ($act == 'dangky') {
    hien_thi_view('dangky.php');
} else {
    hien_thi_view('dangnhap.php');
}
