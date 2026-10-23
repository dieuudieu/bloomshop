<?php
// controller/c_quantri.php - Xử lý toàn bộ Quản trị dành cho Admin & Nhân viên (Staff)

// ==============================================================
// 1. QUẢN LÝ NHÂN VIÊN (ADMIN ONLY)
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
        $_SESSION['flash_success'] = "Thêm nhân viên mới thành công!";
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
    $_SESSION['flash_success'] = "Cập nhật thông tin nhân viên thành công!";
    header("Location: index.php?act=quanlynv");
    exit;
}

if ($act == 'xoanv') {
    $id = (int)($_POST['id'] ?? ($_GET['id'] ?? 0));
    if ($id > 0) {
        $data->execute_item("DELETE FROM users WHERE id = :id AND role = 'staff'", [':id' => $id]);
        $_SESSION['flash_success'] = "Đã xóa nhân viên thành công!";
    }
    header("Location: index.php?act=quanlynv");
    exit;
}

// ==============================================================
// 2. QUẢN LÝ ĐÁNH GIÁ (ẨN/HIỆN, XÓA)
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
// 3. QUẢN LÝ KHÁCH HÀNG (KHÓA/MỞ TÀI KHOẢN)
// ==============================================================
if ($act == 'khoakh') {
    $id = (int)$_POST['id'];
    $status = (int)$_POST['status'];
    $data->execute_item("UPDATE users SET status = :st WHERE id = :id AND role = 'user'", [':st' => $status, ':id' => $id]);
    header("Location: index.php?act=quanlykh");
    exit;
}

// ==============================================================
// 4. QUẢN LÝ VOUCHER (THÊM, SỬA, XÓA)
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
// 5. KHUYẾN MÃI SẢN PHẨM
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
// 6. QUẢN LÝ ĐƠN HÀNG (SỬA TRẠNG THÁI THEO QUY TẮC MỘT CHIỀU TMĐT)
// ==============================================================
if ($act == 'doitrangthaidonhang' && isset($_POST['capnhat_tt'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['new_status'] ?? '';

    $order = $data->readsingle("SELECT * FROM donhang WHERE id = :id", [':id' => $order_id]);
    if (!$order) {
        header("Location: index.php?act=quanlydonhang");
        exit;
    }

    $current_status = $order['status'];

    // Bản đồ chuyển tiếp trạng thái chuẩn của quy trình TMĐT (State Machine)
    // Quy tắc: Đơn đã sang bước tiếp theo thì KHÔNG THỂ quay lại bước trước!
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
        header("Location: index.php?act=quanlydonhang");
        exit;
    }

    // Kiểm tra hợp lệ: Không cho phép quay lùi bước trước hoặc chuyển trạng thái không hợp lệ
    if (!isset($allowed_transitions[$current_status]) || !in_array($new_status, $allowed_transitions[$current_status])) {
        $curr_name = $status_names[$current_status] ?? $current_status;
        $new_name = $status_names[$new_status] ?? $new_status;
        $_SESSION['flash_error'] = "Quy tắc vận hành: Đơn hàng đang ở bước '$curr_name' không thể quay lại bước trước hoặc chuyển sang '$new_name'!";
        header("Location: index.php?act=quanlydonhang");
        exit;
    }

    $status_desc = [
        'xac_nhan'     => 'BloomShop đã xác nhận đơn hàng và đang cắm hoa tươi chuẩn bị xuất kho.',
        'dang_giao'    => 'Hoa tươi đã xuất kho và bàn giao cho Shipper đang trên đường vận chuyển tới địa chỉ nhận.',
        'da_giao'      => 'Shipper đã giao hoa tươi thành công đến tận tay người nhận.',
        'thanh_cong'   => 'Đơn hàng hoàn tất và quyết toán thành công. Khách hàng đã được tích lũy chi tiêu VIP!',
        'da_huy'       => 'Đơn hàng đã bị hủy. ' . (isset($_SESSION['name']) ? "Thực hiện bởi: {$_SESSION['name']}." : '')
    ];

    $desc = $status_desc[$new_status] ?? 'Trạng thái đơn hàng được cập nhật.';

    // Cập nhật trạng thái đơn
    $data->execute_item("UPDATE donhang SET status = :st WHERE id = :id", [':st' => $new_status, ':id' => $order_id]);

    // Ghi nhận nhật ký tracking
    $data->execute_item("INSERT INTO order_tracking (order_id, status, description, created_at)
                         VALUES (:id, :st, :desc, NOW())", [
        ':id' => $order_id,
        ':st' => $new_status,
        ':desc' => $desc
    ]);

    // Nếu đơn hàng bị hủy, hoàn lại số lượt đã dùng của voucher nếu có
    if ($new_status == 'da_huy' && !empty($order['voucher_code'])) {
        $data->execute_item("UPDATE vouchers SET used_count = GREATEST(0, used_count - 1) WHERE code = :code", [':code' => $order['voucher_code']]);
    }

    // Tự động tính toán và thăng hạng thành viên (Silver, Gold, Diamond) khi đơn thành công
    if (!empty($order['user_id']) && $new_status == 'thanh_cong') {
        $data->recalculate_user_tier($order['user_id']);
    }

    $_SESSION['flash_success'] = "Cập nhật đơn hàng #$order_id sang trạng thái '{$status_names[$new_status]}' thành công!";
    header("Location: index.php?act=quanlydonhang");
    exit;
}

