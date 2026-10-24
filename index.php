<?php
/**
 * ==============================================================================
 * BLOOMSHOP - HỆ THỐNG WEBSITE BÁN HOA TƯƠI TRỰC TUYẾN
 * FRONT CONTROLLER / ROUTER TRUNG TÂM (MÔ HÌNH MVC & OOP CHUẨN)
 * ==============================================================================
 * Luồng hoạt động (Lifecycle):
 * 1. Khách hàng gửi yêu cầu (Request qua URL, ví dụ: index.php?act=giohang)
 * 2. Router (index.php) tiếp nhận tham số 'act', kiểm tra phân quyền người dùng
 * 3. Router khởi tạo đối tượng Controller tương ứng (OOP Controller)
 * 4. Controller gọi các phương thức trong Model (OOP Model) để xử lý dữ liệu CSDL
 * 5. Controller nạp và truyền dữ liệu sang View (view/*.php) để kết xuất HTML
 * ==============================================================================
 */

// 1. Khởi động phiên làm việc Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Nạp tầng CSDL & Model nghiệp vụ (OOP Model Layer)
require_once __DIR__ . "/model/database.php";
require_once __DIR__ . "/model/BaseModel.php";
require_once __DIR__ . "/model/SanPhamModel.php";
require_once __DIR__ . "/model/DanhMucModel.php";
require_once __DIR__ . "/model/GioHangModel.php";
require_once __DIR__ . "/model/VoucherModel.php";
require_once __DIR__ . "/model/DonHangModel.php";
require_once __DIR__ . "/model/TaiKhoanModel.php";
require_once __DIR__ . "/model/ThongKeModel.php";
require_once __DIR__ . "/model/mailer.php";

// 3. Nạp tầng Controller điều hướng (OOP Controller Layer)
require_once __DIR__ . "/controller/BaseController.php";
require_once __DIR__ . "/controller/SanPhamController.php";
require_once __DIR__ . "/controller/GioHangController.php";
require_once __DIR__ . "/controller/DonHangController.php";
require_once __DIR__ . "/controller/TaiKhoanController.php";
require_once __DIR__ . "/controller/QuanTriController.php";

// 4. Lấy hành động được yêu cầu (Mặc định là 'trangchu')
$act = $_REQUEST['act'] ?? 'trangchu';

// 5. LỚP BẢO VỆ PHÂN QUYỀN (AUTHENTICATION & AUTHORIZATION MIDDLEWARE)
// Danh sách các chức năng dành cho Quản trị viên và Nhân viên (Staff & Admin)
$staff_allowed = [
    'danhmuc', 'themdm', 'suadm', 'capnhatdm', 'xoadm',
    'quanlysp', 'themsp', 'suasp', 'capnhatsp', 'xoasp',
    'khuyenmai', 'capnhatkhuyenmai',
    'quanlyvoucher', 'themvoucher', 'suavoucher', 'capnhatvoucher', 'xoavoucher',
    'quanlydanhgia', 'doitrangthaidanhgia', 'xoadanhgia',
    'quanlykh', 'khoakh',
    'quanlydonhang', 'doitrangthaidonhang', 'dongbodulieu'
];

// Danh sách các chức năng chỉ dành riêng cho Admin tối cao
$admin_only = [
    'quanlynv', 'themnv', 'suanv', 'capnhatnv', 'xoanv',
    'doanhthu'
];

// Kiểm tra quyền nhân viên & admin
if (in_array($act, $staff_allowed) && (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'staff']))) {
    $_SESSION['flash_error'] = 'Bạn cần đăng nhập bằng tài khoản Quản trị / Nhân viên để truy cập trang này.';
    header("Location: index.php?act=dangnhap");
    exit;
}

// Kiểm tra quyền riêng cho Admin
if (in_array($act, $admin_only) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    $_SESSION['flash_error'] = 'Khu vực này chỉ dành riêng cho Quản trị viên cấp cao (Admin).';
    header("Location: index.php?act=dangnhap");
    exit;
}

