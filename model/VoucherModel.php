<?php
// model/VoucherModel.php - Quản lý Voucher & Mã giảm giá theo OOP
require_once __DIR__ . '/BaseModel.php';

class VoucherModel extends BaseModel
{
    /**
     * Lấy danh sách voucher còn hạn và còn lượt dùng
     */
    public function getAvailableVouchers()
    {
        $sql = "SELECT * FROM vouchers WHERE status = 1 AND expiry_date >= CURDATE() AND used_count < quantity";
        return $this->readitem($sql);
    }

    /**
     * Tìm voucher theo mã code
     */
    public function findByCode($code)
    {
        $sql = "SELECT * FROM vouchers WHERE code = :code AND status = 1";
        return $this->readsingle($sql, [':code' => strtoupper(trim($code))]);
    }

    /**
     * Lấy voucher theo ID
     */
    public function getById($id)
    {
        return $this->readsingle("SELECT * FROM vouchers WHERE id = :id", [':id' => (int)$id]);
    }

    /**
     * Tăng số lượt đã dùng của voucher
     */
    public function incrementUsedCount($id)
    {
        $sql = "UPDATE vouchers SET used_count = used_count + 1 WHERE id = :id";
        $res = $this->execute_item($sql, [':id' => (int)$id]);
        $this->exportSqlDump();
        return $res;
    }

    /**
     * Hoàn lại số lượt đã dùng của voucher khi hủy đơn
     */
    public function decrementUsedCount($code)
    {
        $sql = "UPDATE vouchers SET used_count = GREATEST(0, used_count - 1) WHERE code = :code";
        $res = $this->execute_item($sql, [':code' => $code]);
        $this->exportSqlDump();
        return $res;
    }

    /**
     * Lấy toàn bộ danh sách voucher (Admin)
     */
    public function getAllVouchers()
    {
        return $this->readitem("SELECT * FROM vouchers ORDER BY id DESC");
    }

    /**
     * Thêm voucher mới (Admin)
     */
    public function createVoucher($code, $discount_type, $discount_value, $min_order_value, $quantity, $expiry_date)
    {
        $sql = "INSERT INTO vouchers (code, discount_type, discount_value, min_order_value, quantity, used_count, expiry_date, status)
                VALUES (:code, :dtype, :dval, :minval, :qty, 0, :exp, 1)";
        $res = $this->execute_item($sql, [
            ':code' => strtoupper(trim($code)),
            ':dtype' => $discount_type,
            ':dval' => (float)$discount_value,
            ':minval' => (float)$min_order_value,
            ':qty' => (int)$quantity,
            ':exp' => $expiry_date
        ]);
        $this->exportSqlDump();
        return $res;
    }

    /**
     * Cập nhật voucher (Admin)
     */
    public function updateVoucher($id, $code, $discount_type, $discount_value, $min_order_value, $quantity, $expiry_date, $status)
    {
        $sql = "UPDATE vouchers SET code = :code, discount_type = :dtype, discount_value = :dval, min_order_value = :minval,
                       quantity = :qty, expiry_date = :exp, status = :status WHERE id = :id";
        $res = $this->execute_item($sql, [
            ':code' => strtoupper(trim($code)),
            ':dtype' => $discount_type,
            ':dval' => (float)$discount_value,
            ':minval' => (float)$min_order_value,
            ':qty' => (int)$quantity,
            ':exp' => $expiry_date,
            ':status' => (int)$status,
            ':id' => (int)$id
        ]);
        $this->exportSqlDump();
        return $res;
    }

    /**
     * Xóa voucher (Admin) kèm ràng buộc toàn vẹn dữ liệu
     * KHÔNG ĐƯỢC XÓA nếu mã voucher đã được sử dụng trong bất kỳ đơn hàng nào
     */
    public function deleteVoucher($id)
    {
        $id = (int)$id;
        $vc = $this->getById($id);
        if (!$vc) {
            throw new Exception("Mã voucher không tồn tại trên hệ thống!");
        }

        // Kiểm tra xem voucher đã từng phát sinh trong đơn hàng nào chưa
        $check = $this->readsingle("SELECT COUNT(*) AS total FROM donhang WHERE voucher_code = :code", [':code' => $vc['code']]);
        $orderCount = $check ? (int)$check['total'] : 0;

        if ($vc['used_count'] > 0 || $orderCount > 0) {
            throw new Exception("Ràng buộc toàn vẹn dữ liệu: Không thể xóa mã voucher '{$vc['code']}' vì đã được áp dụng trong " . max($vc['used_count'], $orderCount) . " đơn hàng/hóa đơn! Để ngừng áp dụng, bạn có thể chỉnh sửa trạng thái voucher sang 'Khóa'.");
        }

        $res = $this->execute_item("DELETE FROM vouchers WHERE id = :id", [':id' => $id]);
        $this->exportSqlDump();
        return $res;
    }
}
