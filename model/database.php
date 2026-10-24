<?php
// model/database.php - Kết nối CSDL thông minh hỗ trợ cả Localhost và Hosting
class database
{
    private static $initialized = false;
    private static $conn_instance = null;

    public function connection_database()
    {
        if (self::$conn_instance !== null) {
            return self::$conn_instance;
        }

        // Đọc cấu hình từ config.php
        $configFile = __DIR__ . '/config.php';
        $config = file_exists($configFile) ? require $configFile : [];

        // Nhận diện môi trường đang chạy
        $httpHost = $_SERVER['HTTP_HOST'] ?? '';
        $serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
        $isLocal = (
            php_sapi_name() === 'cli' ||
            strpos($httpHost, 'localhost') !== false ||
            strpos($httpHost, '127.0.0.1') !== false ||
            $serverAddr === '127.0.0.1' ||
            $serverAddr === '::1'
        );

        // Lựa chọn cấu hình Local hay Hosting
        if ($isLocal || empty($config['hosting']['dbname']) || ($config['hosting']['username'] === 'root' && empty($config['hosting']['password']))) {
            $env = $config['local'] ?? [
                'host' => 'localhost',
                'dbname' => 'bloomshop',
                'username' => 'root',
                'password' => '',
                'port' => 3306
            ];
        } else {
            $env = $config['hosting'];
        }

        // Hỗ trợ cả biến môi trường hệ thống (Environment Variables)
        $host = getenv('DB_HOST') ?: ($env['host'] ?? 'localhost');
        $dbname = getenv('DB_NAME') ?: ($env['dbname'] ?? 'bloomshop');
        $username = getenv('DB_USER') ?: ($env['username'] ?? 'root');
        $password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($env['password'] ?? '');
        $port = getenv('DB_PORT') ?: ($env['port'] ?? 3306);

        $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8mb4";

        try {
            $conn = new PDO($dsn, $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (!self::$initialized) {
                $this->init_db_schema($conn);
                self::$initialized = true;
            }

            self::$conn_instance = $conn;
            return self::$conn_instance;
        } catch (PDOException $e) {
            // Thử tạo DB nếu chưa có trên localhost
            if ($isLocal) {
                try {
                    $connRoot = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password);
                    $connRoot->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $connRoot->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

                    $conn = new PDO($dsn, $username, $password);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->init_db_schema($conn);
                    self::$initialized = true;
                    self::$conn_instance = $conn;
                    return self::$conn_instance;
                } catch (PDOException $e2) {
                    die('Lỗi kết nối cơ sở dữ liệu: ' . $e->getMessage());
                }
            } else {
                die('Lỗi kết nối cơ sở dữ liệu trên Hosting: ' . $e->getMessage() . '<br>Vui lòng kiểm tra lại thông tin cấu hình trong file <b>model/config.php</b>.');
            }
        }
    }

    private function init_db_schema($conn)
    {
        try {
            // Kiểm tra xem bảng sản phẩm đã tồn tại chưa
            $check = $conn->query("SHOW TABLES LIKE 'sanpham'")->fetchAll();
            if (count($check) == 0) {
                $sqlFile = __DIR__ . '/../bloomshop.sql';
                if (file_exists($sqlFile)) {
                    $sql = file_get_contents($sqlFile);
                    // Lọc bỏ các lệnh CREATE DATABASE và USE để tương thích tuyệt đối với Hosting phpMyAdmin
                    $sql = preg_replace('/CREATE\s+DATABASE[^\;]+;/i', '', $sql);
                    $sql = preg_replace('/USE\s+[`\w]+;/i', '', $sql);
                    $conn->exec($sql);
                }
            }
        } catch (Exception $e) {
            // Bỏ qua lỗi nhỏ
        }
    }
}
