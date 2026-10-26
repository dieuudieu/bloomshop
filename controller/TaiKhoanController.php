<?php
// controller/TaiKhoanController.php - Controller điều hướng Đăng ký, Đăng nhập, Đăng xuất theo OOP
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/TaiKhoanModel.php';

class TaiKhoanController extends BaseController
{
    private $taiKhoanModel;

    public function __construct()
    {
        parent::__construct();
        $this->taiKhoanModel = new TaiKhoanModel();
    }

    /**
     * Xử lý Đăng ký tài khoản mới
     */
    public function dangKy()
    {
        if ($this->isPost() && isset($_POST['dangky'])) {
            $name = trim($this->post('name', ''));
            $email = trim($this->post('email', ''));
            $password = $this->post('password', '');

            $existing = $this->taiKhoanModel->findByEmail($email);

            if ($existing) {
                $this->loi = 'Email này đã được đăng ký trên hệ thống.';
            } else {
                $this->taiKhoanModel->register($name, $email, $password);
                $this->redirect("index.php?act=dangnhap&msg=dangky_thanhcong");
            }
        }

        $this->renderView('dangky.php');
    }

    /**
     * Xử lý Đăng nhập hệ thống
     */
    public function dangNhap()
    {
        if ($this->isPost() && isset($_POST['dangnhap'])) {
            $email = trim($this->post('email', ''));
            $password = $this->post('password', '');

            $user = $this->taiKhoanModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                if (isset($user['status']) && (int)$user['status'] === 0) {
                    $this->loi = (($user['role'] ?? '') === 'staff')
                        ? 'Tài khoản nhân viên của bạn đã bị Quản trị viên khóa! Bạn đã mất toàn bộ quyền truy cập hệ thống.'
                        : 'Tài khoản của bạn đã bị khóa bởi Quản trị viên. Vui lòng liên hệ hotline để được hỗ trợ.';
                } else {
                    session_regenerate_id(true);
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['tier'] = $user['tier'] ?? 'silver';
                    $this->redirect('index.php?act=trangchu');
                }
            } else {
                $this->loi = 'Email hoặc mật khẩu không chính xác.';
            }
        }

        $this->renderView('dangnhap.php');
    }

    /**
     * Đăng xuất khỏi hệ thống
     */
    public function dangXuat()
    {
        session_unset();
        session_destroy();
        $this->redirect('index.php?act=trangchu');
    }
}
