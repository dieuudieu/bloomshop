<?php
// controller/QuanTriController.php - Controller điều hướng toàn bộ Quản trị Admin & Staff theo OOP
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/DanhMucModel.php';
require_once __DIR__ . '/../model/SanPhamModel.php';
require_once __DIR__ . '/../model/VoucherModel.php';
require_once __DIR__ . '/../model/DonHangModel.php';
require_once __DIR__ . '/../model/TaiKhoanModel.php';
require_once __DIR__ . '/../model/ThongKeModel.php';

class QuanTriController extends BaseController
{
    private $danhMucModel;
    private $sanPhamModel;
    private $voucherModel;
    private $donHangModel;
    private $taiKhoanModel;
    private $thongKeModel;

    public function __construct()
    {
        parent::__construct();
        $this->danhMucModel = new DanhMucModel();
        $this->sanPhamModel = new SanPhamModel();
        $this->voucherModel = new VoucherModel();
        $this->donHangModel = new DonHangModel();
        $this->taiKhoanModel = new TaiKhoanModel();
        $this->thongKeModel = new ThongKeModel();
    }

    /**
     * Phương thức xử lý upload file ảnh an toàn (Hỗ trợ 100% trên cả Localhost & Linux Hosting)
     */
    private function uploadProductImage($inputName = 'image')
    {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES[$inputName];
        $duoi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($duoi, $allowed)) {
            return false;
        }

        // Kiểm tra an toàn file ảnh không phụ thuộc vào tiện ích mở rộng của Hosting
        $isImage = false;
        if (function_exists('getimagesize')) {
            $info = @getimagesize($file['tmp_name']);
            if ($info !== false) {
                $isImage = true;
            }
        } elseif (function_exists('mime_content_type')) {
            $mime = @mime_content_type($file['tmp_name']);
            if ($mime && strpos($mime, 'image/') === 0) {
                $isImage = true;
            }
        } else {
            $isImage = true;
        }

        if (!$isImage) {
            return false;
        }

