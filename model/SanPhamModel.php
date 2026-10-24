<?php
// model/SanPhamModel.php - Quản lý dữ liệu Sản phẩm, Đánh giá & Danh mục theo OOP
require_once __DIR__ . '/BaseModel.php';

class SanPhamModel extends BaseModel
{
    /**
     * Lấy danh sách sản phẩm nổi bật hiển thị trang chủ
     */
    public function getFeaturedProducts($limit = 8)
    {
        $sql = "SELECT * FROM sanpham ORDER BY is_sale DESC, id DESC LIMIT " . (int)$limit;
        return $this->readitem($sql);
    }

    /**
     * Lấy danh sách sản phẩm có lọc, tìm kiếm, sắp xếp và phân trang
     */
    public function getProducts($where_str, $params, $sort_sql, $limit, $offset)
    {
        $sql = "SELECT sanpham.*, danhmuc.name AS tendanhmuc
                FROM sanpham 
                LEFT JOIN danhmuc ON sanpham.id_dm = danhmuc.id
                WHERE $where_str
                $sort_sql
                LIMIT $limit OFFSET $offset";
        return $this->readitem($sql, $params);
    }

    /**
     * Đếm tổng số lượng sản phẩm theo điều kiện lọc
     */
    public function countProducts($where_str, $params)
    {
        $sql = "SELECT COUNT(*) AS total FROM sanpham WHERE $where_str";
        $row = $this->readsingle($sql, $params);
        return $row ? (int)$row['total'] : 0;
    }

    /**
     * Lấy chi tiết một sản phẩm kèm tên danh mục
     */
    public function getProductById($id)
    {
        $sql = "SELECT sanpham.*, danhmuc.name AS tendanhmuc 
                FROM sanpham 
                LEFT JOIN danhmuc ON sanpham.id_dm = danhmuc.id 
                WHERE sanpham.id = :id";
        return $this->readsingle($sql, [':id' => (int)$id]);
    }

    /**
     * Lấy danh sách sản phẩm liên quan cùng danh mục
     */
    public function getRelatedProducts($id_dm, $exclude_id, $limit = 3)
    {
        $sql = "SELECT * FROM sanpham WHERE id_dm = :dm AND id != :id LIMIT " . (int)$limit;
        return $this->readitem($sql, [
            ':dm' => $id_dm,
            ':id' => $exclude_id
        ]);
    }

    /**
     * Lấy danh sách đánh giá đã duyệt của sản phẩm
     */
    public function getApprovedReviews($product_id)
    {
        $sql = "SELECT * FROM danhgia WHERE product_id = :pid AND status = 1 ORDER BY id DESC";
        return $this->readitem($sql, [':pid' => (int)$product_id]);
    }

    /**
     * Tính điểm đánh giá trung bình
     */
    public function getAverageRating($product_id)
    {
        $sql = "SELECT AVG(rating) AS avg_rate FROM danhgia WHERE product_id = :pid AND status = 1";
        $row = $this->readsingle($sql, [':pid' => (int)$product_id]);
        return $row ? (float)$row['avg_rate'] : 5.0;
    }

    /**
     * Thêm đánh giá mới cho sản phẩm
     */
    public function addReview($product_id, $user_id, $user_name, $rating, $content)
    {
        $sql = "INSERT INTO danhgia (product_id, user_id, user_name, rating, content, status, created_at)
                VALUES (:pid, :uid, :uname, :rating, :content, 1, NOW())";
        return $this->execute_item($sql, [
            ':pid' => (int)$product_id,
            ':uid' => $user_id,
            ':uname' => $user_name,
            ':rating' => (int)$rating,
            ':content' => $content
        ]);
    }

    /**
     * Lấy tất cả sản phẩm cho trang quản trị (Admin)
     */
    public function getAllProductsAdmin()
    {
        $sql = "SELECT sanpham.*, danhmuc.name AS tendanhmuc,
                       (SELECT COUNT(*) FROM chitiet_donhang WHERE chitiet_donhang.product_id = sanpham.id) AS total_orders
                FROM sanpham 
                JOIN danhmuc ON sanpham.id_dm = danhmuc.id 
                ORDER BY sanpham.id DESC";
        return $this->readitem($sql);
    }

    /**
     * Lấy danh sách sản phẩm cho trang quản lý khuyến mãi
     */
    public function getAllProductsWithSale()
    {
        $sql = "SELECT sanpham.*, danhmuc.name AS tendanhmuc
                FROM sanpham LEFT JOIN danhmuc ON sanpham.id_dm = danhmuc.id ORDER BY sanpham.is_sale DESC, sanpham.id DESC";
        return $this->readitem($sql);
    }

    /**
     * Thêm mới sản phẩm (Admin)
     */
    public function createProduct($name, $price, $sale_price, $is_sale, $description, $image, $id_dm)
    {
        $sql = "INSERT INTO sanpham (name, price, sale_price, is_sale, description, image, id_dm)
                VALUES (:name, :price, :sale_price, :is_sale, :description, :image, :id_dm)";
        $res = $this->execute_item($sql, [
            ':name' => $name,
            ':price' => $price,
            ':sale_price' => $sale_price,
            ':is_sale' => $is_sale,
            ':description' => $description,
            ':image' => $image,
            ':id_dm' => $id_dm
        ]);
        $this->exportSqlDump();
        return $res;
    }

