<?php
// controller/index.php - Bộ điều hướng trung tâm (Router) BloomShop
session_start();
require_once "../model/xl_database.php";
require_once "../model/mailer.php";

$data = new xl_data();
$act = $_REQUEST['act'] ?? 'trangchu';
$loi = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);

$thanhcong = $_SESSION['flash_success'] ?? ($_GET['msg'] ?? '');
unset($_SESSION['flash_success']);

// Hàm hỗ trợ render giao diện dùng chung (View Helper)
function hien_thi_view($view_file, $data_truyen = []) {
    global $act, $loi, $thanhcong;
    extract($data_truyen);
    include "../view/header.php";
    if (!empty($loi)) {
        echo '<div class="container mt-3"><div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i>' . htmlspecialchars($loi) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>';
    }
    if (!empty($thanhcong)) {
        echo '<div class="container mt-3"><div class="alert alert-success alert-dismissible fade show" role="alert"><i class="bi bi-check-circle-fill me-2"></i>' . htmlspecialchars($thanhcong) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>';
    }
    include "../view/" . $view_file;
    include "../view/footer.php";
}

// Danh sách các quyền Quản trị (Admin & Staff)
$staff_allowed = [
    'danhmuc', 'themdm', 'suadm', 'capnhatdm', 'xoadm',
    'quanlysp', 'themsp', 'suasp', 'capnhatsp', 'xoasp',
    'khuyenmai', 'capnhatkhuyenmai',
    'quanlyvoucher', 'themvoucher', 'suavoucher', 'capnhatvoucher', 'xoavoucher',
    'quanlydanhgia', 'doitrangthaidanhgia', 'xoadanhgia',
    'quanlykh', 'khoakh',
    'quanlydonhang', 'doitrangthaidonhang'
];

$admin_only = [
    'quanlynv', 'themnv', 'suanv', 'capnhatnv', 'xoanv',
    'doanhthu'
];

// Kiểm tra quyền nhân viên & admin
if (in_array($act, $staff_allowed) && (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'staff']))) {
    header("Location: index.php?act=dangnhap");
    exit;
}

// Kiểm tra quyền riêng cho Admin
if (in_array($act, $admin_only) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    header("Location: index.php?act=dangnhap");
    exit;
}

// Điều hướng tới Controller nghiệp vụ bằng Tiếng Việt
switch ($act) {
    // 1. Module Tài khoản (Đăng ký, Đăng nhập, Đăng xuất)
    case 'dangky':
    case 'dangnhap':
    case 'dangxuat':
        require_once "c_taikhoan.php";
        break;

    // 2. Module Giỏ hàng, Voucher & Đặt hàng
    case 'giohang':
    case 'themgiohang':
    case 'capnhatgiohang':
    case 'xoagiohang':
    case 'xoatatcagiohang':
    case 'apvoucher':
    case 'xoavoucher':
    case 'thanhtoan':
    case 'xacnhandathang':
    case 'dathangthanhcong':
        require_once "c_giohang.php";
        break;

    // 3. Module Lịch sử đơn hàng, Tiến trình giao hàng (Tracking) & Hủy đơn
    case 'lichsudonhang':
    case 'chitietdonhang':
    case 'theodoigiaohang':
    case 'inhoadon':
    case 'huydonhang':
    case 'tracuudonhang':
    case 'xulytracuu':
        require_once "c_donhang.php";
        break;

    // 4. Module Quản trị (Admin & Nhân viên bán hàng)
    case 'danhmuc': case 'themdm': case 'suadm': case 'capnhatdm': case 'xoadm':
    case 'quanlysp': case 'themsp': case 'suasp': case 'capnhatsp': case 'xoasp':
    case 'khuyenmai': case 'capnhatkhuyenmai':
    case 'quanlyvoucher': case 'themvoucher': case 'suavoucher': case 'capnhatvoucher': case 'xoavoucher':
    case 'quanlydonhang': case 'doitrangthaidonhang':
    case 'quanlydanhgia': case 'doitrangthaidanhgia': case 'xoadanhgia':
    case 'quanlykh': case 'khoakh':
    case 'quanlynv': case 'themnv': case 'suanv': case 'capnhatnv': case 'xoanv':
    case 'doanhthu':
        require_once "c_quantri.php";
        break;

    // 5. Mặc định: Sản phẩm, Tìm kiếm, Phân trang, Chi tiết & Đánh giá
    default:
        require_once "c_sanpham.php";
        break;
}
?>