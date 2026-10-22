<?php
// controller/c_sanpham.php - Xử lý Sản phẩm, Tìm kiếm, Phân trang, Chi tiết & Đánh giá

// 1. Gửi đánh giá & bình luận sản phẩm
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
    }
    header("Location: index.php?act=chitiettsp&id=$product_id&msg=danhgia_thanhcong");
    exit;
}

// 2. Chi tiết sản phẩm
if ($act == 'chitiettsp') {
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

    // Lấy danh sách đánh giá đã duyệt
    $danhsach_danhgia = $data->readitem("SELECT * FROM danhgia WHERE product_id = :pid AND status = 1 ORDER BY id DESC", [':pid' => $id]);
    $avg_res = $data->readsingle("SELECT AVG(rating) AS avg_rate FROM danhgia WHERE product_id = :pid AND status = 1", [':pid' => $id]);
    $avg_rating = $avg_res ? (float)$avg_res['avg_rate'] : 5;

    // Sản phẩm liên quan cùng danh mục
    $sp_lienquan = $data->readitem("SELECT * FROM sanpham WHERE id_dm = :dm AND id != :id LIMIT 3", [
        ':dm' => $sp['id_dm'], 
        ':id' => $id
    ]);

    hien_thi_view('chitietsp.php', [
        'sp' => $sp,
        'danhsach_danhgia' => $danhsach_danhgia,
        'avg_rating' => $avg_rating,
        'sp_lienquan' => $sp_lienquan
    ]);
    exit;
}

// 3. Danh sách sản phẩm (Tìm kiếm, Lọc danh mục, Sắp xếp & Phân trang)
if ($act == 'sanpham') {
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

    hien_thi_view('dssanpham.php', [
        'sanpham' => $sanpham,
        'danhmuc' => $danhmuc,
        'total_products' => $total_products,
        'total_pages' => $total_pages,
        'current_page' => $current_page
    ]);
    exit;
}

// 4. Mặc định: Trang chủ (Hiển thị các mẫu hoa nổi bật và khuyến mãi)
$sanpham = $data->readitem("SELECT * FROM sanpham ORDER BY is_sale DESC, id DESC LIMIT 8");
hien_thi_view('trangchu.php', [
    'sanpham' => $sanpham
]);
