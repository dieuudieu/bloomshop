<?php
// controller/c_giohang.php - Xử lý Giỏ hàng, Áp dụng Voucher, Thanh toán & Gửi hóa đơn Mail

// 1. Thêm sản phẩm vào giỏ
if ($act == 'themgiohang') {
    $id = (int)$_POST['id'];
    $qty = max(1, (int)($_POST['qty'] ?? 1));

    $sp = $data->readsingle("SELECT * FROM sanpham WHERE id = :id", [':id' => $id]);
    if ($sp) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $price = (!empty($sp['is_sale']) && !empty($sp['sale_price'])) ? $sp['sale_price'] : $sp['price'];

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $sp['id'],
                'name' => $sp['name'],
                'price' => $price,
                'image' => $sp['image'],
                'qty' => $qty
            ];
        }

        if (!empty($_POST['muangay'])) {
            header("Location: index.php?act=thanhtoan");
            exit;
        }
    }
    header("Location: index.php?act=giohang");
    exit;
}

// 2. Cập nhật số lượng giỏ hàng (+ / -)
if ($act == 'capnhatgiohang') {
    $id = (int)$_POST['id'];
    $action = $_POST['action'] ?? '';

    if (isset($_SESSION['cart'][$id])) {
        if ($action == 'increase') {
            $_SESSION['cart'][$id]['qty']++;
        } elseif ($action == 'decrease') {
            $_SESSION['cart'][$id]['qty']--;
            if ($_SESSION['cart'][$id]['qty'] <= 0) {
                unset($_SESSION['cart'][$id]);
            }
        }
    }
    header("Location: index.php?act=giohang");
    exit;
}

// 3. Xóa một sản phẩm khỏi giỏ
if ($act == 'xoagiohang') {
    $id = (int)$_GET['id'];
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: index.php?act=giohang");
    exit;
}

// 4. Xóa toàn bộ giỏ hàng
if ($act == 'xoatatcagiohang') {
    unset($_SESSION['cart']);
    unset($_SESSION['voucher']);
    header("Location: index.php?act=giohang");
    exit;
}

// 5. Áp dụng mã Voucher giảm giá
if ($act == 'apvoucher') {
    $code = strtoupper(trim($_POST['voucher_code'] ?? ''));
    $subtotal = 0;
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $it) {
            $subtotal += $it['price'] * $it['qty'];
        }
    }

    $sql = "SELECT * FROM vouchers WHERE code = :code AND status = 1";
    $vc = $data->readsingle($sql, [':code' => $code]);

    if (!$vc) {
        $loi = "Mã voucher '$code' không tồn tại hoặc đã bị khóa.";
    } elseif (strtotime($vc['expiry_date']) < strtotime(date('Y-m-d'))) {
        $loi = "Mã voucher '$code' đã hết hạn sử dụng.";
    } elseif ($vc['used_count'] >= $vc['quantity']) {
        $loi = "Mã voucher '$code' đã hết lượt sử dụng.";
    } elseif ($subtotal < $vc['min_order_value']) {
        $loi = "Đơn hàng tối thiểu để áp dụng mã này là " . number_format($vc['min_order_value'], 0, ',', '.') . "đ.";
    } else {
        $_SESSION['voucher'] = [
            'id' => $vc['id'],
            'code' => $vc['code'],
            'discount_type' => $vc['discount_type'],
            'discount_value' => (float)$vc['discount_value'],
            'min_order_value' => (float)$vc['min_order_value']
        ];
        $thanhcong = "Áp dụng mã giảm giá '$code' thành công!";
    }
    
    $ds_voucher = $data->readitem("SELECT * FROM vouchers WHERE status = 1 AND expiry_date >= CURDATE() AND used_count < quantity");
    hien_thi_view('giohang.php', ['ds_voucher' => $ds_voucher]);
    exit;
}

// 6. Bỏ chọn voucher
if ($act == 'xoavoucher') {
    unset($_SESSION['voucher']);
    header("Location: index.php?act=giohang");
    exit;
}

