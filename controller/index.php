<?php
session_start();
require_once "../model/xl_database.php";
require_once "../model/mailer.php";

$data = new xl_data();
$act = $_REQUEST['act'] ?? 'trangchu';
$loi = '';
$thanhcong = $_GET['msg'] ?? '';

// Quyền truy cập
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
if (in_array($act, $staff_allowed)) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
        header("Location: index.php?act=dangnhap");
        exit;
    }
}

// Kiểm tra quyền chỉ dành cho Admin
if (in_array($act, $admin_only)) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: index.php?act=dangnhap");
        exit;
    }
}

// ==============================================================
// 1. TÀI KHOẢN (ĐĂNG KÝ, ĐĂNG NHẬP, ĐĂNG XUẤT)
// ==============================================================
if ($act == 'dangky' && isset($_POST['dangky'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "SELECT * FROM users WHERE email = :email";
    $ds = $data->readitem($sql, [':email' => $email]);

    if (count($ds) > 0) {
        $loi = 'Email này đã được đăng ký trên hệ thống.';
    } else {
        $sql = "INSERT INTO users (name, email, password, role, status, tier, total_spent)
                VALUES (:name, :email, :password, 'user', 1, 'silver', 0)";
        $data->execute_item($sql, [
            ':name' => $name, ':email' => $email, ':password' => $password
        ]);
        header("Location: index.php?act=dangnhap&msg=dangky_thanhcong");
        exit;
    }
}

if ($act == 'dangnhap' && isset($_POST['dangnhap'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE email = :email";
    $ds = $data->readitem($sql, [':email' => $email]);

    if (count($ds) > 0 && password_verify($password, $ds[0]['password'])) {
        $user = $ds[0];
        if (isset($user['status']) && $user['status'] == 0) {
            $loi = 'Tài khoản của bạn đã bị khóa bởi Quản trị viên. Vui lòng liên hệ hotline để được hỗ trợ.';
        } else {
            session_regenerate_id(true);
            $_SESSION['id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['tier'] = $user['tier'] ?? 'silver';
            header("Location: index.php?act=trangchu");
            exit;
        }
    } else {
        $loi = 'Email hoặc mật khẩu không chính xác.';
    }
}

if ($act == 'dangxuat') {
    session_unset();
    session_destroy();
    header("Location: index.php?act=trangchu");
    exit;
}

// ==============================================================
// 2. GIỎ HÀNG, ÁP VOUCHER & ĐẶT HÀNG
// ==============================================================
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

if ($act == 'xoagiohang') {
    $id = (int)$_GET['id'];
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: index.php?act=giohang");
    exit;
}

if ($act == 'xoatatcagiohang') {
    unset($_SESSION['cart']);
    unset($_SESSION['voucher']);
    header("Location: index.php?act=giohang");
    exit;
}

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
    $act = 'giohang';
}

if ($act == 'xoavoucher') {
    unset($_SESSION['voucher']);
    header("Location: index.php?act=giohang");
    exit;
}

// XÁC NHẬN ĐẶT HÀNG & GỬI HÓA ĐƠN MAIL
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

    // Lưu đơn hàng
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

    // Ghi nhận mốc tracking đầu tiên
    $sql_track = "INSERT INTO order_tracking (order_id, status, description, created_at)
                  VALUES (:order_id, 'cho_xac_nhan', 'Đơn hàng mới được tạo thành công, đang chờ BloomShop xác nhận.', NOW())";
    $data->execute_item($sql_track, [':order_id' => $order_id]);

    // Lấy thông tin đầy đủ đơn hàng để xuất và gửi email hóa đơn
    $order_info = $data->readsingle("SELECT * FROM donhang WHERE id = :id", [':id' => $order_id]);
    Mailer::send_order_invoice($order_info, $order_items);

    // Xóa giỏ hàng
    unset($_SESSION['cart']);
    unset($_SESSION['voucher']);

    header("Location: index.php?act=dathangthanhcong&id=$order_id");
    exit;
}

// ==============================================================
// 3. ĐÁNH GIÁ & BÌNH LUẬN SẢN PHẨM
// ==============================================================
if ($act == 'guidanhgia' && isset($_POST['guidanhgia'])) {
    $product_id = (int)$_POST['product_id'];
    $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
    $user_name = trim($_POST['user_name'] ?? 'Khách hàng');
    $content = trim($_POST['content'] ?? '');
    $user_id = $_SESSION['id'] ?? null;

    if (!empty($content)) {
        $sql = "INSERT INTO danhgia (product_id, user_id, user_name, rating, content, status, created_at)
                VALUES (:pid, :uid, :uname, :rating, :content, 1, NOW())";
        $data->execute_item($sql, [
            ':pid' => $product_id,
            ':uid' => $user_id,
            ':uname' => $user_name,
            ':rating' => $rating,
            ':content' => $content
        ]);
        $thanhcong = 'Đánh giá của bạn đã được gửi thành công!';
    }
    header("Location: index.php?act=chitiettsp&id=$product_id&msg=danhgia_thanhcong");
    exit;
}

// ==============================================================
// 4. HỦY MUA HÀNG (USER & ADMIN)
// ==============================================================
if ($act == 'huydonhang' && isset($_POST['huydonhang'])) {
    $order_id = (int)$_POST['order_id'];
    $order = $data->readsingle("SELECT * FROM donhang WHERE id = :id", [':id' => $order_id]);

    if ($order && $order['status'] == 'cho_xac_nhan') {
        $data->execute_item("UPDATE donhang SET status = 'da_huy' WHERE id = :id", [':id' => $order_id]);
        
        $actor = isset($_SESSION['role']) && $_SESSION['role'] == 'admin' ? 'Quản trị viên' : 'Khách hàng';
        $data->execute_item("INSERT INTO order_tracking (order_id, status, description, created_at)
                             VALUES (:id, 'da_huy', '$actor đã hủy đơn hàng.', NOW())", [':id' => $order_id]);

        // Hoàn lại voucher nếu có
        if (!empty($order['voucher_code'])) {
            $data->execute_item("UPDATE vouchers SET used_count = GREATEST(0, used_count - 1) WHERE code = :code", [':code' => $order['voucher_code']]);
        }

        $thanhcong = "Đã hủy đơn hàng #$order_id thành công.";
    } else {
        $loi = "Không thể hủy đơn hàng này do đơn đã được xử lý hoặc đã giao.";
    }
    
    if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'staff'])) {
        header("Location: index.php?act=quanlydonhang");
    } else {
        header("Location: index.php?act=lichsudonhang");
    }
    exit;
}

// ==============================================================
// 5. TRA CỨU ĐƠN HÀNG (GUEST/USER)
// ==============================================================
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
        $act = 'tracuudonhang';
    }
}

