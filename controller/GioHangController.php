<?php
// controller/GioHangController.php - Controller điều hướng Giỏ hàng, Voucher & Thanh toán theo OOP
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/GioHangModel.php';
require_once __DIR__ . '/../model/SanPhamModel.php';
require_once __DIR__ . '/../model/VoucherModel.php';
require_once __DIR__ . '/../model/DonHangModel.php';
require_once __DIR__ . '/../model/TaiKhoanModel.php';
require_once __DIR__ . '/../model/mailer.php';

class GioHangController extends BaseController
{
    private $gioHangModel;
    private $sanPhamModel;
    private $voucherModel;
    private $donHangModel;
    private $taiKhoanModel;

    public function __construct()
    {
        parent::__construct();
        $this->gioHangModel = new GioHangModel();
        $this->sanPhamModel = new SanPhamModel();
        $this->voucherModel = new VoucherModel();
        $this->donHangModel = new DonHangModel();
        $this->taiKhoanModel = new TaiKhoanModel();
    }

    /**
     * Hiển thị giao diện giỏ hàng
     */
    public function xemGioHang()
    {
        $ds_voucher = $this->voucherModel->getAvailableVouchers();
        $this->renderView('giohang.php', [
            'ds_voucher' => $ds_voucher
        ]);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function themGioHang()
    {
        $id = (int)$this->post('id', 0);
        $qty = max(1, (int)$this->post('qty', 1));

        $sp = $this->sanPhamModel->getProductById($id);
        $current = (int)($_SESSION['cart'][$id]['qty'] ?? 0);
        if (!$sp || (int)$sp['so_luong'] < $current + $qty) {
            $this->setFlashError('Sản phẩm không đủ tồn kho.');
            $this->redirect('index.php?act=giohang');
        }
        if ($sp) {
            $this->gioHangModel->addItem($id, $qty, $sp);

            if (!empty($_POST['muangay'])) {
                $this->redirect('index.php?act=thanhtoan');
            }
        }
        $this->redirect('index.php?act=giohang');
    }

    /**
     * Cập nhật số lượng (+ / -)
     */
    public function capNhatGioHang()
    {
        $id = (int)$this->post('id', 0);
        $action = $this->post('action', '');

        $sp = $this->sanPhamModel->getProductById($id);
        if ($action === 'increase' && (!$sp || (int)$sp['so_luong'] <= (int)($_SESSION['cart'][$id]['qty'] ?? 0))) {
            $this->setFlashError('Đã đạt số lượng tồn kho.');
        } else {
            $this->gioHangModel->updateItemQty($id, $action);
        }
        $this->redirect('index.php?act=giohang');
    }

    /**
     * Xóa 1 sản phẩm khỏi giỏ hàng
     */
    public function xoaGioHang()
    {
        $id = (int)$this->get('id', 0);
        $this->gioHangModel->removeItem($id);
        $this->redirect('index.php?act=giohang');
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function xoaTatCaGioHang()
    {
        $this->gioHangModel->clear();
        $this->redirect('index.php?act=giohang');
    }

    /**
     * Áp dụng mã giảm giá (Voucher)
     */
    public function apVoucher()
    {
        $code = strtoupper(trim($this->post('voucher_code', '')));
        $subtotal = $this->gioHangModel->getSubtotal();

        $vc = $this->voucherModel->findByCode($code);

        if (!$vc) {
            $this->loi = "Mã voucher '$code' không tồn tại hoặc đã bị khóa.";
        } elseif (strtotime($vc['expiry_date']) < strtotime(date('Y-m-d'))) {
            $this->loi = "Mã voucher '$code' đã hết hạn sử dụng.";
        } elseif ($vc['used_count'] >= $vc['quantity']) {
            $this->loi = "Mã voucher '$code' đã hết lượt sử dụng.";
        } elseif ($subtotal < (float)$vc['min_order_value']) {
            $this->loi = "Đơn hàng tối thiểu để áp dụng mã này là " . number_format($vc['min_order_value'], 0, ',', '.') . "đ.";
        } else {
            $this->gioHangModel->setVoucher([
                'id' => $vc['id'],
                'code' => $vc['code'],
                'discount_type' => $vc['discount_type'],
                'discount_value' => (float)$vc['discount_value'],
                'min_order_value' => (float)$vc['min_order_value']
            ]);
            $this->thanhcong = "Áp dụng mã giảm giá '$code' thành công!";
        }

        $ds_voucher = $this->voucherModel->getAvailableVouchers();
        $this->renderView('giohang.php', [
            'ds_voucher' => $ds_voucher
        ]);
    }

    /**
     * Hủy áp dụng voucher
     */
    public function boVoucher()
    {
        $this->gioHangModel->removeVoucher();
        $this->redirect('index.php?act=giohang');
    }

    /**
     * Màn hình thanh toán
     */
    public function thanhToan()
    {
        $cart_items = $this->gioHangModel->getItems();
        if (empty($cart_items)) {
            $this->redirect('index.php?act=giohang');
        }

        $current_user = null;
        if (isset($_SESSION['id'])) {
            $current_user = $this->taiKhoanModel->findById($_SESSION['id']);
        }

        $this->renderView('thanhtoan.php', [
            'current_user' => $current_user
        ]);
    }

    /**
     * Xác nhận đặt hàng & Gửi email hóa đơn
     */
    public function xacNhanDatHang()
    {
        if (!$this->isPost() || !isset($_POST['dathang'])) {
            $this->redirect('index.php?act=giohang');
        }

        $cart_items = $this->gioHangModel->getItems();
        if (empty($cart_items)) {
            $this->redirect('index.php?act=giohang');
        }

        $customer_name = trim($this->post('customer_name', ''));
        $customer_phone = trim($this->post('customer_phone', ''));
        $customer_email = trim($this->post('customer_email', ''));
        $customer_address = trim($this->post('customer_address', ''));
        $note = trim($this->post('note', ''));
        $payment_method = $this->post('payment_method', 'COD');
        $user_id = $_SESSION['id'] ?? null;

        if (empty($customer_name) || empty($customer_phone) || empty($customer_address)) {
            $this->setFlashError("Vui lòng nhập đầy đủ họ tên, số điện thoại và địa chỉ nhận hàng!");
            $this->redirect('index.php?act=thanhtoan');
        }

        $subtotal = $this->gioHangModel->getSubtotal();
        $discount_data = $this->gioHangModel->calculateDiscount($subtotal);
        $discount_amount = $discount_data['amount'];
        $voucher_code = $discount_data['code'];
        $voucher_id = $discount_data['id'];

        $total_price = max(0, $subtotal - $discount_amount);

        try {
            [$order_id, $order_items] = $this->donHangModel->placeOrderSafely([
                'user_id' => $user_id, 'name' => $customer_name, 'email' => $customer_email,
                'phone' => $customer_phone, 'address' => $customer_address, 'note' => $note,
                'subtotal' => $subtotal, 'voucher' => $voucher_code,
                'discount' => $discount_amount, 'total' => $total_price, 'payment' => $payment_method
            ], $cart_items, $voucher_id);
        } catch (Throwable $e) {
            $this->setFlashError($e->getMessage());
            $this->redirect('index.php?act=giohang');
        }

        // Gửi email hóa đơn tự động
        $order_info = $this->donHangModel->getOrderById($order_id);
        try {
            Mailer::send_order_invoice($order_info, $order_items);
        } catch (Throwable $e) {
            error_log('BloomShop mail failed after order #' . $order_id . ': ' . $e->getMessage());
        }

        // Xóa giỏ hàng sau khi đặt thành công
        $this->gioHangModel->clear();

        $this->redirect("index.php?act=dathangthanhcong&id=$order_id");
    }

    /**
     * Màn hình thông báo đặt hàng thành công
     */
    public function datHangThanhCong()
    {
        $id = (int)$this->get('id', 0);
        $order = $this->donHangModel->getOrderById($id);
        if (!$order) {
            $this->redirect('index.php?act=trangchu');
        }

        $this->renderView('dathangthanhcong.php', [
            'order' => $order
        ]);
    }
}
