<?php
// controller/DonHangController.php - Controller điều hướng Lịch sử đơn hàng, Tracking & Tra cứu theo OOP
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/DonHangModel.php';
require_once __DIR__ . '/../model/VoucherModel.php';

class DonHangController extends BaseController
{
    private $donHangModel;
    private $voucherModel;

    public function __construct()
    {
        parent::__construct();
        $this->donHangModel = new DonHangModel();
        $this->voucherModel = new VoucherModel();
    }

    /**
     * Hủy đơn hàng (Chỉ cho phép hủy khi đơn đang ở trạng thái 'cho_xac_nhan')
     */
    public function huyDonHang()
    {
        if (!$this->isPost() || !isset($_POST['huydonhang'])) {
            $this->redirect('index.php?act=lichsudonhang');
        }

        $order_id = (int)$this->post('order_id', 0);
        $order = $this->donHangModel->getOrderById($order_id);

        $is_admin = isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'staff']);

        // Khách hàng chỉ được phép hủy đơn hàng của chính mình
        if (!$is_admin && (!isset($_SESSION['id']) || empty($order['user_id']) || $order['user_id'] != $_SESSION['id'])) {
            $this->setFlashError("Bạn không có quyền thao tác trên đơn hàng này.");
            $this->redirect('index.php?act=lichsudonhang');
        }

        if ($order && $order['status'] === 'cho_xac_nhan') {
            $actor = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'Quản trị viên' : 'Khách hàng';
            try {
                $this->donHangModel->changeStatusSafely($order_id, 'cho_xac_nhan', 'da_huy', "$actor đã hủy đơn hàng.");
                $this->setFlashSuccess("Đã hủy đơn hàng #$order_id và hoàn tồn kho/voucher.");
            } catch (Throwable $e) {
                $this->setFlashError($e->getMessage());
            }
        } else {
            $st_hientai = $order ? $order['status'] : '';
            if (in_array($st_hientai, ['xac_nhan', 'dang_giao'])) {
                $this->setFlashError("Đơn hàng #$order_id đã được xác nhận / đang cắm hoa và giao hàng. Theo quy định mua hàng online, bạn không thể tự hủy. Vui lòng gọi Hotline: 0901.234.567 để được hỗ trợ gấp!");
            } else {
                $this->setFlashError("Không thể hủy đơn hàng này do đơn đã được giao hoặc đã hoàn tất.");
            }
        }

        if ($is_admin) {
            $this->redirect('index.php?act=quanlydonhang');
        } else {
            $this->redirect('index.php?act=lichsudonhang');
        }
    }

    public function yeuCauTraHang()
    {
        if (!$this->isPost() || !isset($_SESSION['id'])) $this->redirect('index.php?act=dangnhap');
        $id = (int)$this->post('order_id', 0);
        $reason = trim($this->post('return_reason', ''));
        if (mb_strlen($reason) < 5 || mb_strlen($reason) > 1000) {
            $this->setFlashError('Lý do trả hàng phải từ 5 đến 1000 ký tự.');
        } else {
            try {
                $this->donHangModel->requestReturn($id, $_SESSION['id'], $reason);
                $this->setFlashSuccess('Đã gửi yêu cầu trả hàng. Vui lòng chờ Admin duyệt.');
            } catch (Throwable $e) { $this->setFlashError($e->getMessage()); }
        }
        $this->redirect('index.php?act=lichsudonhang');
    }

    public function duyetTraHang()
    {
        if (!$this->isPost() || !in_array($_SESSION['role'] ?? '', ['admin','staff'], true)) $this->redirect('index.php?act=trangchu');
        $id = (int)$this->post('order_id', 0);
        $action = $this->post('return_action', '');
        $next = $action === 'approve' ? 'da_tra_hang' : ($action === 'reject' ? 'tu_choi_tra' : '');
        if ($next === '') $this->redirect('index.php?act=quanlydonhang');
        try {
            $this->donHangModel->changeStatusSafely($id, 'yeu_cau_tra', $next, $next === 'da_tra_hang' ? 'Admin duyệt trả hàng, hoàn tồn kho và voucher.' : 'Admin từ chối yêu cầu trả hàng.');
            $this->setFlashSuccess('Đã xử lý yêu cầu trả hàng #' . $id);
        } catch (Throwable $e) { $this->setFlashError($e->getMessage()); }
        $this->redirect('index.php?act=quanlydonhang');
    }

    /**
     * Màn hình tra cứu đơn hàng
     */
    public function traCuuDonHang()
    {
        $this->renderView('tracuudonhang.php');
    }

    /**
     * Xử lý tra cứu đơn hàng
     */
    public function xuLyTraCuu()
    {
        if ($this->isPost() && isset($_POST['tracuu'])) {
            $order_id = (int)$this->post('order_id', 0);
            $phone = trim($this->post('phone', ''));

            $order = $this->donHangModel->lookupOrder($order_id, $phone);

            if ($order) {
                $this->redirect("index.php?act=theodoigiaohang&id=$order_id");
            } else {
                $this->loi = "Không tìm thấy đơn hàng #$order_id với số điện thoại này.";
                $this->renderView('tracuudonhang.php');
            }
        }
        $this->redirect('index.php?act=tracuudonhang');
    }

    /**
     * Xem / In hóa đơn bán hàng trực tiếp
     */
    public function inHoaDon()
    {
        $id = (int)$this->get('id', 0);
        $order = $this->donHangModel->getOrderById($id);

        if ($order) {
            $is_admin = isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'staff']);
            if (!$is_admin && !empty($order['user_id'])) {
                if (!isset($_SESSION['id']) || $_SESSION['id'] != $order['user_id']) {
                    $this->redirect('index.php?act=trangchu');
                }
            }
            $order_items = $this->donHangModel->getOrderItems($id);
            include __DIR__ . "/../view/hoadon.php";
            exit;
        }
        $this->redirect('index.php?act=trangchu');
    }

    /**
     * Lịch sử mua hàng của khách hàng đăng nhập
     */
    public function lichSuDonHang()
    {
        if (!isset($_SESSION['id'])) {
            $this->redirect('index.php?act=dangnhap');
        }
        $danhsach_donhang = $this->donHangModel->getOrdersByUser($_SESSION['id']);
        $this->renderView('lichsudonhang.php', [
            'danhsach_donhang' => $danhsach_donhang
        ]);
    }

    /**
     * Chi tiết một đơn hàng cụ thể
     */
    public function chiTietDonHang()
    {
        $id = (int)$this->get('id', 0);
        $order = $this->donHangModel->getOrderById($id);

        if (!$order) {
            $this->redirect('index.php?act=trangchu');
        }

        $is_admin = isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'staff']);
        if (!$is_admin && !empty($order['user_id'])) {
            if (!isset($_SESSION['id']) || $_SESSION['id'] != $order['user_id']) {
                $this->setFlashError("Bạn không có quyền xem thông tin đơn hàng này.");
                $this->redirect('index.php?act=lichsudonhang');
            }
        }

        $order_items = $this->donHangModel->getOrderItems($id);
        $this->renderView('chitietdonhang.php', [
            'order' => $order,
            'order_items' => $order_items
        ]);
    }

    /**
     * Theo dõi tiến trình giao hàng (Tracking Timeline)
     */
    public function theoDoiGiaoHang()
    {
        $id = (int)$this->get('id', 0);
        $order = $this->donHangModel->getOrderById($id);

        if (!$order) {
            $this->redirect('index.php?act=trangchu');
        }

        $tracking_logs = $this->donHangModel->getTrackingLogs($id);
        $this->renderView('theodoigiaohang.php', [
            'order' => $order,
            'tracking_logs' => $tracking_logs
        ]);
    }
}