// ==============================================================
// 7. DANH MỤC & SẢN PHẨM (THÊM, SỬA, XÓA)
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
}

if ($act == 'xoasp' && isset($_POST['xoa'])) {
    $id = (int)$_POST['id'];
    $data->execute_item("DELETE FROM sanpham WHERE id = :id", [':id' => $id]);
    header("Location: index.php?act=quanlysp");
    exit;
}

// ==============================================================
// 8. RENDER CÁC MÀN HÌNH QUẢN TRỊ
// ==============================================================

// Danh mục
if ($act == 'danhmuc') {
    $danhsach = $data->readitem("SELECT * FROM danhmuc");
    hien_thi_view('danhmuc.php', ['danhsach' => $danhsach]);
    exit;
}
if ($act == 'themdm') {
    hien_thi_view('themdm.php');
    exit;
}
if ($act == 'suadm') {
    $id = (int)$_GET['id'];
    $danhsach = $data->readitem("SELECT * FROM danhmuc WHERE id = $id");
    if ($danhsach) {
        hien_thi_view('suadm.php', ['dm' => $danhsach[0]]);
        exit;
    }
}

// Sản phẩm
if ($act == 'quanlysp') {
    $sql = "SELECT sanpham.*, danhmuc.name AS tendanhmuc
            FROM sanpham JOIN danhmuc ON sanpham.id_dm = danhmuc.id ORDER BY sanpham.id DESC";
    $danhsach = $data->readitem($sql);
    hien_thi_view('sanpham.php', ['danhsach' => $danhsach]);
    exit;
}
if ($act == 'themsp') {
    $danhsach = $data->readitem("SELECT * FROM danhmuc");
    hien_thi_view('themsp.php', ['danhsach' => $danhsach]);
    exit;
}
if ($act == 'suasp') {
    $id = (int)$_GET['id'];
    $danhsach = $data->readitem("SELECT * FROM sanpham WHERE id = $id");
    if ($danhsach) {
        $danhmuc = $data->readitem("SELECT * FROM danhmuc");
        hien_thi_view('suasp.php', ['sp' => $danhsach[0], 'danhmuc' => $danhmuc]);
        exit;
    }
}
if ($act == 'khuyenmai') {
    $sql = "SELECT sanpham.*, danhmuc.name AS tendanhmuc
            FROM sanpham LEFT JOIN danhmuc ON sanpham.id_dm = danhmuc.id ORDER BY sanpham.is_sale DESC, sanpham.id DESC";
    $danhsach_sp = $data->readitem($sql);
    hien_thi_view('admin_khuyenmai.php', ['danhsach_sp' => $danhsach_sp]);
    exit;
}

// Voucher
if ($act == 'quanlyvoucher') {
    $danhsach_vc = $data->readitem("SELECT * FROM vouchers ORDER BY id DESC");
    hien_thi_view('admin_voucher.php', ['danhsach_vc' => $danhsach_vc]);
    exit;
}
if ($act == 'themvoucher') {
    hien_thi_view('admin_themvoucher.php');
    exit;
}
if ($act == 'suavoucher') {
    $id = (int)$_GET['id'];
    $vc = $data->readsingle("SELECT * FROM vouchers WHERE id = :id", [':id' => $id]);
    if ($vc) {
        hien_thi_view('admin_suavoucher.php', ['vc' => $vc]);
        exit;
    }
}

