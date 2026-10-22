<?php
// controller/c_donhang.php - Xử lý Lịch sử đơn hàng, Tiến trình giao hàng, Tra cứu & Hủy đơn

// 1. Hủy đơn hàng (User chỉ được hủy khi đơn đang ở trạng thái 'cho_xac_nhan')
if ($act == 'huydonhang' && isset($_POST['huydonhang'])) {
    $order_id = (int)$_POST['order_id'];
    $order = $data->readsingle("SELECT * FROM donhang WHERE id = :id", [':id' => $order_id]);

    $is_admin = isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'staff']);

    // Bảo mật: Khách hàng chỉ được phép hủy đơn hàng của chính mình
    if (!$is_admin && (!isset($_SESSION['id']) || empty($order['user_id']) || $order['user_id'] != $_SESSION['id'])) {
        $_SESSION['flash_error'] = "Bạn không có quyền thao tác trên đơn hàng này.";
        header("Location: index.php?act=lichsudonhang");
        exit;
    }

    if ($order && $order['status'] == 'cho_xac_nhan') {
        $data->execute_item("UPDATE donhang SET status = 'da_huy' WHERE id = :id", [':id' => $order_id]);
        
        $actor = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? 'Quản trị viên' : 'Khách hàng';
        $data->execute_item("INSERT INTO order_tracking (order_id, status, description, created_at)
                             VALUES (:id, 'da_huy', '$actor đã hủy đơn hàng.', NOW())", [':id' => $order_id]);

        // Hoàn lại lượt dùng voucher nếu có
        if (!empty($order['voucher_code'])) {
            $data->execute_item("UPDATE vouchers SET used_count = GREATEST(0, used_count - 1) WHERE code = :code", [':code' => $order['voucher_code']]);
        }

        $_SESSION['flash_success'] = "Đã hủy đơn hàng #$order_id thành công.";
    } else {
        $st_hientai = $order ? $order['status'] : '';
        if (in_array($st_hientai, ['xac_nhan', 'dang_giao'])) {
            $_SESSION['flash_error'] = "Đơn hàng #$order_id đã được xác nhận / đang cắm hoa và giao hàng. Theo quy định mua hàng online, bạn không thể tự hủy. Vui lòng gọi Hotline: 0901.234.567 để được hỗ trợ gấp!";
        } else {
            $_SESSION['flash_error'] = "Không thể hủy đơn hàng này do đơn đã được giao hoặc đã hoàn tất.";
        }
    }
    
    if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'staff'])) {
        header("Location: index.php?act=quanlydonhang");
    } else {
        header("Location: index.php?act=lichsudonhang");
    }
    exit;
}

// 2. Tra cứu đơn hàng nhanh (Khách vãng lai / Người nhận)
if ($act == 'xulytracuu' && isset($_POST['tracuu'])) {
    $order_id = (int)$_POST['order_id'];
    $phone = trim($_POST['phone']);

    $order = $data->readsingle("SELECT * FROM donhang WHERE id = :id AND customer_phone = :phone", [
        ':id' => $order_id,
        ':phone' => $phone
    ]);

    if ($order) {
        header("Location: index.php?act=theodoigiaohang&id=$order_id");
        exit;
    } else {
        $loi = "Không tìm thấy đơn hàng #$order_id với số điện thoại này.";
        hien_thi_view('tracuudonhang.php');
        exit;
    }
}

// 3. Xem / In hóa đơn bán hàng trực tiếp (Render qua View view/hoadon.php chuẩn MVC)
if ($act == 'inhoadon') {
    $id = (int)($_GET['id'] ?? 0);
    $order = $data->readsingle("SELECT * FROM donhang WHERE id = :id", [':id' => $id]);
    if ($order) {
        $is_admin = isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'staff']);
        if (!$is_admin && !empty($order['user_id'])) {
            if (!isset($_SESSION['id']) || $_SESSION['id'] != $order['user_id']) {
                header("Location: index.php?act=trangchu");
                exit;
            }
        }
        $order_items = $data->readitem("SELECT * FROM chitiet_donhang WHERE order_id = :id", [':id' => $id]);
        include "../view/hoadon.php";
        exit;
    }
    header("Location: index.php?act=trangchu");
    exit;
}

// 4. Lịch sử mua hàng của khách hàng đăng nhập
if ($act == 'lichsudonhang') {
    if (!isset($_SESSION['id'])) {
        header("Location: index.php?act=dangnhap");
        exit;
    }
    $danhsach_donhang = $data->readitem("SELECT * FROM donhang WHERE user_id = :uid ORDER BY id DESC", [':uid' => $_SESSION['id']]);
    hien_thi_view('lichsudonhang.php', ['danhsach_donhang' => $danhsach_donhang]);
    exit;
}

// 5. Chi tiết một đơn hàng cụ thể
if ($act == 'chitietdonhang') {
    $id = (int)($_GET['id'] ?? 0);
    $order = $data->readsingle("SELECT * FROM donhang WHERE id = :id", [':id' => $id]);
    if (!$order) {
        header("Location: index.php?act=trangchu");
        exit;
    }

    $is_admin = isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'staff']);
    if (!$is_admin && !empty($order['user_id'])) {
        if (!isset($_SESSION['id']) || $_SESSION['id'] != $order['user_id']) {
            $_SESSION['flash_error'] = "Bạn không có quyền xem thông tin đơn hàng này.";
            header("Location: index.php?act=lichsudonhang");
            exit;
        }
    }

    $order_items = $data->readitem("SELECT * FROM chitiet_donhang WHERE order_id = :id", [':id' => $id]);
    hien_thi_view('chitietdonhang.php', [
        'order' => $order,
        'order_items' => $order_items
    ]);
    exit;
}

// 6. Theo dõi tiến trình giao hàng (Tracking Timeline)
if ($act == 'theodoigiaohang') {
    $id = (int)($_GET['id'] ?? 0);
    $order = $data->readsingle("SELECT * FROM donhang WHERE id = :id", [':id' => $id]);
    if (!$order) {
        header("Location: index.php?act=trangchu");
        exit;
    }
    $tracking_logs = $data->readitem("SELECT * FROM order_tracking WHERE order_id = :id ORDER BY created_at ASC", [':id' => $id]);
    hien_thi_view('theodoigiaohang.php', [
        'order' => $order,
        'tracking_logs' => $tracking_logs
    ]);
    exit;
}

// 7. Màn hình tra cứu đơn hàng
if ($act == 'tracuudonhang') {
    hien_thi_view('tracuudonhang.php');
    exit;
}