    /**
     * Cập nhật thông tin sản phẩm (Admin)
     */
    public function updateProduct($id, $name, $price, $sale_price, $is_sale, $description, $id_dm, $image = null)
    {
        if ($image !== null) {
            // Xóa file ảnh cũ nếu có
            $sp = $this->getProductById($id);
            if ($sp && !empty($sp['image']) && $sp['image'] !== $image) {
                $oldPath = __DIR__ . "/../view/image/sanpham/" . $sp['image'];
                if (file_exists($oldPath) && is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $sql = "UPDATE sanpham SET name=:name, price=:price, sale_price=:sale_price, is_sale=:is_sale, description=:description, image=:image, id_dm=:id_dm WHERE id=:id";
            $res = $this->execute_item($sql, [
                ':name' => $name,
                ':price' => $price,
                ':sale_price' => $sale_price,
                ':is_sale' => $is_sale,
                ':description' => $description,
                ':image' => $image,
                ':id_dm' => $id_dm,
                ':id' => $id
            ]);
        } else {
            $sql = "UPDATE sanpham SET name=:name, price=:price, sale_price=:sale_price, is_sale=:is_sale, description=:description, id_dm=:id_dm WHERE id=:id";
            $res = $this->execute_item($sql, [
                ':name' => $name,
                ':price' => $price,
                ':sale_price' => $sale_price,
                ':is_sale' => $is_sale,
                ':description' => $description,
                ':id_dm' => $id_dm,
                ':id' => $id
            ]);
        }
        $this->exportSqlDump();
        return $res;
    }

    /**
     * Xóa sản phẩm theo ID (Admin) kèm ràng buộc toàn vẹn dữ liệu
     * KHÔNG ĐƯỢC XÓA nếu sản phẩm đã phát sinh trong bất kỳ đơn hàng / hóa đơn nào của khách
     */
    public function deleteProduct($id)
    {
        $id = (int)$id;
        $sp = $this->getProductById($id);
        if (!$sp) {
            throw new Exception("Sản phẩm không tồn tại trên hệ thống!");
        }

        // 1. Kiểm tra ràng buộc toàn vẹn dữ liệu: Có trong đơn hàng / hóa đơn nào chưa
        $check = $this->readsingle("SELECT COUNT(*) AS total FROM chitiet_donhang WHERE product_id = :id", [':id' => $id]);
        $orderCount = $check ? (int)$check['total'] : 0;
        if ($orderCount > 0) {
            throw new Exception("Ràng buộc toàn vẹn dữ liệu: Không thể xóa sản phẩm '{$sp['name']}' vì đã phát sinh trong {$orderCount} đơn hàng/hóa đơn của khách hàng! Quy định thương mại điện tử không cho phép xóa sản phẩm đã có giao dịch để bảo toàn lịch sử hóa đơn.");
        }

        // 2. Xóa các đánh giá thuộc về sản phẩm này (nếu chưa có đơn hàng nào)
        $this->execute_item("DELETE FROM danhgia WHERE product_id = :id", [':id' => $id]);

        // 3. Xóa file ảnh trên ổ đĩa hosting (tránh rác bộ nhớ)
        if (!empty($sp['image'])) {
            $imgPath = __DIR__ . "/../view/image/sanpham/" . $sp['image'];
            if (file_exists($imgPath) && is_file($imgPath)) {
                @unlink($imgPath);
            }
        }

        // 4. Xóa sản phẩm khỏi bảng sanpham
        $res = $this->execute_item("DELETE FROM sanpham WHERE id = :id", [':id' => $id]);
        $this->exportSqlDump();
        return $res;
    }

    /**
     * Cập nhật thông tin khuyến mãi cho sản phẩm
     */
    public function updateSale($id, $sale_price, $is_sale)
    {
        $sql = "UPDATE sanpham SET sale_price = :sale, is_sale = :is_sale WHERE id = :id";
        $res = $this->execute_item($sql, [
            ':sale' => $sale_price,
            ':is_sale' => $is_sale,
            ':id' => (int)$id
        ]);
        $this->exportSqlDump();
        return $res;
    }

    /**
     * Quản lý đánh giá (Admin)
     */
    public function getAllReviewsAdmin($star = null)
    {
        $where = "1=1";
        $params = [];
        if (!empty($star)) {
            $where .= " AND danhgia.rating = :star";
            $params[':star'] = (int)$star;
        }
        $sql = "SELECT danhgia.*, sanpham.name AS product_name, sanpham.image AS product_image
                FROM danhgia 
                JOIN sanpham ON danhgia.product_id = sanpham.id
                WHERE $where
                ORDER BY danhgia.id DESC";
        return $this->readitem($sql, $params);
    }

    public function updateReviewStatus($id, $status)
    {
        return $this->execute_item("UPDATE danhgia SET status = :st WHERE id = :id", [
            ':st' => (int)$status,
            ':id' => (int)$id
        ]);
    }

    public function deleteReview($id)
    {
        return $this->execute_item("DELETE FROM danhgia WHERE id = :id", [':id' => (int)$id]);
    }
}
