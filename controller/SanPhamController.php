<?php
// controller/SanPhamController.php - Controller điều hướng Sản phẩm & Đánh giá theo OOP
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/SanPhamModel.php';
require_once __DIR__ . '/../model/DanhMucModel.php';

class SanPhamController extends BaseController
{
    private $sanPhamModel;
    private $danhMucModel;

    public function __construct()
    {
        parent::__construct();
        $this->sanPhamModel = new SanPhamModel();
        $this->danhMucModel = new DanhMucModel();
    }

    /**
     * Trang chủ: Hiển thị danh sách hoa tươi nổi bật, banner chuyển động giữa các khối hoa và hoa mới
     */
    public function trangChu()
    {
        $all_featured = $this->sanPhamModel->getFeaturedProducts(8);
        $hoa_noibat = array_slice($all_featured, 0, 4);
        $hoa_moive = array_slice($all_featured, 4, 4);
        $danhmuc = $this->danhMucModel->getAll();
        $this->renderView('trangchu.php', [
            'hoa_noibat' => $hoa_noibat,
            'hoa_moive' => $hoa_moive,
            'danhmuc' => $danhmuc
        ]);
    }

    /**
     * Danh sách sản phẩm: Tìm kiếm, lọc theo danh mục, sắp xếp giá & phân trang
     */
    public function danhSach()
    {
        $danhmuc = $this->danhMucModel->getAll();

        $where = ["1=1"];
        $params = [];

        // Tìm kiếm theo từ khóa
        $q = trim($this->get('q', ''));
        if (!empty($q)) {
            $where[] = "sanpham.name LIKE :q";
            $params[':q'] = '%' . $q . '%';
        }

        // Lọc theo danh mục
        $id_dm = (int)$this->get('id_dm', 0);
        if ($id_dm > 0) {
            $where[] = "sanpham.id_dm = :id_dm";
            $params[':id_dm'] = $id_dm;
        }

        // Sắp xếp
        $sort_sql = "ORDER BY sanpham.id DESC";
        $sort = $this->get('sort', '');
        if ($sort === 'price_asc') {
            $sort_sql = "ORDER BY COALESCE(NULLIF(sanpham.sale_price, 0), sanpham.price) ASC";
        } elseif ($sort === 'price_desc') {
            $sort_sql = "ORDER BY COALESCE(NULLIF(sanpham.sale_price, 0), sanpham.price) DESC";
        } elseif ($sort === 'sale') {
            $where[] = "sanpham.is_sale = 1 AND sanpham.sale_price IS NOT NULL";
            $sort_sql = "ORDER BY sanpham.id DESC";
        }

        $where_str = implode(" AND ", $where);

        // Phân trang
        $total_products = $this->sanPhamModel->countProducts($where_str, $params);
        $per_page = 8;
        $total_pages = ceil($total_products / $per_page);
        $current_page = max(1, min($total_pages ?: 1, (int)$this->get('page', 1)));
        $offset = ($current_page - 1) * $per_page;

        $sanpham = $this->sanPhamModel->getProducts($where_str, $params, $sort_sql, $per_page, $offset);

        $this->renderView('dssanpham.php', [
            'sanpham' => $sanpham,
            'danhmuc' => $danhmuc,
            'total_products' => $total_products,
            'total_pages' => $total_pages,
            'current_page' => $current_page
        ]);
    }

    /**
     * Chi tiết sản phẩm kèm sản phẩm liên quan và danh sách đánh giá
     */
    public function chiTiet()
    {
        $id = (int)$this->get('id', 0);
        $sp = $this->sanPhamModel->getProductById($id);

        if (!$sp) {
            $this->redirect('index.php?act=sanpham');
        }

        $danhsach_danhgia = $this->sanPhamModel->getApprovedReviews($id);
        $avg_rating = $this->sanPhamModel->getAverageRating($id);
        $sp_lienquan = $this->sanPhamModel->getRelatedProducts($sp['id_dm'], $id, 3);

        $this->renderView('chitietsp.php', [
            'sp' => $sp,
            'danhsach_danhgia' => $danhsach_danhgia,
            'avg_rating' => $avg_rating,
            'sp_lienquan' => $sp_lienquan
        ]);
    }

    /**
     * Xử lý khách gửi đánh giá & bình luận sản phẩm
     */
    public function guiDanhGia()
    {
        if ($this->isPost() && isset($_POST['guidanhgia'])) {
            $product_id = (int)$this->post('product_id', 0);
            $rating = max(1, min(5, (int)$this->post('rating', 5)));
            $user_name = trim($this->post('user_name', 'Khách hàng'));
            $content = trim($this->post('content', ''));
            $user_id = $_SESSION['id'] ?? null;

            if (!empty($content)) {
                $this->sanPhamModel->addReview($product_id, $user_id, $user_name, $rating, $content);
            }
            $this->redirect("index.php?act=chitiettsp&id=$product_id&msg=danhgia_thanhcong");
        }
        $this->redirect('index.php?act=sanpham');
    }
}
