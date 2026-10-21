<?php

// Nếu chưa có giỏ hàng thì tạo giỏ hàng rỗng
if (!isset($_SESSION['giohang'])) {
    $_SESSION['giohang'] = [];
}

// Chỉ xử lý thay đổi giỏ hàng khi gửi form POST
if ($_SERVER['REQUEST_METHOD'] == 'POST'
    && isset($_POST['thao_tac'])) {

    $thao_tac = $_POST['thao_tac'];
    $id = (int) ($_POST['id'] ?? 0);

    if ($id > 0) {

        // Thêm sản phẩm vào giỏ
        if ($thao_tac == 'them') {

            $sp = $data->readitem(
                "SELECT id FROM sanpham WHERE id = :id",
                [':id' => $id]
            );

            if (count($sp) > 0) {
                if (isset($_SESSION['giohang'][$id])) {
                    $_SESSION['giohang'][$id]++;
                } else {
                    $_SESSION['giohang'][$id] = 1;
                }
            }
        }

        // Tăng số lượng
        if ($thao_tac == 'tang') {
            if (isset($_SESSION['giohang'][$id])) {
                $_SESSION['giohang'][$id]++;
            }
        }

        // Giảm số lượng
        if ($thao_tac == 'giam') {
            if (isset($_SESSION['giohang'][$id])) {
                $_SESSION['giohang'][$id]--;

                if ($_SESSION['giohang'][$id] <= 0) {
                    unset($_SESSION['giohang'][$id]);
                }
            }
        }

        // Xóa sản phẩm khỏi giỏ
        if ($thao_tac == 'xoa') {
            unset($_SESSION['giohang'][$id]);
        }
    }

    // Nếu thêm sản phẩm thì quay lại trang đang xem
    if ($thao_tac == 'them') {

        $trang_truoc = $_POST['trang_truoc'] ?? '';

        // Chỉ cho quay lại đường dẫn thuộc Controller BloomShop
        if (strpos($trang_truoc, '/bloomshop/controller/index.php') === 0) {
            header("Location: " . $trang_truoc);
        } else {
            header("Location: index.php?act=sanpham");
        }

    } else {

        // Tăng, giảm, xóa thì vẫn ở trang giỏ hàng
        header("Location: index.php?act=giohang");

    }

    exit;
    
}