// ==============================================================================
// 6. ĐIỀU PHỐI YÊU CẦU ĐẾN CÁC ĐỐI TƯỢNG CONTROLLER (OOP DISPATCHER)
// ==============================================================================
switch ($act) {

    // --------------------------------------------------------------------------
    // PHÂN HỆ 1: TÀI KHOẢN KHÁCH HÀNG (ĐĂNG KÝ, ĐĂNG NHẬP, ĐĂNG XUẤT)
    // --------------------------------------------------------------------------
    case 'dangky':
        (new TaiKhoanController())->dangKy();
        break;
    case 'dangnhap':
        (new TaiKhoanController())->dangNhap();
        break;
    case 'dangxuat':
        (new TaiKhoanController())->dangXuat();
        break;

    // --------------------------------------------------------------------------
    // PHÂN HỆ 2: GIỎ HÀNG, MÃ VOUCHER & THANH TOÁN ĐẶT HÀNG
    // --------------------------------------------------------------------------
    case 'giohang':
        (new GioHangController())->xemGioHang();
        break;
    case 'themgiohang':
        (new GioHangController())->themGioHang();
        break;
    case 'capnhatgiohang':
        (new GioHangController())->capNhatGioHang();
        break;
    case 'xoagiohang':
        (new GioHangController())->xoaGioHang();
        break;
    case 'xoatatcagiohang':
        (new GioHangController())->xoaTatCaGioHang();
        break;
    case 'apvoucher':
        (new GioHangController())->apVoucher();
        break;
    case 'bovoucher':
        (new GioHangController())->boVoucher();
        break;
    case 'thanhtoan':
        (new GioHangController())->thanhToan();
        break;
    case 'xacnhandathang':
        (new GioHangController())->xacNhanDatHang();
        break;
    case 'dathangthanhcong':
        (new GioHangController())->datHangThanhCong();
        break;

    // --------------------------------------------------------------------------
    // PHÂN HỆ 3: ĐƠN HÀNG, TIẾN TRÌNH TRACKING & HÓA ĐƠN
    // --------------------------------------------------------------------------
    case 'lichsudonhang':
        (new DonHangController())->lichSuDonHang();
        break;
    case 'chitietdonhang':
        (new DonHangController())->chiTietDonHang();
        break;
    case 'theodoigiaohang':
        (new DonHangController())->theoDoiGiaoHang();
        break;
    case 'inhoadon':
        (new DonHangController())->inHoaDon();
        break;
    case 'huydonhang':
        (new DonHangController())->huyDonHang();
        break;
    case 'tracuudonhang':
        (new DonHangController())->traCuuDonHang();
        break;
    case 'xulytracuu':
        (new DonHangController())->xuLyTraCuu();
        break;

    // --------------------------------------------------------------------------
    // PHÂN HỆ 4: QUẢN TRỊ VIÊN & NHÂN VIÊN (ADMIN & STAFF MANAGEMENT)
    // --------------------------------------------------------------------------
    // Quản lý Danh mục
    case 'danhmuc':
        (new QuanTriController())->danhmuc();
        break;
    case 'themdm':
        (new QuanTriController())->themdm();
        break;
    case 'suadm':
        (new QuanTriController())->suadm();
        break;
    case 'capnhatdm':
        (new QuanTriController())->capnhatdm();
        break;
    case 'xoadm':
        (new QuanTriController())->xoadm();
        break;

    // Quản lý Sản phẩm
    case 'quanlysp':
        (new QuanTriController())->quanlysp();
        break;
    case 'themsp':
        (new QuanTriController())->themsp();
        break;
    case 'suasp':
        (new QuanTriController())->suasp();
        break;
    case 'capnhatsp':
        (new QuanTriController())->capnhatsp();
        break;
    case 'xoasp':
        (new QuanTriController())->xoasp();
        break;

    // Quản lý Khuyến mãi
    case 'khuyenmai':
        (new QuanTriController())->khuyenMai();
        break;
    case 'capnhatkhuyenmai':
        (new QuanTriController())->capNhatKhuyenMai();
        break;

    // Quản lý Voucher
    case 'quanlyvoucher':
        (new QuanTriController())->quanLyVoucher();
        break;
    case 'themvoucher':
        (new QuanTriController())->themVoucher();
        break;
    case 'suavoucher':
        (new QuanTriController())->suaVoucher();
        break;
    case 'capnhatvoucher':
        (new QuanTriController())->capNhatVoucher();
        break;
    case 'xoavoucher':
        (new QuanTriController())->xoaVoucher();
        break;

    // Quản lý Đơn hàng
    case 'quanlydonhang':
        (new QuanTriController())->quanLyDonHang();
        break;
    case 'doitrangthaidonhang':
        (new QuanTriController())->doiTrangThaiDonHang();
        break;

    // Quản lý Đánh giá sản phẩm
    case 'quanlydanhgia':
        (new QuanTriController())->quanLyDanhGia();
        break;
    case 'doitrangthaidanhgia':
        (new QuanTriController())->doiTrangThaiDanhGia();
        break;
    case 'xoadanhgia':
        (new QuanTriController())->xoaDanhGia();
        break;

    // Quản lý Khách hàng
    case 'quanlykh':
        (new QuanTriController())->quanLyKh();
        break;
    case 'khoakh':
        (new QuanTriController())->khoaKh();
        break;

    // Quản lý Nhân viên (Admin Only)
    case 'quanlynv':
        (new QuanTriController())->quanLyNv();
        break;
    case 'themnv':
        (new QuanTriController())->themNv();
        break;
    case 'suanv':
        (new QuanTriController())->suaNv();
        break;
    case 'capnhatnv':
        (new QuanTriController())->capNhatNv();
        break;
    case 'xoanv':
        (new QuanTriController())->xoaNv();
        break;

    // Báo cáo Doanh thu & Thống kê (Admin Only)
    case 'doanhthu':
        (new QuanTriController())->doanhthu();
        break;

    // Tự động đồng bộ CSDL ra bloomshop.sql cho Hosting
    case 'dongbodulieu':
        (new QuanTriController())->dongbodulieu();
        break;

    // --------------------------------------------------------------------------
    // PHÂN HỆ 5: KHÁCH HÀNG - SẢN PHẨM, TÌM KIẾM, CHI TIẾT & TRANG CHỦ
    // --------------------------------------------------------------------------
    case 'sanpham':
        (new SanPhamController())->danhSach();
        break;
    case 'chitiettsp':
    case 'chitietsp':
        (new SanPhamController())->chiTiet();
        break;
    case 'guidanhgia':
        (new SanPhamController())->guiDanhGia();
        break;
    case 'trangchu':
    default:
        (new SanPhamController())->trangChu();
        break;
}
