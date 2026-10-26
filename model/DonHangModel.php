<?php
// model/DonHangModel.php - Quản lý Đơn hàng, Chi tiết đơn hàng & Tiến trình giao hàng theo OOP
require_once __DIR__ . '/BaseModel.php';

class DonHangModel extends BaseModel
{
    /** Giao dịch đặt hàng: khóa tồn kho, lưu đơn, trừ kho và voucher một lần. */
    public function placeOrderSafely($data, $cart_items, $voucher_id = null)
    {
        $conn = $this->get_conn();
        $conn->beginTransaction();
        try {
            foreach ($cart_items as $item) {
                $id = (int)$item['id'];
                $qty = (int)$item['qty'];
                if ($qty <= 0) throw new RuntimeException('Số lượng đặt không hợp lệ.');
                $q = $conn->prepare('SELECT so_luong FROM sanpham WHERE id = ? FOR UPDATE');
                $q->execute([$id]);
                $stock = $q->fetchColumn();
                if ($stock === false || (int)$stock < $qty) {
                    throw new RuntimeException('Sản phẩm #' . $id . ' không đủ tồn kho (còn ' . (int)$stock . ').');
                }
            }
            if ($voucher_id) {
                $q = $conn->prepare('UPDATE vouchers SET used_count = used_count + 1 WHERE id = ? AND used_count < quantity AND status = 1 AND expiry_date >= CURDATE()');
                $q->execute([(int)$voucher_id]);
                if ($q->rowCount() !== 1) throw new RuntimeException('Voucher đã hết lượt hoặc không còn hiệu lực.');
            }
            $id = $this->createOrder($data);
            $saved = $this->createOrderItems($id, $cart_items);
            foreach ($cart_items as $item) {
                $q = $conn->prepare('UPDATE sanpham SET so_luong = so_luong - ? WHERE id = ? AND so_luong >= ?');
                $q->execute([(int)$item['qty'], (int)$item['id'], (int)$item['qty']]);
                if ($q->rowCount() !== 1) throw new RuntimeException('Tồn kho vừa thay đổi, vui lòng thử lại.');
            }
            $this->execute_item('UPDATE donhang SET stock_deducted=1 WHERE id=:id', [':id'=>$id]);
            $this->addTracking($id, 'cho_xac_nhan', 'Đơn hàng mới được tạo, đã trừ tồn kho.');
            $conn->commit();
            return [$id, $saved];
        } catch (Throwable $e) {
            if ($conn->inTransaction()) $conn->rollBack();
            throw $e;
        }
    }

    /** Đổi trạng thái có khóa dòng, hoàn kho và voucher đúng một lần. */
    public function changeStatusSafely($id, $expected, $next, $description = '')
    {
        $conn = $this->get_conn();
        $conn->beginTransaction();
        try {
            $q = $conn->prepare('SELECT * FROM donhang WHERE id=? FOR UPDATE');
            $q->execute([(int)$id]);
            $order = $q->fetch(PDO::FETCH_ASSOC);
            if (!$order || $order['status'] !== $expected) throw new RuntimeException('Trạng thái đơn đã thay đổi, vui lòng tải lại.');
            $valid = [
                'cho_xac_nhan'=>['xac_nhan','da_huy'],
                'xac_nhan'=>['dang_giao','da_huy'],
                'dang_giao'=>['da_giao','da_huy'],
                'da_giao'=>['thanh_cong','yeu_cau_tra'],
                'thanh_cong'=>['yeu_cau_tra'],
                'yeu_cau_tra'=>['da_tra_hang','tu_choi_tra'],
                'tu_choi_tra'=>['yeu_cau_tra']
            ];
            if (!in_array($next, $valid[$expected] ?? [], true)) throw new RuntimeException('Chuyển trạng thái không hợp lệ.');
            if (in_array($next, ['da_huy','da_tra_hang'], true)) {
                if ((int)$order['stock_deducted'] === 1 && (int)$order['inventory_restored'] === 0) {
                    $items = $this->getOrderItems($id);
                    foreach ($items as $item) {
                        $this->execute_item('UPDATE sanpham SET so_luong=so_luong+:qty WHERE id=:id', [':qty'=>(int)$item['quantity'], ':id'=>(int)$item['product_id']]);
                    }
                    $this->execute_item('UPDATE donhang SET inventory_restored=1 WHERE id=:id', [':id'=>$id]);
                }
                if (!empty($order['voucher_code']) && (int)$order['voucher_restored'] === 0) {
                    $this->execute_item('UPDATE vouchers SET used_count=GREATEST(0,used_count-1) WHERE code=:code', [':code'=>$order['voucher_code']]);
                    $this->execute_item('UPDATE donhang SET voucher_restored=1 WHERE id=:id', [':id'=>$id]);
                }
            }
            $this->execute_item('UPDATE donhang SET status=:status WHERE id=:id', [':status'=>$next, ':id'=>$id]);
            if ($description !== '') $this->addTracking($id, $next, $description);
            $conn->commit();
        } catch (Throwable $e) {
            if ($conn->inTransaction()) $conn->rollBack();
            throw $e;
        }
    }

    public function requestReturn($id, $user_id, $reason)
    {
        $conn = $this->get_conn();
        $conn->beginTransaction();
        try {
            $q = $conn->prepare("UPDATE donhang SET status='yeu_cau_tra', return_reason=:reason, return_requested_at=NOW() WHERE id=:id AND user_id=:uid AND status IN ('da_giao','thanh_cong','tu_choi_tra')");
            $q->execute([':reason'=>$reason, ':id'=>(int)$id, ':uid'=>(int)$user_id]);
            if ($q->rowCount() !== 1) throw new RuntimeException('Đơn hàng không đủ điều kiện yêu cầu trả.');
            $this->addTracking($id,'yeu_cau_tra','Khách yêu cầu trả hàng: '.$reason);
            $conn->commit();
        } catch (Throwable $e) {
            if ($conn->inTransaction()) $conn->rollBack();
            throw $e;
        }
    }

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
                'product_image' => $it['image'],
                'quantity' => (int)$it['qty'],
                'price' => (float)$it['price'],
                'total' => $item_total
            ];
        }
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
