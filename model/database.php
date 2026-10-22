<?php
class database
{
    private static $initialized = false;

    public function connection_database()
    {
        $host = 'localhost';
        $dbname = 'bloomshop';
        $username = 'root';
        $password = '';

        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (!self::$initialized) {
                $this->init_db_schema($conn);
                self::$initialized = true;
            }

            return $conn;
        } catch (PDOException $e) {
            // Thử tạo DB nếu chưa có
            try {
                $connRoot = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
                $connRoot->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $connRoot->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                
                $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->init_db_schema($conn);
                self::$initialized = true;
                return $conn;
            } catch (PDOException $e2) {
                die('Lỗi kết nối database: ' . $e->getMessage());
            }
        }
    }

    private function init_db_schema($conn)
    {
        try {
            // Kiểm tra xem bảng vouchers đã tồn tại chưa
            $check = $conn->query("SHOW TABLES LIKE 'vouchers'")->fetchAll();
            if (count($check) == 0) {
                $sqlFile = __DIR__ . '/../bloomshop.sql';
                if (file_exists($sqlFile)) {
                    $sql = file_get_contents($sqlFile);
                    $conn->exec($sql);
                }
            } else {
                // Đảm bảo các bảng cũ có đủ cột cần thiết
                $this->add_column_if_not_exists($conn, 'sanpham', 'sale_price', 'DECIMAL(12,2) DEFAULT NULL');
                $this->add_column_if_not_exists($conn, 'sanpham', 'is_sale', 'TINYINT(1) DEFAULT 0');
                $this->add_column_if_not_exists($conn, 'sanpham', 'description', 'TEXT DEFAULT NULL');
                $this->add_column_if_not_exists($conn, 'users', 'status', 'TINYINT(1) DEFAULT 1');
                $this->add_column_if_not_exists($conn, 'users', 'tier', "ENUM('silver', 'gold', 'diamond') DEFAULT 'silver'");
                $this->add_column_if_not_exists($conn, 'users', 'total_spent', 'DECIMAL(12,2) DEFAULT 0');
                $this->add_column_if_not_exists($conn, 'users', 'phone', 'VARCHAR(20) DEFAULT NULL');
                $this->add_column_if_not_exists($conn, 'users', 'address', 'VARCHAR(255) DEFAULT NULL');
                
                // Đảm bảo có bảng tracking, donhang, chitiet_donhang, danhgia nếu thiếu
                $tracking = $conn->query("SHOW TABLES LIKE 'order_tracking'")->fetchAll();
                if (count($tracking) == 0) {
                    $sqlFile = __DIR__ . '/../bloomshop.sql';
                    if (file_exists($sqlFile)) {
                        $sql = file_get_contents($sqlFile);
                        $conn->exec($sql);
                    }
                }
            }
        } catch (Exception $e) {
            // Tránh ngắt trang web nếu kiểm tra migration gặp lỗi nhỏ
        }
    }

    private function add_column_if_not_exists($conn, $table, $column, $definition)
    {
        try {
            $cols = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'")->fetchAll();
            if (count($cols) == 0) {
                $conn->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
            }
        } catch (Exception $e) {
            // Bỏ qua nếu cột đã tồn tại
        }
    }
}
