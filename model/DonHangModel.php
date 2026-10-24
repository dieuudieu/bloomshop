<?php
// model/DonHangModel.php - Quản lý Đơn hàng, Chi tiết đơn hàng & Tiến trình giao hàng theo OOP
require_once __DIR__ . '/BaseModel.php';

class DonHangModel extends BaseModel
{
    /**
     * Tạo đơn hàng mới trong CSDL
     */
    public function createOrder($data)
    {
        $sql = "INSERT INTO donhang (user_id, customer_name, customer_email, customer_phone, customer_address, note, subtotal, voucher_code, discount_amount, total_price, payment_method, status, created_at)
                VALUES (:user_id, :name, :email, :phone, :address, :note, :subtotal, :voucher, :discount, :total, :payment, 'cho_xac_nhan', NOW())";
        
        return $this->execute_get_id($sql, [
            ':user_id' => $data['user_id'] ?? null,
            ':name' => $data['name'],
            ':email' => $data['email'] ?? '',
            ':phone' => $data['phone'],
            ':address' => $data['address'],
            ':note' => $data['note'] ?? '',
            ':subtotal' => (float)$data['subtotal'],
            ':voucher' => $data['voucher'] ?? null,
            ':discount' => (float)($data['discount'] ?? 0),
            ':total' => (float)$data['total'],
            ':payment' => $data['payment'] ?? 'COD'
        ]);
    }

    /**
     * Lưu danh sách sản phẩm trong giỏ hàng vào bảng chi tiết đơn hàng
     */
    public function createOrderItems($order_id, $cart_items)
    {
        $saved_items = [];
        foreach ($cart_items as $it) {
            $item_total = (float)$it['price'] * (int)$it['qty'];
            $sql = "INSERT INTO chitiet_donhang (order_id, product_id, product_name, product_image, price, quantity, total)
                    VALUES (:order_id, :product_id, :product_name, :product_image, :price, :quantity, :total)";
            $this->execute_item($sql, [
                ':order_id' => (int)$order_id,
                ':product_id' => (int)$it['id'],
                ':product_name' => $it['name'],
                ':product_image' => $it['image'],
                ':price' => (float)$it['price'],
                ':quantity' => (int)$it['qty'],
                ':total' => $item_total
            ]);

            $saved_items[] = [
                'product_name' => $it['name'],
                'quantity' => (int)$it['qty'],
                'price' => (float)$it['price'],
                'total' => $item_total
            ];
        }
        $this->exportSqlDump();
        return $saved_items;
    }

    /**
     * Thêm bản ghi tiến trình đơn hàng (Tracking timeline)
     */
    public function addTracking($order_id, $status, $description)
    {
        $sql = "INSERT INTO order_tracking (order_id, status, description, created_at)
                VALUES (:order_id, :status, :desc, NOW())";
        return $this->execute_item($sql, [
            ':order_id' => (int)$order_id,
            ':status' => $status,
            ':desc' => $description
        ]);
    }

    /**
     * Lấy thông tin đơn hàng theo ID
     */
    public function getOrderById($id)
    {
        return $this->readsingle("SELECT * FROM donhang WHERE id = :id", [':id' => (int)$id]);
    }

    /**
     * Lấy chi tiết các sản phẩm trong đơn hàng
     */
    public function getOrderItems($order_id)
    {
        return $this->readitem("SELECT * FROM chitiet_donhang WHERE order_id = :id", [':id' => (int)$order_id]);
    }

    /**
     * Lấy lịch sử đơn hàng của khách hàng theo user_id
     */
    public function getOrdersByUser($user_id)
    {
        return $this->readitem("SELECT * FROM donhang WHERE user_id = :uid ORDER BY id DESC", [':uid' => (int)$user_id]);
    }

    /**
     * Lấy lịch sử tracking giao hàng
     */
    public function getTrackingLogs($order_id)
    {
        return $this->readitem("SELECT * FROM order_tracking WHERE order_id = :id ORDER BY created_at ASC", [':id' => (int)$order_id]);
    }

    /**
     * Tra cứu đơn hàng nhanh bằng mã đơn và số điện thoại
     */
    public function lookupOrder($order_id, $phone)
    {
        $sql = "SELECT * FROM donhang WHERE id = :id AND customer_phone = :phone";
        return $this->readsingle($sql, [
            ':id' => (int)$order_id,
            ':phone' => trim($phone)
        ]);
    }

    /**
     * Cập nhật trạng thái đơn hàng và ghi nhật ký tracking
     */
    public function updateOrderStatus($order_id, $new_status, $desc = '')
    {
        $this->execute_item("UPDATE donhang SET status = :st WHERE id = :id", [
            ':st' => $new_status,
            ':id' => (int)$order_id
        ]);

        if (!empty($desc)) {
            $this->addTracking($order_id, $new_status, $desc);
        }
        $this->exportSqlDump();
    }

    /**
     * Lấy danh sách đơn hàng cho trang quản trị (Admin/Staff)
     */
    public function getAllOrders($status = '')
    {
        $where = "1=1";
        $params = [];
        if (!empty($status)) {
            $where .= " AND status = :status";
            $params[':status'] = $status;
        }
        return $this->readitem("SELECT * FROM donhang WHERE $where ORDER BY id DESC", $params);
    }
}
