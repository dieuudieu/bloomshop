<?php
// model/BaseModel.php - Lớp Model cơ sở trong kiến trúc OOP MVC
require_once __DIR__ . '/database.php';

class BaseModel extends database
{
    private static $cached_conn = null;

    public function __construct()
    {
        // Khởi tạo kết nối PDO thông qua lớp cơ sở database
    }

    /**
     * Tái sử dụng kết nối PDO trong cùng một lượt tải trang để tối ưu hiệu năng
     */
    protected function get_conn()
    {
        if (self::$cached_conn === null) {
            self::$cached_conn = $this->connection_database();
        }
        return self::$cached_conn;
    }

    /**
     * Thực thi truy vấn SELECT lấy danh sách nhiều dòng (Mảng các bản ghi)
     */
    public function queryAll($sql, $params = [])
    {
        $conn = $this->get_conn();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Alias tương thích ngược
    public function readitem($sql, $params = [])
    {
        return $this->queryAll($sql, $params);
    }

    /**
     * Thực thi truy vấn SELECT lấy một dòng duy nhất (1 bản ghi)
     */
    public function queryOne($sql, $params = [])
    {
        $conn = $this->get_conn();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : null;
    }

    // Alias tương thích ngược
    public function readsingle($sql, $params = [])
    {
        return $this->queryOne($sql, $params);
    }

    /**
     * Thực thi câu lệnh INSERT, UPDATE, DELETE (Trả về true/false)
     */
    public function execute($sql, $params = [])
    {
        $conn = $this->get_conn();
        $stmt = $conn->prepare($sql);
        return $stmt->execute($params);
    }

    // Alias tương thích ngược
    public function execute_item($sql, $params = [])
    {
        return $this->execute($sql, $params);
    }

    /**
     * Thực thi lệnh INSERT và trả về ID tự tăng vừa được tạo
     */
    public function insertGetId($sql, $params = [])
    {
        $conn = $this->get_conn();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $conn->lastInsertId();
    }

    // Alias tương thích ngược
    public function execute_get_id($sql, $params = [])
    {
        return $this->insertGetId($sql, $params);
    }

    /**
     * Tự động tính toán lại tổng tiền chi tiêu và phân hạng khách hàng (Silver, Gold, Diamond)
     */
    public function recalculate_user_tier($user_id)
    {
        if (!$user_id) return;
        
        $sql = "SELECT COALESCE(SUM(total_price), 0) AS total 
                FROM donhang 
                WHERE user_id = :uid AND status = 'thanh_cong'";
        $row = $this->queryOne($sql, [':uid' => $user_id]);
        $total = $row ? (float)$row['total'] : 0;

        $tier = 'silver';
        if ($total >= 5000000) {
            $tier = 'diamond';
        } elseif ($total >= 1000000) {
            $tier = 'gold';
        }

        $upSql = "UPDATE users SET total_spent = :spent, tier = :tier WHERE id = :uid";
        $this->execute($upSql, [
            ':spent' => $total,
            ':tier' => $tier,
            ':uid' => $user_id
        ]);
    }

    /**
     * Tự động xuất toàn bộ cấu trúc và dữ liệu mới nhất ra file bloomshop.sql
     * Đảm bảo dữ liệu luôn sẵn sàng để đồng bộ hoặc import lên Hosting
     */
    public function exportSqlDump($filePath = null)
    {
        if ($filePath === null) {
            $filePath = __DIR__ . '/../bloomshop.sql';
        }

        try {
            $conn = $this->get_conn();
            $tables = ['danhmuc', 'users', 'sanpham', 'vouchers', 'donhang', 'chitiet_donhang', 'order_tracking', 'danhgia'];

            $sql = "-- ==============================================================\n";
            $sql .= "-- BLOOMSHOP DATABASE DUMP - TỰ ĐỘNG ĐỒNG BỘ CHO HOSTING\n";
            $sql .= "-- Cập nhật mới nhất lúc: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Tương thích 100% với phpMyAdmin, cPanel, InfinityFree, XAMPP\n";
            $sql .= "-- ==============================================================\n\n";
            $sql .= "SET NAMES utf8mb4;\n";
            $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

            foreach ($tables as $table) {
                $chk = $conn->query("SHOW TABLES LIKE '$table'")->fetchAll();
                if (count($chk) === 0) continue;

                // 1. Tạo cấu trúc bảng
                $createTable = $conn->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
                $createSql = $createTable['Create Table'] ?? '';
                $sql .= "-- --------------------------------------------------------\n";
                $sql .= "-- Cấu trúc bảng `$table`\n";
                $sql .= "-- --------------------------------------------------------\n";
                $sql .= "DROP TABLE IF EXISTS `$table`;\n";
                $sql .= $createSql . ";\n\n";

                // 2. Nạp dữ liệu của bảng
                $rows = $conn->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    $sql .= "-- Dữ liệu bảng `$table`\n";
                    $columns = array_keys($rows[0]);
                    $colsEscaped = array_map(function($c) { return "`$c`"; }, $columns);
                    $sql .= "INSERT INTO `$table` (" . implode(", ", $colsEscaped) . ") VALUES\n";

                    $rowStrings = [];
                    foreach ($rows as $r) {
                        $vals = [];
                        foreach ($r as $val) {
                            if ($val === null) {
                                $vals[] = 'NULL';
                            } else {
                                $vals[] = $conn->quote($val);
                            }
                        }
                        $rowStrings[] = "(" . implode(", ", $vals) . ")";
                    }
                    $sql .= implode(",\n", $rowStrings) . ";\n\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
            @file_put_contents($filePath, $sql);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
