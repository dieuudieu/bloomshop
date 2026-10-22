<?php
require_once __DIR__ . '/database.php';

class xl_data extends database
{
    public function readitem($sql, $params = [])
    {
        $conn = $this->connection_database();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readsingle($sql, $params = [])
    {
        $conn = $this->connection_database();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : null;
    }

    public function execute_item($sql, $params = [])
    {
        $conn = $this->connection_database();
        $stmt = $conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function execute_get_id($sql, $params = [])
    {
        $conn = $this->connection_database();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $conn->lastInsertId();
    }

    // Cập nhật lại tổng tiền đã mua và hạng thành viên (Silver, Gold, Diamond)
    public function recalculate_user_tier($user_id)
    {
        if (!$user_id) return;
        
        $sql = "SELECT COALESCE(SUM(total_price), 0) AS total 
                FROM donhang 
                WHERE user_id = :uid AND status = 'thanh_cong'";
        $row = $this->readsingle($sql, [':uid' => $user_id]);
        $total = $row ? (float)$row['total'] : 0;

        $tier = 'silver';
        if ($total >= 5000000) {
            $tier = 'diamond';
        } elseif ($total >= 1000000) {
            $tier = 'gold';
        }

        $upSql = "UPDATE users SET total_spent = :spent, tier = :tier WHERE id = :uid";
        $this->execute_item($upSql, [
            ':spent' => $total,
            ':tier' => $tier,
            ':uid' => $user_id
        ]);
    }
}
