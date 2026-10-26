<?php
// controller/BaseController.php - Lớp Controller cơ sở trong kiến trúc OOP MVC
class BaseController
{
    protected $loi = '';
    protected $thanhcong = '';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (isset($_SESSION['flash_error'])) {
            $this->loi = $_SESSION['flash_error'];
            unset($_SESSION['flash_error']);
        }

        if (isset($_SESSION['flash_success'])) {
            $this->thanhcong = $_SESSION['flash_success'];
            unset($_SESSION['flash_success']);
        } elseif (isset($_GET['msg'])) {
            $msgMap = [
                'danhgia_thanhcong' => 'Cảm ơn bạn đã gửi đánh giá! Nhận xét của bạn đã được ghi nhận thành công.',
                'dangky_thanhcong'  => 'Đăng ký tài khoản thành công! Vui lòng đăng nhập để tiếp tục mua sắm.'
            ];
            $this->thanhcong = $msgMap[$_GET['msg']] ?? $_GET['msg'];
        }
    }

    /**
     * Hiển thị giao diện View kèm nạp Header, Footer và Flash messages
     */
    public function renderView($view_file, $data_truyen = [])
    {
        global $act;
        $loi = $this->loi;
        $thanhcong = $this->thanhcong;

        // Tự động nhận diện đường dẫn thư mục tài nguyên view
        $asset_dir = file_exists('view/css/style.css') ? 'view' : (file_exists('../view/css/style.css') ? '../view' : 'view');

        extract($data_truyen);

        include __DIR__ . "/../view/header.php";
        if (!empty($loi)) {
            echo '<div class="container mt-3"><div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i>' . htmlspecialchars($loi) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>';
        }
        if (!empty($thanhcong)) {
            echo '<div class="container mt-3"><div class="alert alert-success alert-dismissible fade show" role="alert"><i class="bi bi-check-circle-fill me-2"></i>' . htmlspecialchars($thanhcong) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>';
        }
        // Đóng khung nội dung chính
        echo '<main class="site-main">';

        include __DIR__ . "/../view/" . $view_file;

        echo '</main>';

        // Hiển thị footer
        include __DIR__ . "/../view/footer.php";
        exit;
    }

    /**
     * Chuyển hướng trang (Redirect)
     */
    public function redirect($url)
    {
        header("Location: " . $url);
        exit;
    }

    /**
     * Lưu thông báo lỗi tạm thời vào Session
     */
    public function setFlashError($msg)
    {
        $_SESSION['flash_error'] = $msg;
    }

    /**
     * Lưu thông báo thành công tạm thời vào Session
     */
    public function setFlashSuccess($msg)
    {
        $_SESSION['flash_success'] = $msg;
    }

    /**
     * Kiểm tra phương thức gửi dữ liệu là POST
     */
    public function isPost()
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    /**
     * Lấy giá trị an toàn từ $_POST
     */
    public function post($key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Lấy giá trị an toàn từ $_GET
     */
    public function get($key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }
}