// 7. Xác nhận đặt hàng & Gửi email hóa đơn cho Admin
if ($act == 'xacnhandathang' && isset($_POST['dathang'])) {
    if (empty($_SESSION['cart'])) {
        header("Location: index.php?act=giohang");
        exit;
    }

    $customer_name = trim($_POST['customer_name']);
    $customer_phone = trim($_POST['customer_phone']);
    $customer_email = trim($_POST['customer_email']);
    $customer_address = trim($_POST['customer_address']);
    $note = trim($_POST['note'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'COD';
    $user_id = $_SESSION['id'] ?? null;

    $subtotal = 0;
    foreach ($_SESSION['cart'] as $it) {
        $subtotal += $it['price'] * $it['qty'];
    }

    $discount_amount = 0;
    $voucher_code = null;
    $voucher_id = null;
    if (isset($_SESSION['voucher'])) {
        $vc = $_SESSION['voucher'];
        $voucher_code = $vc['code'];
        $voucher_id = $vc['id'] ?? null;
        if ($vc['discount_type'] == 'percent') {
            $discount_amount = ($subtotal * $vc['discount_value']) / 100;
        } else {
            $discount_amount = $vc['discount_value'];
        }
        if ($discount_amount > $subtotal) $discount_amount = $subtotal;
    }

    $total_price = max(0, $subtotal - $discount_amount);

    // Lưu vào bảng đơn hàng
    $sql_order = "INSERT INTO donhang (user_id, customer_name, customer_email, customer_phone, customer_address, note, subtotal, voucher_code, discount_amount, total_price, payment_method, status, created_at)
                  VALUES (:user_id, :name, :email, :phone, :address, :note, :subtotal, :voucher, :discount, :total, :payment, 'cho_xac_nhan', NOW())";
    
    $order_id = $data->execute_get_id($sql_order, [
        ':user_id' => $user_id,
        ':name' => $customer_name,
        ':email' => $customer_email,
        ':phone' => $customer_phone,
        ':address' => $customer_address,
        ':note' => $note,
        ':subtotal' => $subtotal,
        ':voucher' => $voucher_code,
        ':discount' => $discount_amount,
        ':total' => $total_price,
        ':payment' => $payment_method
    ]);

    // Lưu chi tiết đơn hàng
    $order_items = [];
    foreach ($_SESSION['cart'] as $it) {
        $item_total = $it['price'] * $it['qty'];
        $sql_item = "INSERT INTO chitiet_donhang (order_id, product_id, product_name, product_image, price, quantity, total)
                     VALUES (:order_id, :product_id, :product_name, :product_image, :price, :quantity, :total)";
        $data->execute_item($sql_item, [
            ':order_id' => $order_id,
            ':product_id' => $it['id'],
            ':product_name' => $it['name'],
            ':product_image' => $it['image'],
            ':price' => $it['price'],
            ':quantity' => $it['qty'],
            ':total' => $item_total
        ]);

        $order_items[] = [
            'product_name' => $it['name'],
            'quantity' => $it['qty'],
            'price' => $it['price'],
            'total' => $item_total
        ];
    }

    // Tăng số lượng đã dùng của voucher
    if ($voucher_id) {
        $data->execute_item("UPDATE vouchers SET used_count = used_count + 1 WHERE id = :id", [':id' => $voucher_id]);
    }

    // Ghi nhận mốc tracking ban đầu
    $sql_track = "INSERT INTO order_tracking (order_id, status, description, created_at)
                  VALUES (:order_id, 'cho_xac_nhan', 'Đơn hàng mới được tạo thành công, đang chờ BloomShop xác nhận.', NOW())";
    $data->execute_item($sql_track, [':order_id' => $order_id]);

    // Gửi email hóa đơn tự động cho Quản trị viên và Khách hàng
    $order_info = $data->readsingle("SELECT * FROM donhang WHERE id = :id", [':id' => $order_id]);
    Mailer::send_order_invoice($order_info, $order_items);

    // Xóa giỏ hàng
    unset($_SESSION['cart']);
    unset($_SESSION['voucher']);

    header("Location: index.php?act=dathangthanhcong&id=$order_id");
    exit;
}

// 8. Hiển thị Trang giỏ hàng
if ($act == 'giohang') {
    $ds_voucher = $data->readitem("SELECT * FROM vouchers WHERE status = 1 AND expiry_date >= CURDATE() AND used_count < quantity");
    hien_thi_view('giohang.php', ['ds_voucher' => $ds_voucher]);
    exit;
}

// 9. Hiển thị Trang thanh toán
if ($act == 'thanhtoan') {
    if (empty($_SESSION['cart'])) {
        header("Location: index.php?act=giohang");
        exit;
    }
    $current_user = null;
    if (isset($_SESSION['id'])) {
        $current_user = $data->readsingle("SELECT * FROM users WHERE id = :id", [':id' => $_SESSION['id']]);
    }
    hien_thi_view('thanhtoan.php', ['current_user' => $current_user]);
    exit;
}

// 10. Hiển thị Trang đặt hàng thành công
if ($act == 'dathangthanhcong') {
    $id = (int)($_GET['id'] ?? 0);
    $order = $data->readsingle("SELECT * FROM donhang WHERE id = :id", [':id' => $id]);
    if (!$order) {
        header("Location: index.php?act=trangchu");
        exit;
    }
    hien_thi_view('dathangthanhcong.php', ['order' => $order]);
    exit;
}
