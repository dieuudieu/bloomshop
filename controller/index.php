<?php
session_start();
require_once "../model/xl_database.php";

$data = new xl_data();
$act = $_REQUEST['act'] ?? 'trangchu';
$loi = '';

// Chỉ Admin mới được truy cập các chức năng quản lý.
$quanly = ['danhmuc', 'themdm', 'suadm', 'capnhatdm', 'xoadm',
           'quanlysp', 'themsp', 'suasp', 'capnhatsp', 'xoasp'];

if (in_array($act, $quanly)) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        header("Location: index.php?act=dangnhap");
        exit;
    }
}

// Đăng ký: tài khoản mới luôn là user.
if ($act == 'dangky' && isset($_POST['dangky'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "SELECT * FROM users WHERE email = :email";
    $ds = $data->readitem($sql, [':email' => $email]);

    if (count($ds) > 0) {
        $loi = 'Email đã được đăng ký.';
    } else {
        $sql = "INSERT INTO users (name, email, password, role)
                VALUES (:name, :email, :password, 'user')";
        $data->execute_item($sql, [
            ':name' => $name, ':email' => $email, ':password' => $password
        ]);
        header("Location: index.php?act=dangnhap");
        exit;
    }
}

// Đăng nhập.
if ($act == 'dangnhap' && isset($_POST['dangnhap'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE email = :email";
    $ds = $data->readitem($sql, [':email' => $email]);

    if (count($ds) > 0 && password_verify($password, $ds[0]['password'])) {
        session_regenerate_id(true);
        $_SESSION['id'] = $ds[0]['id'];
        $_SESSION['name'] = $ds[0]['name'];
        $_SESSION['role'] = $ds[0]['role'];
        header("Location: index.php?act=trangchu");
        exit;
    } else {
        $loi = 'Email hoặc mật khẩu không đúng.';
    }
}

// Đăng xuất.
if ($act == 'dangxuat' && isset($_POST['dangxuat'])) {
    session_unset();
    session_destroy();
    header("Location: index.php?act=trangchu");
    exit;
}

// Thêm, sửa, xóa danh mục.
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
    $sql = "DELETE FROM danhmuc WHERE id = :id";
    try {
        $data->execute_item($sql, [':id' => $_POST['id']]);
        header("Location: index.php?act=danhmuc");
        exit;
    } catch (PDOException $e) {
        $loi = 'Không thể xóa danh mục đang có sản phẩm.';
        $act = 'danhmuc';
    }
}

// Thêm sản phẩm kèm ảnh.
if ($act == 'themsp' && isset($_POST['them'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $id_dm = $_POST['id_dm'];

    if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $duoi = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $mime = mime_content_type($_FILES['image']['tmp_name']);
        $loaiAnh = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
                    'png' => 'image/png', 'webp' => 'image/webp'];
        if (isset($loaiAnh[$duoi]) && $loaiAnh[$duoi] == $mime) {
            $image = uniqid('hoa_') . '.' . $duoi;
            move_uploaded_file($_FILES['image']['tmp_name'], "../view/image/sanpham/" . $image);
            $sql = "INSERT INTO sanpham (name, price, image, id_dm)
                    VALUES (:name, :price, :image, :id_dm)";
            $data->execute_item($sql, [
                ':name' => $name, ':price' => $price,
                ':image' => $image, ':id_dm' => $id_dm
            ]);
            header("Location: index.php?act=quanlysp");
            exit;
        }
    }
    $loi = 'Vui lòng chọn ảnh JPG, PNG hoặc WebP hợp lệ.';
}

// Sửa sản phẩm: có ảnh mới thì đổi ảnh, không thì giữ ảnh cũ.
if ($act == 'capnhatsp' && isset($_POST['capnhat'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $id_dm = $_POST['id_dm'];

    if ($_FILES['image']['error'] == UPLOAD_ERR_NO_FILE) {
        $sql = "UPDATE sanpham SET name=:name, price=:price, id_dm=:id_dm WHERE id=:id";
        $data->execute_item($sql, [
            ':name' => $name, ':price' => $price,
            ':id_dm' => $id_dm, ':id' => $id
        ]);
    } else {
        $duoi = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $mime = $_FILES['image']['error'] == UPLOAD_ERR_OK ? mime_content_type($_FILES['image']['tmp_name']) : '';
        $loaiAnh = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
                    'png' => 'image/png', 'webp' => 'image/webp'];
        if (isset($loaiAnh[$duoi]) && $loaiAnh[$duoi] == $mime) {
            $image = uniqid('hoa_') . '.' . $duoi;
            move_uploaded_file($_FILES['image']['tmp_name'], "../view/image/sanpham/" . $image);
            $sql = "UPDATE sanpham SET name=:name, price=:price, image=:image,
                    id_dm=:id_dm WHERE id=:id";
            $data->execute_item($sql, [
                ':name' => $name, ':price' => $price, ':image' => $image,
                ':id_dm' => $id_dm, ':id' => $id
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
    $sql = "DELETE FROM sanpham WHERE id = :id";
    $data->execute_item($sql, [':id' => $_POST['id']]);
    header("Location: index.php?act=quanlysp");
    exit;
}

include "../view/header.php";
if ($loi != '') {
    echo '<div class="container mt-3 alert alert-danger">' . htmlspecialchars($loi) . '</div>';
}

switch ($act) {
    case 'dangky':
        include "../view/dangky.php";
        break;
    case 'dangnhap':
        include "../view/dangnhap.php";
        break;
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
    case 'quanlysp':
        $sql = "SELECT sanpham.*, danhmuc.name AS tendanhmuc
                FROM sanpham JOIN danhmuc ON sanpham.id_dm = danhmuc.id";
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
    case 'sanpham':

        // 1. Lấy dữ liệu tìm kiếm từ URL
        $keyword = trim($_GET['keyword'] ?? '');
        $id_dm = (int) ($_GET['id_dm'] ?? 0);
        $page = (int) ($_GET['page'] ?? 1);

        if ($page < 1) {
            $page = 1;
        }

        // 2. Lấy danh sách danh mục cho bộ lọc
        $danhmuc = $data->readitem("SELECT * FROM danhmuc");

        // 3. Tạo câu SQL tìm kiếm sản phẩm
        $sql = "SELECT * FROM sanpham WHERE 1=1";
        $params = [];

        // Tìm theo tên
        if ($keyword != '') {
            $sql .= " AND name LIKE :keyword";
            $params[':keyword'] = "%" . $keyword . "%";
        }

        // Lọc theo danh mục
        if ($id_dm > 0) {
            $sql .= " AND id_dm = :id_dm";
            $params[':id_dm'] = $id_dm;
        }

        // 4. Đếm số sản phẩm tìm được
        $sqlCount = str_replace(
            "SELECT *",
            "SELECT COUNT(*) AS tong",
            $sql
        );

        $ketqua = $data->readitem($sqlCount, $params);
        $tongSanPham = (int) $ketqua[0]['tong'];

        // 5. Tính số trang
        $limit = 9;
        $tongTrang = (int) ceil($tongSanPham / $limit);

        if ($tongTrang > 0 && $page > $tongTrang) {
            $page = $tongTrang;
        }

        $offset = ($page - 1) * $limit;

        // 6. Lấy sản phẩm thuộc trang hiện tại
        $sql .= " ORDER BY id DESC LIMIT $limit OFFSET $offset";

        $sanpham = $data->readitem($sql, $params);

        // 7. Hiển thị giao diện
        include "../view/dssanpham.php";
        break;
    default:
        $sanpham = $data->readitem("SELECT * FROM sanpham ORDER BY id DESC");
        include "../view/trangchu.php";
        break;
}
include "../view/footer.php";
?>