        $targetDir = __DIR__ . '/../view/image/sanpham/';
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $newFileName = uniqid('hoa_') . '.' . $duoi;
        $dest = $targetDir . $newFileName;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            @chmod($dest, 0644); // Cấp quyền đọc ảnh trên web server Linux Hosting
            return $newFileName;
        }

        return false;
    }

    // ==============================================================
    // 1. QUẢN LÝ NHÂN VIÊN (ADMIN ONLY)
    // ==============================================================
    public function quanLyNv()
    {
        $danhsach_nv = $this->taiKhoanModel->getAllStaff();
        $this->renderView('admin_nhanvien.php', ['danhsach_nv' => $danhsach_nv]);
    }

    public function themNv()
    {
        if ($this->isPost() && isset($_POST['themnv'])) {
            $name = trim($this->post('name', ''));
            $email = trim($this->post('email', ''));
            $phone = trim($this->post('phone', ''));
            $password = $this->post('password', '');

            $check = $this->taiKhoanModel->findByEmail($email);
            if ($check) {
                $this->loi = 'Email nhân viên này đã tồn tại trong hệ thống.';
            } else {
                $this->taiKhoanModel->createStaff($name, $email, $phone, $password);
                $this->setFlashSuccess("Thêm nhân viên mới thành công!");
                $this->redirect('index.php?act=quanlynv');
            }
        }
        $this->renderView('admin_themnv.php');
    }

    public function suaNv()
    {
        $id = (int)$this->get('id', 0);
        $nv = $this->taiKhoanModel->findById($id);
        if ($nv && ($nv['role'] ?? '') === 'staff') {
            $this->renderView('admin_suanv.php', ['nv' => $nv]);
        }
        $this->redirect('index.php?act=quanlynv');
    }

    public function capNhatNv()
    {
        if ($this->isPost() && isset($_POST['capnhatnv'])) {
            $id = (int)$this->post('id', 0);
            $name = trim($this->post('name', ''));
            $email = trim($this->post('email', ''));
            $phone = trim($this->post('phone', ''));
            $status = (int)$this->post('status', 1);
            $password = $this->post('password', '');

            $this->taiKhoanModel->updateStaff($id, $name, $email, $phone, $status, $password);
            $this->setFlashSuccess("Cập nhật thông tin nhân viên thành công!");
        }
        $this->redirect('index.php?act=quanlynv');
    }

    public function xoaNv()
    {
        $id = (int)($this->post('id') ?? $this->get('id', 0));
        if ($id > 0) {
            if (isset($_SESSION['id']) && (int)$_SESSION['id'] === $id) {
                $this->setFlashError("Ràng buộc bảo mật: Bạn không thể tự xóa tài khoản của chính mình khi đang đăng nhập!");
            } else {
                try {
                    $this->taiKhoanModel->deleteStaff($id);
                    $this->setFlashSuccess("Đã xóa nhân viên thành công!");
                } catch (Exception $e) {
                    $this->setFlashError($e->getMessage());
                }
            }
        }
        $this->redirect('index.php?act=quanlynv');
    }

    // ==============================================================
    // 2. QUẢN LÝ ĐÁNH GIÁ (ẨN/HIỆN, XÓA)
    // ==============================================================
    public function quanLyDanhGia()
    {
        $star = $this->get('star', null);
        $danhsach_dg = $this->sanPhamModel->getAllReviewsAdmin($star);
        $this->renderView('admin_danhgia.php', ['danhsach_dg' => $danhsach_dg]);
    }

    public function doiTrangThaiDanhGia()
    {
        $id = (int)($this->post('id') ?? $this->get('id', 0));
        $status = (int)($this->post('status') ?? $this->get('status', 1));
        $this->sanPhamModel->updateReviewStatus($id, $status);
        $this->setFlashSuccess("Đã cập nhật trạng thái hiển thị đánh giá!");
        $this->redirect('index.php?act=quanlydanhgia');
    }

    public function xoaDanhGia()
    {
        $id = (int)($this->post('id') ?? $this->get('id', 0));
        if ($id > 0) {
            $this->sanPhamModel->deleteReview($id);
            $this->setFlashSuccess("Đã xóa đánh giá thành công!");
        }
        $this->redirect('index.php?act=quanlydanhgia');
    }

    // ==============================================================
    // 3. QUẢN LÝ KHÁCH HÀNG (KHÓA/MỞ TÀI KHOẢN)
    // ==============================================================
    public function quanLyKh()
    {
        $tier = $this->get('tier', '');
        $danhsach_kh = $this->taiKhoanModel->getAllCustomers($tier);
        $tier_counts = $this->taiKhoanModel->countCustomersByTier();

        $this->renderView('admin_khachhang.php', [
            'danhsach_kh' => $danhsach_kh,
            'count_diamond' => $tier_counts['diamond'],
            'count_gold' => $tier_counts['gold'],
            'count_silver' => $tier_counts['silver']
        ]);
    }

    public function khoaKh()
    {
        $id = (int)($this->post('id') ?? $this->get('id', 0));
        $status = (int)($this->post('status') ?? $this->get('status', 1));
        $this->taiKhoanModel->updateCustomerStatus($id, $status);
        $msg = ($status === 1) ? "Đã mở khóa tài khoản khách hàng thành công!" : "Đã khóa tài khoản khách hàng thành công!";
        $this->setFlashSuccess($msg);
        $this->redirect('index.php?act=quanlykh');
    }

    // ==============================================================
    // 4. QUẢN LÝ VOUCHER (THÊM, SỬA, XÓA)
    // ==============================================================
    public function quanLyVoucher()
    {
        $danhsach_vc = $this->voucherModel->getAllVouchers();
        $this->renderView('admin_voucher.php', ['danhsach_vc' => $danhsach_vc]);
    }

    public function themVoucher()
    {
        if ($this->isPost() && isset($_POST['them'])) {
            $code = strtoupper(trim($this->post('code', '')));
            $discount_type = $this->post('discount_type', 'fixed');
            $discount_value = (float)$this->post('discount_value', 0);
            $min_order_value = (float)$this->post('min_order_value', 0);
            $quantity = (int)$this->post('quantity', 0);
            $expiry_date = $this->post('expiry_date', '');

            $check = $this->voucherModel->findByCode($code);
            if ($check) {
                $this->loi = "Mã voucher '$code' đã tồn tại!";
            } else {
                $this->voucherModel->createVoucher($code, $discount_type, $discount_value, $min_order_value, $quantity, $expiry_date);
                $this->setFlashSuccess("Thêm mã voucher '$code' thành công!");
                $this->redirect('index.php?act=quanlyvoucher');
            }
        }
        $this->renderView('admin_themvoucher.php');
    }

    public function suaVoucher()
    {
        $id = (int)$this->get('id', 0);
        $vc = $this->voucherModel->getById($id);
        if ($vc) {
            $this->renderView('admin_suavoucher.php', ['vc' => $vc]);
        }
        $this->redirect('index.php?act=quanlyvoucher');
    }

    public function capNhatVoucher()
    {
        if ($this->isPost() && isset($_POST['capnhat'])) {
            $id = (int)$this->post('id', 0);
            $code = strtoupper(trim($this->post('code', '')));
            $discount_type = $this->post('discount_type', 'fixed');
            $discount_value = (float)$this->post('discount_value', 0);
            $min_order_value = (float)$this->post('min_order_value', 0);
            $quantity = (int)$this->post('quantity', 0);
            $expiry_date = $this->post('expiry_date', '');
            $status = (int)$this->post('status', 1);

            $this->voucherModel->updateVoucher($id, $code, $discount_type, $discount_value, $min_order_value, $quantity, $expiry_date, $status);
            $this->setFlashSuccess("Cập nhật mã voucher '$code' thành công!");
        }
        $this->redirect('index.php?act=quanlyvoucher');
    }

    public function xoaVoucher()
    {
        $id = (int)($this->post('id') ?? $this->get('id', 0));
        if ($id > 0) {
            try {
                $this->voucherModel->deleteVoucher($id);
                $this->setFlashSuccess("Đã xóa voucher thành công!");
            } catch (Exception $e) {
                $this->setFlashError($e->getMessage());
            }
        }
        $this->redirect('index.php?act=quanlyvoucher');
    }

    // ==============================================================
    // 5. KHUYẾN MÃI SẢN PHẨM
    // ==============================================================
    public function khuyenMai()
    {
        $danhsach_sp = $this->sanPhamModel->getAllProductsWithSale();
        $this->renderView('admin_khuyenmai.php', ['danhsach_sp' => $danhsach_sp]);
    }

    public function capNhatKhuyenMai()
    {
        if ($this->isPost()) {
            $id = (int)$this->post('id', 0);
            $sale_price = (!empty($_POST['sale_price']) && (float)$_POST['sale_price'] > 0) ? (float)$_POST['sale_price'] : null;
            $is_sale = isset($_POST['is_sale']) ? 1 : 0;

            $this->sanPhamModel->updateSale($id, $sale_price, $is_sale);
            $this->setFlashSuccess("Cập nhật khuyến mãi thành công!");
        }
        $this->redirect('index.php?act=khuyenmai');
    }

    // ==============================================================
    // 6. QUẢN LÝ ĐƠN HÀNG (STATE MACHINE QUY TRÌNH TMĐT)
    // ==============================================================
    public function quanLyDonHang()
    {
        $status = $this->get('status', '');
        $danhsach_dh = $this->donHangModel->getAllOrders($status);
        $this->renderView('admin_donhang.php', ['danhsach_dh' => $danhsach_dh]);
    }

    public function doiTrangThaiDonHang()
    {
        if ($this->isPost() && isset($_POST['capnhat_tt'])) {
            $order_id = (int)$this->post('order_id', 0);
            $new_status = $this->post('new_status', '');

            $order = $this->donHangModel->getOrderById($order_id);
            if (!$order) {
                $this->redirect('index.php?act=quanlydonhang');
            }

            $current_status = $order['status'];

            $allowed_transitions = [
                'cho_xac_nhan' => ['xac_nhan', 'da_huy'],
                'xac_nhan'     => ['dang_giao', 'da_huy'],
                'dang_giao'    => ['da_giao', 'da_huy'],
                'da_giao'      => ['thanh_cong'],
                'thanh_cong'   => [],
                'da_huy'       => []
            ];

            $status_names = [
                'cho_xac_nhan' => 'Chờ xác nhận',
                'xac_nhan'     => 'Xác nhận đơn hàng',
                'dang_giao'    => 'Đang giao',
                'da_giao'      => 'Đã giao',
                'thanh_cong'   => 'Thành công',
                'da_huy'       => 'Đã hủy'
            ];

            if ($current_status === $new_status) {
                $this->redirect('index.php?act=quanlydonhang');
            }

            if (!isset($allowed_transitions[$current_status]) || !in_array($new_status, $allowed_transitions[$current_status])) {
                $curr_name = $status_names[$current_status] ?? $current_status;
                $new_name = $status_names[$new_status] ?? $new_status;
                $this->setFlashError("Quy tắc vận hành: Đơn hàng đang ở bước '$curr_name' không thể quay lại bước trước hoặc chuyển sang '$new_name'!");
                $this->redirect('index.php?act=quanlydonhang');
            }

            $status_desc = [
                'xac_nhan'     => 'BloomShop đã xác nhận đơn hàng và đang cắm hoa tươi chuẩn bị xuất kho.',
                'dang_giao'    => 'Hoa tươi đã xuất kho và bàn giao cho Shipper đang trên đường vận chuyển tới địa chỉ nhận.',
                'da_giao'      => 'Shipper đã giao hoa tươi thành công đến tận tay người nhận.',
                'thanh_cong'   => 'Đơn hàng hoàn tất và quyết toán thành công. Khách hàng đã được tích lũy chi tiêu VIP!',
                'da_huy'       => 'Đơn hàng đã bị hủy. ' . (isset($_SESSION['name']) ? "Thực hiện bởi: {$_SESSION['name']}." : '')
            ];

            $desc = $status_desc[$new_status] ?? 'Trạng thái đơn hàng được cập nhật.';

            try {
                $this->donHangModel->changeStatusSafely($order_id, $current_status, $new_status, $desc);
            } catch (Throwable $e) {
                $this->setFlashError($e->getMessage());
                $this->redirect('index.php?act=quanlydonhang');
            }

            // Tự động thăng hạng khách hàng khi đơn thành công
            if (!empty($order['user_id']) && $new_status === 'thanh_cong') {
                $this->donHangModel->recalculate_user_tier($order['user_id']);
            }

            $this->setFlashSuccess("Cập nhật đơn hàng #$order_id sang trạng thái '{$status_names[$new_status]}' thành công!");
        }
        $this->redirect('index.php?act=quanlydonhang');
    }

    // ==============================================================
    // 7. DANH MỤC & SẢN PHẨM (THÊM, SỬA, XÓA)
    // ==============================================================
    public function danhmuc()
    {
        $danhsach = $this->danhMucModel->getAll();
        $this->renderView('danhmuc.php', ['danhsach' => $danhsach]);
    }

    public function themdm()
    {
        if ($this->isPost() && isset($_POST['them'])) {
            $name = trim($this->post('name', ''));
            if (!empty($name)) {
                $this->danhMucModel->create($name);
                $this->setFlashSuccess("Thêm danh mục '$name' thành công!");
            }
            $this->redirect('index.php?act=danhmuc');
        }
        $this->renderView('themdm.php');
    }

    public function suadm()
    {
        $id = (int)$this->get('id', 0);
        $dm = $this->danhMucModel->getById($id);
        if ($dm) {
            $this->renderView('suadm.php', ['dm' => $dm]);
        }
        $this->redirect('index.php?act=danhmuc');
    }

    public function capnhatdm()
    {
        if ($this->isPost() && isset($_POST['capnhat'])) {
            $id = (int)$this->post('id', 0);
            $name = trim($this->post('name', ''));
            if (!empty($name)) {
                $this->danhMucModel->update($id, $name);
                $this->setFlashSuccess("Cập nhật danh mục thành công!");
            }
        }
        $this->redirect('index.php?act=danhmuc');
    }

    public function xoadm()
    {
        $id = (int)($this->post('id') ?? $this->get('id', 0));
        try {
            $this->danhMucModel->delete($id);
            $this->setFlashSuccess("Đã xóa danh mục thành công!");
        } catch (Exception $e) {
            $this->setFlashError($e->getMessage());
        }
        $this->redirect('index.php?act=danhmuc');
    }

    public function quanlysp()
    {
        $danhsach = $this->sanPhamModel->getAllProductsAdmin();
        $this->renderView('sanpham.php', ['danhsach' => $danhsach]);
    }

    public function themsp()
    {
        if ($this->isPost() && isset($_POST['them'])) {
            $name = trim($this->post('name', ''));
            $price = (float)$this->post('price', 0);
            $sale_price = (!empty($_POST['sale_price']) && (float)$_POST['sale_price'] > 0) ? (float)$_POST['sale_price'] : null;
            $is_sale = isset($_POST['is_sale']) ? 1 : 0;
            $description = trim($this->post('description', ''));
            $id_dm = (int)$this->post('id_dm', 0);
            $so_luong = max(0, (int)$this->post('so_luong', 0));

            $uploadedImage = $this->uploadProductImage('image');

            if ($uploadedImage === false) {
                $this->loi = 'Vui lòng chọn ảnh JPG, PNG hoặc WebP hợp lệ.';
            } elseif ($uploadedImage !== null) {
                $this->sanPhamModel->createProduct($name, $price, $sale_price, $is_sale, $description, $uploadedImage, $id_dm, $so_luong);
                $this->setFlashSuccess("Thêm sản phẩm hoa mới thành công!");
                $this->redirect('index.php?act=quanlysp');
            } else {
                $this->loi = 'Vui lòng chọn file ảnh sản phẩm.';
            }
        }
        $danhsach = $this->danhMucModel->getAll();
        $this->renderView('themsp.php', ['danhsach' => $danhsach]);
    }

    public function suasp()
    {
        $id = (int)$this->get('id', 0);
        $sp = $this->sanPhamModel->getProductById($id);
        if ($sp) {
            $danhmuc = $this->danhMucModel->getAll();
            $this->renderView('suasp.php', ['sp' => $sp, 'danhmuc' => $danhmuc]);
        }
        $this->redirect('index.php?act=quanlysp');
    }

    public function capnhatsp()
    {
        if ($this->isPost() && isset($_POST['capnhat'])) {
            $id = (int)$this->post('id', 0);
            $name = trim($this->post('name', ''));
            $price = (float)$this->post('price', 0);
            $sale_price = (!empty($_POST['sale_price']) && (float)$_POST['sale_price'] > 0) ? (float)$_POST['sale_price'] : null;
            $is_sale = isset($_POST['is_sale']) ? 1 : 0;
            $description = trim($this->post('description', ''));
            $id_dm = (int)$this->post('id_dm', 0);
            $so_luong = max(0, (int)$this->post('so_luong', 0));

            // Kiểm tra có upload ảnh mới không
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $newImage = $this->uploadProductImage('image');
                if ($newImage === false) {
                    $this->setFlashError("Ảnh tải lên không hợp lệ (chỉ hỗ trợ JPG, PNG, WebP).");
                    $this->redirect("index.php?act=suasp&id=$id");
                }
                $this->sanPhamModel->updateProduct($id, $name, $price, $sale_price, $is_sale, $description, $id_dm, $newImage, $so_luong);
            } else {
                $this->sanPhamModel->updateProduct($id, $name, $price, $sale_price, $is_sale, $description, $id_dm, null, $so_luong);
            }

            $this->setFlashSuccess("Cập nhật thông tin sản phẩm thành công!");
            $this->redirect('index.php?act=quanlysp');
        }
        $this->redirect('index.php?act=quanlysp');
    }

    public function xoasp()
    {
        $id = (int)($this->post('id') ?? $this->get('id', 0));
        if ($id > 0) {
            try {
                $this->sanPhamModel->deleteProduct($id);
                $this->setFlashSuccess("Đã xóa sản phẩm thành công!");
            } catch (Exception $e) {
                $this->setFlashError($e->getMessage());
            }
        }
        $this->redirect('index.php?act=quanlysp');
    }

    // ==============================================================
    // 8. ĐỒNG BỘ CSDL CHO HOSTING (1-CLICK SYNC)
    // ==============================================================
    public function dongbodulieu()
    {
        $res = (new BaseModel())->exportSqlDump();
        if ($res) {
            $this->setFlashSuccess("Đồng bộ toàn bộ cơ sở dữ liệu mới nhất ra file 'bloomshop.sql' thành công! Dữ liệu đã sẵn sàng để đẩy lên hosting.");
        } else {
            $this->setFlashError("Không thể ghi file bloomshop.sql. Vui lòng kiểm tra quyền ghi thư mục.");
        }
        $this->redirect('index.php?act=quanlysp');
    }

    // ==============================================================
    // 9. BÁO CÁO DOANH THU & THỐNG KÊ (ADMIN ONLY)
    // ==============================================================
    public function doanhthu()
    {
        $overview = $this->thongKeModel->getOverview();
        $stats_7days = $this->thongKeModel->getStats7Days();
        $stats_months = $this->thongKeModel->getStatsMonths();
        $stats_years = $this->thongKeModel->getStatsYears();
        $sp_banchay = $this->thongKeModel->getBestSellers(5);
        $sp_enhat = $this->thongKeModel->getLeastSellers(6);

        $this->renderView('admin_doanhthu.php', [
            'tong_doanhthu' => $overview['tong_doanhthu'],
            'tong_don_thanhcong' => $overview['tong_don_thanhcong'],
            'doanhthu_tuan' => $overview['doanhthu_tuan'],
            'doanhthu_thang' => $overview['doanhthu_thang'],
            'doanhthu_nam' => $overview['doanhthu_nam'],
            'stats_7days' => $stats_7days,
            'stats_months' => $stats_months,
            'stats_years' => $stats_years,
            'sp_banchay' => $sp_banchay,
            'sp_enhat' => $sp_enhat
        ]);
    }
}
