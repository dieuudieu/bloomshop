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

            // Tự động đảm bảo 5 tài khoản mẫu và 4 mã voucher luôn tồn tại để test hệ thống
            $this->ensure_demo_data($conn);
        } catch (Exception $e) {
            // Tránh ngắt trang web nếu kiểm tra migration gặp lỗi nhỏ
        }
    }

    private function ensure_demo_data($conn)
    {
        try {
            // 1. Danh sách 5 tài khoản mẫu
            $users = [
                [
                    'name' => 'Quản Trị Viên',
                    'email' => 'admin@bloomshop.vn',
                    'password' => '$2y$10$KAxIU4DnpqSNYGo61P9.POnusepGginOJHjlem9Dkjze2Qe.sg9pS', // admin123
                    'role' => 'admin',
                    'tier' => 'diamond',
                    'spent' => 15000000.00,
                    'phone' => '0901234567',
                    'address' => 'Tòa nhà Bloom, Quận 1, TP.HCM'
                ],
                [
                    'name' => 'Nguyễn Văn Nhân Viên',
                    'email' => 'staff@bloomshop.vn',
                    'password' => '$2y$10$1zwom.nZS681rIEeglmFc.PrUkoZt1cZjH5M35pGnFsuAWrFD6Idq', // staff123
                    'role' => 'staff',
                    'tier' => 'silver',
                    'spent' => 0.00,
                    'phone' => '0909888777',
                    'address' => 'BloomShop Chi nhánh 1'
                ],
                [
                    'name' => 'Lê Thị Kim Cương',
                    'email' => 'diamond@bloomshop.vn',
                    'password' => '$2y$10$z1KeoyWrs.dfa2k..ghNHu23LXrIz.g23ts3fIExZyOQoOjY3Atfe', // 123456
                    'role' => 'user',
                    'tier' => 'diamond',
                    'spent' => 6800000.00,
                    'phone' => '0912345678',
                    'address' => '123 Lê Duẩn, Quận 1, TP.HCM'
                ],
                [
                    'name' => 'Trần Hoàng Vàng',
                    'email' => 'gold@bloomshop.vn',
                    'password' => '$2y$10$z1KeoyWrs.dfa2k..ghNHu23LXrIz.g23ts3fIExZyOQoOjY3Atfe', // 123456
                    'role' => 'user',
                    'tier' => 'gold',
                    'spent' => 2900000.00,
                    'phone' => '0987654321',
                    'address' => '456 Nguyễn Thị Minh Khai, Quận 3'
                ],
                [
                    'name' => 'Phạm Ngọc Bạc',
                    'email' => 'silver@bloomshop.vn',
                    'password' => '$2y$10$z1KeoyWrs.dfa2k..ghNHu23LXrIz.g23ts3fIExZyOQoOjY3Atfe', // 123456
                    'role' => 'user',
                    'tier' => 'silver',
                    'spent' => 550000.00,
                    'phone' => '0933221100',
                    'address' => '789 CMT8, Tân Bình, TP.HCM'
                ]
            ];

            foreach ($users as $u) {
                $chk = $conn->prepare("SELECT id FROM users WHERE email = :email");
                $chk->execute([':email' => $u['email']]);
                if ($row = $chk->fetch()) {
                    $up = $conn->prepare("UPDATE users SET password = :pwd, role = :role, status = 1, tier = :tier, name = :name WHERE id = :id");
                    $up->execute([
                        ':pwd' => $u['password'],
                        ':role' => $u['role'],
                        ':tier' => $u['tier'],
                        ':name' => $u['name'],
                        ':id' => $row['id']
                    ]);
                } else {
                    $ins = $conn->prepare("INSERT INTO users (name, email, password, phone, address, role, status, tier, total_spent) VALUES (:name, :email, :pwd, :phone, :address, :role, 1, :tier, :spent)");
                    $ins->execute([
                        ':name' => $u['name'],
                        ':email' => $u['email'],
                        ':pwd' => $u['password'],
                        ':phone' => $u['phone'],
                        ':address' => $u['address'],
                        ':role' => $u['role'],
                        ':tier' => $u['tier'],
                        ':spent' => $u['spent']
                    ]);
                }
            }

            // 2. Danh sách 4 mã voucher mẫu
            $vouchers = [
                ['code' => 'BLOOM10', 'type' => 'percent', 'val' => 10.00, 'min' => 200000.00, 'qty' => 100],
                ['code' => 'GIAM50K', 'type' => 'fixed', 'val' => 50000.00, 'min' => 300000.00, 'qty' => 100],
                ['code' => 'VIPMEMBER', 'type' => 'fixed', 'val' => 100000.00, 'min' => 600000.00, 'qty' => 50],
                ['code' => 'FREESHIP', 'type' => 'fixed', 'val' => 30000.00, 'min' => 150000.00, 'qty' => 200]
            ];

            foreach ($vouchers as $v) {
                $chk = $conn->prepare("SELECT id FROM vouchers WHERE code = :code");
                $chk->execute([':code' => $v['code']]);
                if ($row = $chk->fetch()) {
                    $up = $conn->prepare("UPDATE vouchers SET discount_type = :type, discount_value = :val, min_order_value = :min, status = 1, expiry_date = '2026-12-31' WHERE id = :id");
                    $up->execute([
                        ':type' => $v['type'],
                        ':val' => $v['val'],
                        ':min' => $v['min'],
                        ':id' => $row['id']
                    ]);
                } else {
                    $ins = $conn->prepare("INSERT INTO vouchers (code, discount_type, discount_value, min_order_value, quantity, used_count, expiry_date, status) VALUES (:code, :type, :val, :min, :qty, 0, '2026-12-31', 1)");
                    $ins->execute([
                        ':code' => $v['code'],
                        ':type' => $v['type'],
                        ':val' => $v['val'],
                        ':min' => $v['min'],
                        ':qty' => $v['qty']
                    ]);
                }
            }
        } catch (Exception $e) {
            // Bỏ qua lỗi nhỏ
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
