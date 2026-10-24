<?php
// model/config.php - Cấu hình Cơ sở dữ liệu đa môi trường (Localhost & Hosting)
return [
    // Tự động nhận diện môi trường đang chạy
    'auto_detect' => true,

    // 1. Cấu hình chạy trên máy cục bộ (Localhost / XAMPP)
    'local' => [
        'host'     => 'localhost',
        'dbname'   => 'bloomshop',
        'username' => 'root',
        'password' => '',
        'port'     => 3306,
    ],

    // 2. Cấu hình chạy trên Hosting (cPanel / InfinityFree / DirectAdmin / VPS...)
    // 👉 Khi upload web lên hosting, bạn chỉ cần điền thông số CSDL hosting vào đây:
    'hosting' => [
        'host'     => 'localhost',              // Thường là 'localhost' hoặc IP/host MySQL hosting cấp
        'dbname'   => 'bloomshop',              // Tên Database trên hosting (vd: epiz_38123456_bloomshop)
        'username' => 'root',                   // Tên tài khoản Database hosting (vd: epiz_38123456)
        'password' => '',                       // Mật khẩu Database trên hosting
        'port'     => 3306,
    ]
];