// Đơn hàng
if ($act == 'quanlydonhang') {
    $where = "1=1";
    $params = [];
    if (!empty($_GET['status'])) {
        $where .= " AND status = :status";
        $params[':status'] = $_GET['status'];
    }
    $danhsach_dh = $data->readitem("SELECT * FROM donhang WHERE $where ORDER BY id DESC", $params);
    hien_thi_view('admin_donhang.php', ['danhsach_dh' => $danhsach_dh]);
    exit;
}

// Đánh giá
if ($act == 'quanlydanhgia') {
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
    hien_thi_view('admin_danhgia.php', ['danhsach_dg' => $danhsach_dg]);
    exit;
}

// Khách hàng
if ($act == 'quanlykh') {
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

    hien_thi_view('admin_khachhang.php', [
        'danhsach_kh' => $danhsach_kh,
        'count_diamond' => $count_diamond,
        'count_gold' => $count_gold,
        'count_silver' => $count_silver
    ]);
    exit;
}

// Nhân viên (Admin)
if ($act == 'quanlynv') {
    $danhsach_nv = $data->readitem("SELECT * FROM users WHERE role = 'staff' ORDER BY id DESC");
    hien_thi_view('admin_nhanvien.php', ['danhsach_nv' => $danhsach_nv]);
    exit;
}
if ($act == 'themnv') {
    hien_thi_view('admin_themnv.php');
    exit;
}
if ($act == 'suanv') {
    $id = (int)$_GET['id'];
    $nv = $data->readsingle("SELECT * FROM users WHERE id = :id AND role = 'staff'", [':id' => $id]);
    if ($nv) {
        hien_thi_view('admin_suanv.php', ['nv' => $nv]);
        exit;
    }
}

// Doanh thu (Admin)
if ($act == 'doanhthu') {
    $tong_doanhthu = (float)($data->readsingle("SELECT COALESCE(SUM(total_price), 0) AS total FROM donhang WHERE status = 'thanh_cong'")['total'] ?? 0);
    $tong_don_thanhcong = (int)($data->readsingle("SELECT COUNT(*) AS total FROM donhang WHERE status = 'thanh_cong'")['total'] ?? 0);
    
    $doanhthu_tuan = (float)($data->readsingle("SELECT COALESCE(SUM(total_price), 0) AS total FROM donhang WHERE status = 'thanh_cong' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")['total'] ?? 0);
    $doanhthu_thang = (float)($data->readsingle("SELECT COALESCE(SUM(total_price), 0) AS total FROM donhang WHERE status = 'thanh_cong' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())")['total'] ?? 0);
    $doanhthu_nam = (float)($data->readsingle("SELECT COALESCE(SUM(total_price), 0) AS total FROM donhang WHERE status = 'thanh_cong' AND YEAR(created_at) = YEAR(CURRENT_DATE())")['total'] ?? 0);

    // 7 ngày gần nhất
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

    // 12 tháng năm hiện tại
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

    // Các năm
    $stats_years = $data->readitem("SELECT YEAR(created_at) AS year, COALESCE(SUM(total_price), 0) AS revenue, COUNT(*) AS orders_count 
                                    FROM donhang 
                                    WHERE status = 'thanh_cong' 
                                    GROUP BY YEAR(created_at) 
                                    ORDER BY year DESC");

    // Sản phẩm bán chạy nhất
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

    // Sản phẩm ế nhất
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

    hien_thi_view('admin_doanhthu.php', [
        'tong_doanhthu' => $tong_doanhthu,
        'tong_don_thanhcong' => $tong_don_thanhcong,
        'doanhthu_tuan' => $doanhthu_tuan,
        'doanhthu_thang' => $doanhthu_thang,
        'doanhthu_nam' => $doanhthu_nam,
        'stats_7days' => $stats_7days,
        'stats_months' => $stats_months,
        'stats_years' => $stats_years,
        'sp_banchay' => $sp_banchay,
        'sp_enhat' => $sp_enhat
    ]);
    exit;
}