// ==============================================================
// 6. QUẢN LÝ NHÂN VIÊN (ADMIN ONLY)
// ==============================================================
if ($act == 'themnv' && isset($_POST['themnv'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $data->readitem("SELECT * FROM users WHERE email = :email", [':email' => $email]);
    if (count($check) > 0) {
        $loi = 'Email nhân viên này đã tồn tại trong hệ thống.';
    } else {
        $sql = "INSERT INTO users (name, email, password, phone, role, status)
                VALUES (:name, :email, :password, :phone, 'staff', 1)";
        $data->execute_item($sql, [
            ':name' => $name, ':email' => $email, ':password' => $password, ':phone' => $phone
        ]);
        header("Location: index.php?act=quanlynv");
        exit;
    }
}

if ($act == 'capnhatnv' && isset($_POST['capnhatnv'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $status = (int)$_POST['status'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name = :name, email = :email, phone = :phone, password = :pwd, status = :status WHERE id = :id AND role = 'staff'";
        $data->execute_item($sql, [
            ':name' => $name, ':email' => $email, ':phone' => $phone, ':pwd' => $password, ':status' => $status, ':id' => $id
        ]);
    } else {
        $sql = "UPDATE users SET name = :name, email = :email, phone = :phone, status = :status WHERE id = :id AND role = 'staff'";
        $data->execute_item($sql, [
            ':name' => $name, ':email' => $email, ':phone' => $phone, ':status' => $status, ':id' => $id
        ]);
    }
    header("Location: index.php?act=quanlynv");
    exit;
}

if ($act == 'xoanv' && isset($_POST['xoanv'])) {
    $id = (int)$_POST['id'];
    $data->execute_item("DELETE FROM users WHERE id = :id AND role = 'staff'", [':id' => $id]);
    header("Location: index.php?act=quanlynv");
    exit;
}

// ==============================================================
// 7. QUẢN LÝ ĐÁNH GIÁ (ADMIN / STAFF)
// ==============================================================
if ($act == 'doitrangthaidanhgia') {
    $id = (int)$_POST['id'];
    $status = (int)$_POST['status'];
    $data->execute_item("UPDATE danhgia SET status = :st WHERE id = :id", [':st' => $status, ':id' => $id]);
    header("Location: index.php?act=quanlydanhgia");
    exit;
}

if ($act == 'xoadanhgia' && isset($_POST['xoadanhgia'])) {
    $id = (int)$_POST['id'];
    $data->execute_item("DELETE FROM danhgia WHERE id = :id", [':id' => $id]);
    header("Location: index.php?act=quanlydanhgia");
    exit;
}

// ==============================================================
// 8. QUẢN LÝ KHÁCH HÀNG (XEM, KHÓA)
// ==============================================================
if ($act == 'khoakh') {
    $id = (int)$_POST['id'];
    $status = (int)$_POST['status'];
    $data->execute_item("UPDATE users SET status = :st WHERE id = :id AND role = 'user'", [':st' => $status, ':id' => $id]);
    header("Location: index.php?act=quanlykh");
    exit;
}

// ==============================================================
// 9. QUẢN LÝ VOUCHER (ADMIN / STAFF)
// ==============================================================
if ($act == 'themvoucher' && isset($_POST['them'])) {
    $code = strtoupper(trim($_POST['code']));
    $discount_type = $_POST['discount_type'];
    $discount_value = (float)$_POST['discount_value'];
    $min_order_value = (float)($_POST['min_order_value'] ?? 0);
    $quantity = (int)$_POST['quantity'];
    $expiry_date = $_POST['expiry_date'];

    $check = $data->readitem("SELECT * FROM vouchers WHERE code = :code", [':code' => $code]);
    if (count($check) > 0) {
        $loi = "Mã voucher '$code' đã tồn tại!";
    } else {
        $sql = "INSERT INTO vouchers (code, discount_type, discount_value, min_order_value, quantity, used_count, expiry_date, status)
                VALUES (:code, :dtype, :dval, :minval, :qty, 0, :exp, 1)";
        $data->execute_item($sql, [
            ':code' => $code, ':dtype' => $discount_type, ':dval' => $discount_value,
            ':minval' => $min_order_value, ':qty' => $quantity, ':exp' => $expiry_date
        ]);
        header("Location: index.php?act=quanlyvoucher");
        exit;
    }
}

if ($act == 'capnhatvoucher' && isset($_POST['capnhat'])) {
    $id = (int)$_POST['id'];
    $code = strtoupper(trim($_POST['code']));
    $discount_type = $_POST['discount_type'];
    $discount_value = (float)$_POST['discount_value'];
    $min_order_value = (float)($_POST['min_order_value'] ?? 0);
    $quantity = (int)$_POST['quantity'];
    $expiry_date = $_POST['expiry_date'];
    $status = (int)$_POST['status'];

    $sql = "UPDATE vouchers SET code = :code, discount_type = :dtype, discount_value = :dval, min_order_value = :minval,
                   quantity = :qty, expiry_date = :exp, status = :status WHERE id = :id";
    $data->execute_item($sql, [
        ':code' => $code, ':dtype' => $discount_type, ':dval' => $discount_value,
        ':minval' => $min_order_value, ':qty' => $quantity, ':exp' => $expiry_date,
        ':status' => $status, ':id' => $id
    ]);
    header("Location: index.php?act=quanlyvoucher");
    exit;
}

if ($act == 'xoavoucher' && isset($_POST['xoavoucher'])) {
    $id = (int)$_POST['id'];
    $data->execute_item("DELETE FROM vouchers WHERE id = :id", [':id' => $id]);
    header("Location: index.php?act=quanlyvoucher");
    exit;
}

// ==============================================================
// 10. KHUYẾN MÃI SẢN PHẨM (ADMIN / STAFF)
// ==============================================================
if ($act == 'capnhatkhuyenmai' && isset($_POST['capnhatkm'])) {
    $id = (int)$_POST['id'];
    $sale_price = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $is_sale = isset($_POST['is_sale']) ? 1 : 0;

    $sql = "UPDATE sanpham SET sale_price = :sale, is_sale = :is_sale WHERE id = :id";
    $data->execute_item($sql, [
        ':sale' => $sale_price,
        ':is_sale' => $is_sale,
        ':id' => $id
    ]);
    header("Location: index.php?act=khuyenmai");
    exit;
}

// ==============================================================
// 11. QUẢN LÝ ĐƠN HÀNG (SỬA TRẠNG THÁI ĐƠN HÀNG)
// ==============================================================
if ($act == 'doitrangthaidonhang' && isset($_POST['capnhat_tt'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['new_status'];

    $status_desc = [
        'cho_xac_nhan' => 'Đang chờ BloomShop xác nhận đơn hàng.',
        'xac_nhan'     => 'BloomShop đã xác nhận đơn hàng và đang chuẩn bị hoa tươi.',
        'dang_giao'    => 'Shipper đang trên đường vận chuyển hoa tươi đến địa chỉ nhận.',
        'da_giao'      => 'Đơn hàng đã được giao tới tay người nhận.',
        'thanh_cong'   => 'Đơn hàng đã hoàn thành thành công. Cảm ơn quý khách!',
        'da_huy'       => 'Đơn hàng đã bị hủy.'
    ];

    $desc = $status_desc[$new_status] ?? 'Trạng thái đơn hàng được cập nhật.';

    // Cập nhật đơn hàng
    $data->execute_item("UPDATE donhang SET status = :st WHERE id = :id", [':st' => $new_status, ':id' => $order_id]);

    // Ghi nhận tracking
    $data->execute_item("INSERT INTO order_tracking (order_id, status, description, created_at)
                         VALUES (:id, :st, :desc, NOW())", [
        ':id' => $order_id,
        ':st' => $new_status,
        ':desc' => $desc
    ]);

    // Lấy thông tin đơn hàng để tính toán cấp bậc thành viên nếu đơn hoàn tất
    $order = $data->readsingle("SELECT * FROM donhang WHERE id = :id", [':id' => $order_id]);
    if ($order && !empty($order['user_id']) && $new_status == 'thanh_cong') {
        $data->recalculate_user_tier($order['user_id']);
    }

    header("Location: index.php?act=quanlydonhang");
    exit;
}

// ==============================================================
// 12. DANH MỤC & SẢN PHẨM (CRUD GỐC)
// ==============================================================
if ($act == 'themdm' && isset($_POST['them'])) {
    $sql = "INSERT INTO danhmuc (name) VALUES (:name)";
    $data->execute_item($sql, [':name' => $_POST['name']]);
    header("Location: index.php?act=danhmuc");
    exit;
}

if ($act == 'capnhatdm' && isset($_POST['capnhat'])) {
    $sql = "UPDATE danhmuc SET name = :name WHERE id = :id";
    $data->execute_item($sql, [':name' => $_POST['name'], ':id' => $_POST['id']]);
    header("Location: index.php?act=danhmuc");
    exit;
}

if ($act == 'xoadm' && isset($_POST['xoa'])) {
    try {
        $data->execute_item("DELETE FROM danhmuc WHERE id = :id", [':id' => $_POST['id']]);
        header("Location: index.php?act=danhmuc");
        exit;
    } catch (PDOException $e) {
        $loi = 'Không thể xóa danh mục đang có sản phẩm.';
        $act = 'danhmuc';
    }
}

if ($act == 'themsp' && isset($_POST['them'])) {
    $name = trim($_POST['name']);
    $price = (float)$_POST['price'];
    $sale_price = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $is_sale = isset($_POST['is_sale']) ? 1 : 0;
    $description = trim($_POST['description'] ?? '');
    $id_dm = (int)$_POST['id_dm'];

    if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $duoi = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $mime = mime_content_type($_FILES['image']['tmp_name']);
        $loaiAnh = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
        
        if (isset($loaiAnh[$duoi]) && $loaiAnh[$duoi] == $mime) {
            $image = uniqid('hoa_') . '.' . $duoi;
            move_uploaded_file($_FILES['image']['tmp_name'], "../view/image/sanpham/" . $image);
            
            $sql = "INSERT INTO sanpham (name, price, sale_price, is_sale, description, image, id_dm)
                    VALUES (:name, :price, :sale_price, :is_sale, :description, :image, :id_dm)";
            $data->execute_item($sql, [
                ':name' => $name, ':price' => $price, ':sale_price' => $sale_price,
                ':is_sale' => $is_sale, ':description' => $description, ':image' => $image, ':id_dm' => $id_dm
            ]);
            header("Location: index.php?act=quanlysp");
            exit;
        }
    }
    $loi = 'Vui lòng chọn ảnh JPG, PNG hoặc WebP hợp lệ.';
}

if ($act == 'capnhatsp' && isset($_POST['capnhat'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $price = (float)$_POST['price'];
    $sale_price = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $is_sale = isset($_POST['is_sale']) ? 1 : 0;
    $description = trim($_POST['description'] ?? '');
    $id_dm = (int)$_POST['id_dm'];

    if ($_FILES['image']['error'] == UPLOAD_ERR_NO_FILE) {
        $sql = "UPDATE sanpham SET name=:name, price=:price, sale_price=:sale_price, is_sale=:is_sale, description=:description, id_dm=:id_dm WHERE id=:id";
        $data->execute_item($sql, [
            ':name' => $name, ':price' => $price, ':sale_price' => $sale_price,
            ':is_sale' => $is_sale, ':description' => $description, ':id_dm' => $id_dm, ':id' => $id
        ]);
    } else {
        $duoi = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $mime = $_FILES['image']['error'] == UPLOAD_ERR_OK ? mime_content_type($_FILES['image']['tmp_name']) : '';
        $loaiAnh = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
        
        if (isset($loaiAnh[$duoi]) && $loaiAnh[$duoi] == $mime) {
            $image = uniqid('hoa_') . '.' . $duoi;
            move_uploaded_file($_FILES['image']['tmp_name'], "../view/image/sanpham/" . $image);
            $sql = "UPDATE sanpham SET name=:name, price=:price, sale_price=:sale_price, is_sale=:is_sale, description=:description, image=:image, id_dm=:id_dm WHERE id=:id";
            $data->execute_item($sql, [
                ':name' => $name, ':price' => $price, ':sale_price' => $sale_price,
                ':is_sale' => $is_sale, ':description' => $description, ':image' => $image, ':id_dm' => $id_dm, ':id' => $id
            ]);
        } else {
            $loi = 'Ảnh không hợp lệ.';
        }
    }
    if ($loi == '') {
        header("Location: index.php?act=quanlysp");
        exit;
    }
    $act = 'suasp';
    $_GET['id'] = $id;
}

if ($act == 'xoasp' && isset($_POST['xoa'])) {
    $id = (int)$_POST['id'];
    $data->execute_item("DELETE FROM sanpham WHERE id = :id", [':id' => $id]);
    header("Location: index.php?act=quanlysp");
    exit;
}

// ==============================================================
// 13. RENDER VIEWS THEO TỪNG ACTION
// ==============================================================
include "../view/header.php";

if ($loi != '') {
    echo '<div class="container mt-3"><div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i>' . htmlspecialchars($loi) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>';
}
if ($thanhcong != '') {
    echo '<div class="container mt-3"><div class="alert alert-success alert-dismissible fade show" role="alert"><i class="bi bi-check-circle-fill me-2"></i>' . htmlspecialchars($thanhcong) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>';
}

switch ($act) {
    case 'dangky':
        include "../view/dangky.php";
        break;

    case 'dangnhap':
        include "../view/dangnhap.php";
        break;

    case 'giohang':
        $ds_voucher = $data->readitem("SELECT * FROM vouchers WHERE status = 1 AND expiry_date >= CURDATE() AND used_count < quantity");
        include "../view/giohang.php";
        break;

    case 'thanhtoan':
        if (empty($_SESSION['cart'])) {
            header("Location: index.php?act=giohang");
            exit;
        }
        $current_user = null;
        if (isset($_SESSION['id'])) {
            $current_user = $data->readsingle("SELECT * FROM users WHERE id = :id", [':id' => $_SESSION['id']]);
        }
        include "../view/thanhtoan.php";
        break;

    case 'dathangthanhcong':
        $id = (int)($_GET['id'] ?? 0);
        $order = $data->readsingle("SELECT * FROM donhang WHERE id = :id", [':id' => $id]);
        if (!$order) {
            header("Location: index.php?act=trangchu");
            exit;
        }
        include "../view/dathangthanhcong.php";
        break;

    case 'lichsudonhang':
        if (!isset($_SESSION['id'])) {
            header("Location: index.php?act=dangnhap");
            exit;
        }
        $danhsach_donhang = $data->readitem("SELECT * FROM donhang WHERE user_id = :uid ORDER BY id DESC", [':uid' => $_SESSION['id']]);
        include "../view/lichsudonhang.php";
        break;

    case 'chitietdonhang':
        $id = (int)($_GET['id'] ?? 0);
        $order = $data->readsingle("SELECT * FROM donhang WHERE id = :id", [':id' => $id]);
        if (!$order) {
            header("Location: index.php?act=trangchu");
            exit;
        }
        $order_items = $data->readitem("SELECT * FROM chitiet_donhang WHERE order_id = :id", [':id' => $id]);
        include "../view/chitietdonhang.php";
        break;

    case 'theodoigiaohang':
        $id = (int)($_GET['id'] ?? 0);
        $order = $data->readsingle("SELECT * FROM donhang WHERE id = :id", [':id' => $id]);
        if (!$order) {
            header("Location: index.php?act=trangchu");
            exit;
        }
        $tracking_logs = $data->readitem("SELECT * FROM order_tracking WHERE order_id = :id ORDER BY created_at ASC", [':id' => $id]);
        include "../view/theodoigiaohang.php";
        break;

    case 'inhoadon':
        $id = (int)($_GET['id'] ?? 0);
        $order = $data->readsingle("SELECT * FROM donhang WHERE id = :id", [':id' => $id]);
        if ($order) {
            $items = $data->readitem("SELECT * FROM chitiet_donhang WHERE order_id = :id", [':id' => $id]);
            echo Mailer::generate_invoice_html($order, $items);
            exit;
        }
        header("Location: index.php?act=trangchu");
        exit;

    case 'tracuudonhang':
        include "../view/tracuudonhang.php";
        break;

    case 'chitiettsp':
        $id = (int)($_GET['id'] ?? 0);
        $sql = "SELECT sanpham.*, danhmuc.name AS tendanhmuc 
                FROM sanpham 
                LEFT JOIN danhmuc ON sanpham.id_dm = danhmuc.id 
                WHERE sanpham.id = :id";
        $sp = $data->readsingle($sql, [':id' => $id]);
        if (!$sp) {
            header("Location: index.php?act=sanpham");
            exit;
        }
        // Đánh giá
        $danhsach_danhgia = $data->readitem("SELECT * FROM danhgia WHERE product_id = :pid AND status = 1 ORDER BY id DESC", [':pid' => $id]);
        $avg_res = $data->readsingle("SELECT AVG(rating) AS avg_rate FROM danhgia WHERE product_id = :pid AND status = 1", [':pid' => $id]);
        $avg_rating = $avg_res ? (float)$avg_res['avg_rate'] : 5;

        // Sản phẩm liên quan
        $sp_lienquan = $data->readitem("SELECT * FROM sanpham WHERE id_dm = :dm AND id != :id LIMIT 3", [':dm' => $sp['id_dm'], ':id' => $id]);
        include "../view/chitietsp.php";
        break;

    case 'sanpham':
        $danhmuc = $data->readitem("SELECT * FROM danhmuc");
        
        $where = ["1=1"];
        $params = [];

        // Tìm kiếm theo từ khóa
        if (!empty($_GET['q'])) {
            $where[] = "sanpham.name LIKE :q";
            $params[':q'] = '%' . trim($_GET['q']) . '%';
        }

        // Lọc theo danh mục
        if (!empty($_GET['id_dm'])) {
            $where[] = "sanpham.id_dm = :id_dm";
            $params[':id_dm'] = (int)$_GET['id_dm'];
        }

        // Sắp xếp
        $sort_sql = "ORDER BY sanpham.id DESC";
        $sort = $_GET['sort'] ?? '';
        if ($sort == 'price_asc') {
            $sort_sql = "ORDER BY COALESCE(NULLIF(sanpham.sale_price, 0), sanpham.price) ASC";
        } elseif ($sort == 'price_desc') {
            $sort_sql = "ORDER BY COALESCE(NULLIF(sanpham.sale_price, 0), sanpham.price) DESC";
        } elseif ($sort == 'sale') {
            $where[] = "sanpham.is_sale = 1 AND sanpham.sale_price IS NOT NULL";
            $sort_sql = "ORDER BY sanpham.id DESC";
        }

        $where_str = implode(" AND ", $where);

        // Phân trang
        $count_res = $data->readsingle("SELECT COUNT(*) AS total FROM sanpham WHERE $where_str", $params);
        $total_products = $count_res ? (int)$count_res['total'] : 0;
        
        $per_page = 8;
        $total_pages = ceil($total_products / $per_page);
        $current_page = max(1, min($total_pages ?: 1, (int)($_GET['page'] ?? 1)));
        $offset = ($current_page - 1) * $per_page;

        $sql = "SELECT sanpham.*, danhmuc.name AS tendanhmuc
                FROM sanpham 
                LEFT JOIN danhmuc ON sanpham.id_dm = danhmuc.id
                WHERE $where_str
                $sort_sql
                LIMIT $per_page OFFSET $offset";
        $sanpham = $data->readitem($sql, $params);
        include "../view/dssanpham.php";
        break;

    // --- ADMIN: DANH MỤC ---
    case 'danhmuc':
        $danhsach = $data->readitem("SELECT * FROM danhmuc");
        include "../view/danhmuc.php";
        break;
    case 'themdm':
        include "../view/themdm.php";
        break;
    case 'suadm':
        $id = (int) $_GET['id'];
        $danhsach = $data->readitem("SELECT * FROM danhmuc WHERE id = $id");
        if ($danhsach) {
            $dm = $danhsach[0];
            include "../view/suadm.php";
        }
        break;

    // --- ADMIN: SẢN PHẨM & KHUYẾN MÃI ---
    case 'quanlysp':
        $sql = "SELECT sanpham.*, danhmuc.name AS tendanhmuc
                FROM sanpham JOIN danhmuc ON sanpham.id_dm = danhmuc.id ORDER BY sanpham.id DESC";
        $danhsach = $data->readitem($sql);
        include "../view/sanpham.php";
        break;
    case 'themsp':
        $danhsach = $data->readitem("SELECT * FROM danhmuc");
        include "../view/themsp.php";
        break;
    case 'suasp':
        $id = (int) $_GET['id'];
        $danhsach = $data->readitem("SELECT * FROM sanpham WHERE id = $id");
        if ($danhsach) {
            $sp = $danhsach[0];
            $danhmuc = $data->readitem("SELECT * FROM danhmuc");
            include "../view/suasp.php";
        }
        break;
    case 'khuyenmai':
        $sql = "SELECT sanpham.*, danhmuc.name AS tendanhmuc
                FROM sanpham LEFT JOIN danhmuc ON sanpham.id_dm = danhmuc.id ORDER BY sanpham.is_sale DESC, sanpham.id DESC";
        $danhsach_sp = $data->readitem($sql);
        include "../view/admin_khuyenmai.php";
        break;

    // --- ADMIN: VOUCHER ---
    case 'quanlyvoucher':
        $danhsach_vc = $data->readitem("SELECT * FROM vouchers ORDER BY id DESC");
        include "../view/admin_voucher.php";
        break;
    case 'themvoucher':
        include "../view/admin_themvoucher.php";
        break;
    case 'suavoucher':
        $id = (int)$_GET['id'];
        $vc = $data->readsingle("SELECT * FROM vouchers WHERE id = :id", [':id' => $id]);
        if ($vc) {
            include "../view/admin_suavoucher.php";
        }
        break;

    // --- ADMIN: ĐƠN HÀNG ---
    case 'quanlydonhang':
        $where = "1=1";
        $params = [];
        if (!empty($_GET['status'])) {
            $where .= " AND status = :status";
            $params[':status'] = $_GET['status'];
        }
        $danhsach_dh = $data->readitem("SELECT * FROM donhang WHERE $where ORDER BY id DESC", $params);
        include "../view/admin_donhang.php";
        break;

    // --- ADMIN: ĐÁNH GIÁ ---
    case 'quanlydanhgia':
        $where = "1=1";
        $params = [];
        if (!empty($_GET['star'])) {
            $where .= " AND danhgia.rating = :star";
            $params[':star'] = (int)$_GET['star'];
        }
        $sql = "SELECT danhgia.*, sanpham.name AS product_name, sanpham.image AS product_image
                FROM danhgia 
                JOIN sanpham ON danhgia.product_id = sanpham.id
                WHERE $where
                ORDER BY danhgia.id DESC";
        $danhsach_dg = $data->readitem($sql, $params);
        include "../view/admin_danhgia.php";
        break;

    // --- ADMIN: KHÁCH HÀNG (DIAMOND, GOLD, SILVER) ---
    case 'quanlykh':
        $where = "role = 'user'";
        $params = [];
        if (!empty($_GET['tier'])) {
            $where .= " AND tier = :tier";
            $params[':tier'] = $_GET['tier'];
        }
        $danhsach_kh = $data->readitem("SELECT * FROM users WHERE $where ORDER BY total_spent DESC, id DESC", $params);

        $count_diamond = $data->readsingle("SELECT COUNT(*) as c FROM users WHERE role = 'user' AND tier = 'diamond'")['c'] ?? 0;
        $count_gold = $data->readsingle("SELECT COUNT(*) as c FROM users WHERE role = 'user' AND tier = 'gold'")['c'] ?? 0;
        $count_silver = $data->readsingle("SELECT COUNT(*) as c FROM users WHERE role = 'user' AND tier = 'silver'")['c'] ?? 0;

        include "../view/admin_khachhang.php";
        break;

    // --- ADMIN: NHÂN VIÊN ---
    case 'quanlynv':
        $danhsach_nv = $data->readitem("SELECT * FROM users WHERE role = 'staff' ORDER BY id DESC");
        include "../view/admin_nhanvien.php";
        break;
    case 'themnv':
        include "../view/admin_themnv.php";
        break;
    case 'suanv':
        $id = (int)$_GET['id'];
        $nv = $data->readsingle("SELECT * FROM users WHERE id = :id AND role = 'staff'", [':id' => $id]);
        if ($nv) {
            include "../view/admin_suanv.php";
        }
        break;

    // --- ADMIN: DOANH THU & THỐNG KÊ ---
    case 'doanhthu':
        // KPI Tổng quan
        $tong_doanhthu = (float)($data->readsingle("SELECT COALESCE(SUM(total_price), 0) AS total FROM donhang WHERE status = 'thanh_cong'")['total'] ?? 0);
        $tong_don_thanhcong = (int)($data->readsingle("SELECT COUNT(*) AS total FROM donhang WHERE status = 'thanh_cong'")['total'] ?? 0);
        
        // Doanh thu tuần này (7 ngày qua)
        $doanhthu_tuan = (float)($data->readsingle("SELECT COALESCE(SUM(total_price), 0) AS total FROM donhang WHERE status = 'thanh_cong' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")['total'] ?? 0);
        
        // Doanh thu tháng này
        $doanhthu_thang = (float)($data->readsingle("SELECT COALESCE(SUM(total_price), 0) AS total FROM donhang WHERE status = 'thanh_cong' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())")['total'] ?? 0);
        
        // Doanh thu năm nay
        $doanhthu_nam = (float)($data->readsingle("SELECT COALESCE(SUM(total_price), 0) AS total FROM donhang WHERE status = 'thanh_cong' AND YEAR(created_at) = YEAR(CURRENT_DATE())")['total'] ?? 0);

        // Chi tiết 7 ngày qua
        $stats_7days = [];
        for ($i = 6; $i >= 0; $i--) {
            $day_date = date('Y-m-d', strtotime("-$i days"));
            $row = $data->readsingle("SELECT COALESCE(SUM(total_price), 0) AS rev, COUNT(*) AS cnt 
                                      FROM donhang 
                                      WHERE status = 'thanh_cong' AND DATE(created_at) = :d", [':d' => $day_date]);
            $stats_7days[] = [
                'date' => $day_date,
                'revenue' => (float)($row['rev'] ?? 0),
                'orders_count' => (int)($row['cnt'] ?? 0)
            ];
        }

        // Chi tiết 12 tháng của năm hiện tại
        $stats_months = [];
        for ($m = 1; $m <= 12; $m++) {
            $row = $data->readsingle("SELECT COALESCE(SUM(total_price), 0) AS rev, COUNT(*) AS cnt 
                                      FROM donhang 
                                      WHERE status = 'thanh_cong' AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND MONTH(created_at) = :m", [':m' => $m]);
            $stats_months[] = [
                'month' => $m,
                'revenue' => (float)($row['rev'] ?? 0),
                'orders_count' => (int)($row['cnt'] ?? 0)
            ];
        }

        // Doanh thu theo các năm
        $stats_years = $data->readitem("SELECT YEAR(created_at) AS year, COALESCE(SUM(total_price), 0) AS revenue, COUNT(*) AS orders_count 
                                        FROM donhang 
                                        WHERE status = 'thanh_cong' 
                                        GROUP BY YEAR(created_at) 
                                        ORDER BY year DESC");

        // Sản phẩm Bán Chạy Nhất (Best-sellers)
        $sql_bc = "SELECT sanpham.id, sanpham.name, sanpham.image, sanpham.price,
                          COALESCE(SUM(chitiet_donhang.quantity), 0) AS total_sold,
                          COALESCE(SUM(chitiet_donhang.total), 0) AS total_revenue
                   FROM sanpham
                   JOIN chitiet_donhang ON sanpham.id = chitiet_donhang.product_id
                   JOIN donhang ON chitiet_donhang.order_id = donhang.id
                   WHERE donhang.status = 'thanh_cong'
                   GROUP BY sanpham.id
                   ORDER BY total_sold DESC
                   LIMIT 5";
        $sp_banchay = $data->readitem($sql_bc);

        // Sản phẩm Ế Nhất (Ít bán nhất hoặc chưa bán được chiếc nào)
        $sql_e = "SELECT sanpham.id, sanpham.name, sanpham.image, sanpham.price, danhmuc.name AS tendanhmuc,
                         COALESCE(SUM(CASE WHEN donhang.status = 'thanh_cong' THEN chitiet_donhang.quantity ELSE 0 END), 0) AS total_sold
                  FROM sanpham
                  LEFT JOIN danhmuc ON sanpham.id_dm = danhmuc.id
                  LEFT JOIN chitiet_donhang ON sanpham.id = chitiet_donhang.product_id
                  LEFT JOIN donhang ON chitiet_donhang.order_id = donhang.id
                  GROUP BY sanpham.id
                  ORDER BY total_sold ASC, sanpham.id DESC
                  LIMIT 6";
        $sp_enhat = $data->readitem($sql_e);

        include "../view/admin_doanhthu.php";
        break;

    // --- MẶC ĐỊNH: TRANG CHỦ ---
    default:
        $sanpham = $data->readitem("SELECT * FROM sanpham ORDER BY is_sale DESC, id DESC LIMIT 8");
        include "../view/trangchu.php";
        break;
}

include "../view/footer.php";
?>