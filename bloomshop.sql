-- ==============================================================
-- DATABASE: bloomshop
-- BloomShop - Cửa hàng hoa tươi
-- ==============================================================

CREATE DATABASE IF NOT EXISTS `bloomshop` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `bloomshop`;

-- 1. BẢNG DANH MỤC
CREATE TABLE IF NOT EXISTS `danhmuc` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. BẢNG NGƯỜI DÙNG (ADMIN, NHÂN VIÊN, KHÁCH HÀNG)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `role` ENUM('admin', 'staff', 'user') DEFAULT 'user',
  `status` TINYINT(1) DEFAULT 1 COMMENT '1: Hoạt động, 0: Bị khóa',
  `tier` ENUM('silver', 'gold', 'diamond') DEFAULT 'silver' COMMENT 'Hạng thành viên',
  `total_spent` DECIMAL(12,2) DEFAULT 0 COMMENT 'Tổng chi tiêu tích lũy',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. BẢNG SẢN PHẨM
CREATE TABLE IF NOT EXISTS `sanpham` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `sale_price` DECIMAL(12,2) DEFAULT NULL COMMENT 'Giá khuyến mãi',
  `is_sale` TINYINT(1) DEFAULT 0 COMMENT '1: Đang khuyến mãi, 0: Không',
  `image` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `id_dm` INT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_dm`) REFERENCES `danhmuc`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. BẢNG MÃ GIẢM GIÁ (VOUCHER)
CREATE TABLE IF NOT EXISTS `vouchers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `discount_type` ENUM('percent', 'fixed') DEFAULT 'fixed' COMMENT 'percent: %, fixed: VNĐ',
  `discount_value` DECIMAL(12,2) NOT NULL,
  `min_order_value` DECIMAL(12,2) DEFAULT 0 COMMENT 'Giá trị đơn tối thiểu',
  `quantity` INT NOT NULL DEFAULT 100 COMMENT 'Số lượng ban đầu',
  `used_count` INT NOT NULL DEFAULT 0 COMMENT 'Đã sử dụng',
  `expiry_date` DATE NOT NULL COMMENT 'Thời hạn',
  `status` TINYINT(1) DEFAULT 1 COMMENT '1: Hoạt động, 0: Khóa',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. BẢNG ĐƠN HÀNG
CREATE TABLE IF NOT EXISTS `donhang` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(100) NOT NULL,
  `customer_phone` VARCHAR(20) NOT NULL,
  `customer_address` VARCHAR(255) NOT NULL,
  `note` TEXT DEFAULT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `voucher_code` VARCHAR(50) DEFAULT NULL,
  `discount_amount` DECIMAL(12,2) DEFAULT 0,
  `total_price` DECIMAL(12,2) NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'COD',
  `status` ENUM('cho_xac_nhan', 'xac_nhan', 'dang_giao', 'da_giao', 'thanh_cong', 'da_huy') DEFAULT 'cho_xac_nhan',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. BẢNG CHI TIẾT ĐƠN HÀNG
CREATE TABLE IF NOT EXISTS `chitiet_donhang` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT DEFAULT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `product_image` VARCHAR(255) DEFAULT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `quantity` INT NOT NULL,
  `total` DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `donhang`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. BẢNG TIẾN TRÌNH GIAO HÀNG (TRACKING)
CREATE TABLE IF NOT EXISTS `order_tracking` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `status` VARCHAR(50) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `donhang`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. BẢNG ĐÁNH GIÁ & BÌNH LUẬN SẢN PHẨM
CREATE TABLE IF NOT EXISTS `danhgia` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `user_id` INT DEFAULT NULL,
  `user_name` VARCHAR(100) NOT NULL,
  `rating` INT NOT NULL DEFAULT 5 COMMENT '1 - 5 sao',
  `content` TEXT NOT NULL,
  `status` TINYINT(1) DEFAULT 1 COMMENT '1: Hiển thị, 0: Ẩn',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `sanpham`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- DỮ LIỆU MẪU BAN ĐẦU (SEED DATA)
-- ==============================================================

-- 1. Danh mục
INSERT INTO `danhmuc` (`id`, `name`) VALUES
(1, 'Hoa Hồng Quyến Rũ'),
(2, 'Hoa Tulip Thanh Lịch'),
(3, 'Hoa Hướng Dương Rực Rỡ'),
(4, 'Hoa Bó Mix Nghệ Thuật')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 2. Tài khoản mẫu (Admin: admin123 | Staff: staff123 | Khách hàng: 123456)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `role`, `status`, `tier`, `total_spent`) VALUES
(1, 'Quản Trị Viên', 'admin@bloomshop.vn', '$2y$10$KAxIU4DnpqSNYGo61P9.POnusepGginOJHjlem9Dkjze2Qe.sg9pS', '0901234567', 'Tòa nhà Bloom, Quận 1, TP.HCM', 'admin', 1, 'diamond', 15000000.00),
(2, 'Nguyễn Văn Nhân Viên', 'staff@bloomshop.vn', '$2y$10$1zwom.nZS681rIEeglmFc.PrUkoZt1cZjH5M35pGnFsuAWrFD6Idq', '0909888777', 'BloomShop Chi nhánh 1', 'staff', 1, 'silver', 0.00),
(3, 'Lê Thị Kim Cương', 'diamond@bloomshop.vn', '$2y$10$z1KeoyWrs.dfa2k..ghNHu23LXrIz.g23ts3fIExZyOQoOjY3Atfe', '0912345678', '123 Lê Duẩn, Quận 1, TP.HCM', 'user', 1, 'diamond', 6800000.00),
(4, 'Trần Hoàng Vàng', 'gold@bloomshop.vn', '$2y$10$z1KeoyWrs.dfa2k..ghNHu23LXrIz.g23ts3fIExZyOQoOjY3Atfe', '0987654321', '456 Nguyễn Thị Minh Khai, Quận 3', 'user', 1, 'gold', 2900000.00),
(5, 'Phạm Ngọc Bạc', 'silver@bloomshop.vn', '$2y$10$z1KeoyWrs.dfa2k..ghNHu23LXrIz.g23ts3fIExZyOQoOjY3Atfe', '0933221100', '789 CMT8, Tân Bình, TP.HCM', 'user', 1, 'silver', 550000.00)
ON DUPLICATE KEY UPDATE `password`=VALUES(`password`), `role`=VALUES(`role`), `status`=1;

-- 3. Sản phẩm mẫu
INSERT INTO `sanpham` (`id`, `name`, `price`, `sale_price`, `is_sale`, `image`, `description`, `id_dm`) VALUES
(1, 'Bó Hoa Hồng Đỏ Lãng Mạn', 380000, 320000, 1, 'rose1.webp', 'Bó hoa hồng đỏ nhập khẩu kết hợp lá phụ thanh lịch, biểu tượng cho tình yêu nồng nàn và vĩnh cửu.', 1),
(2, 'Bó Hoa Hồng Phấn Pastel Dịu Dàng', 420000, NULL, 0, 'rose2.webp', 'Tone hồng phấn ngọt ngào, nhẹ nhàng thích hợp tặng bạn gái, mẹ hoặc đồng nghiệp vào các dịp đặc biệt.', 1),
(3, 'Bó Hoa Hồng Kem Dâu Quyến Rũ', 450000, 399000, 1, 'rose3.webp', 'Hồng kem dâu tuyển chọn cánh dày, mùi hương dịu dàng tỏa ngát, gói giấy mờ phong cách Hàn Quốc.', 1),
(4, 'Bó Hoa Tulip Vàng Ánh Dương', 520000, NULL, 0, 'tulip1.webp', 'Hoa tulip vàng tươi tắn đại diện cho sự may mắn, thịnh vượng và nguồn năng lượng tích cực tràn ngập.', 2),
(5, 'Bó Hoa Tulip Trắng Tinh Khôi', 550000, 480000, 1, 'tulip2.webp', 'Tulip Hà Lan trắng thuần khiết, mang vẻ đẹp tao nhã, kiêu kỳ dành cho những sự kiện sang trọng.', 2),
(6, 'Bó Hoa Tulip Hồng Ngọt Ngào', 530000, NULL, 0, 'tulip3.jpg', 'Sắc hồng e ấp như nụ cười đầu ngày, thiết kế đơn giản nhưng đốn tim mọi ánh nhìn.', 2),
(7, 'Bó Hướng Dương Nắng Sớm Rạng Ngời', 390000, 340000, 1, 'sunflower1.webp', 'Bó hoa hướng dương tràn đầy sức sống, món quà hoàn hảo cổ vũ tinh thần, chúc mừng lễ tốt nghiệp.', 3),
(8, 'Bó Hướng Dương Mix Baby Nắng Hạ', 450000, NULL, 0, 'sunflower2.jpeg', 'Sự phối hợp ăn ý giữa hướng dương vàng ươm và hoa bi trắng li ti tạo nên sự thanh thoát rực rỡ.', 3),
(9, 'Bó Hoa Mix Nghệ Thuật Pastel Dreams', 650000, 580000, 1, 'mixed1.webp', 'Bó hoa phối nhiều loại hoa nhập cao cấp theo tone màu pastel hiện đại chuẩn phong cách florist cao cấp.', 4),
(10, 'Bó Hoa Phối Phong Cách Sang Trọng', 750000, NULL, 0, 'mixed2.jpg', 'Thiết kế hoa hoành tráng, sang trọng dành tặng đối tác, sếp hoặc ngày kỷ niệm trọng đại.', 4),
(11, 'Bó Hoa Mix Cúc Tana & Hồng Kem', 480000, 420000, 1, 'mixed3.jpg', 'Sự hòa quyện giữa nét mộc mạc của hoa cúc tana và vẻ đài các của hoa hồng kem ngọt ngào.', 4),
(12, 'Bó Hoa Mix Sắc Màu Tươi Mới', 590000, NULL, 0, 'mixed4.webp', 'Rực rỡ sắc màu yêu thương đem đến niềm vui bất tận cho người nhận.', 4)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 4. Mã giảm giá (Voucher)
INSERT INTO `vouchers` (`id`, `code`, `discount_type`, `discount_value`, `min_order_value`, `quantity`, `used_count`, `expiry_date`, `status`) VALUES
(1, 'BLOOM10', 'percent', 10.00, 200000.00, 100, 4, '2026-12-31', 1),
(2, 'GIAM50K', 'fixed', 50000.00, 300000.00, 100, 12, '2026-12-31', 1),
(3, 'VIPMEMBER', 'fixed', 100000.00, 600000.00, 50, 2, '2026-12-31', 1),
(4, 'FREESHIP', 'fixed', 30000.00, 150000.00, 200, 25, '2026-12-31', 1)
ON DUPLICATE KEY UPDATE `status`=1, `expiry_date`='2026-12-31', `discount_value`=VALUES(`discount_value`), `min_order_value`=VALUES(`min_order_value`);

-- 5. Đánh giá mẫu
INSERT INTO `danhgia` (`id`, `product_id`, `user_id`, `user_name`, `rating`, `content`, `status`, `created_at`) VALUES
(1, 1, 3, 'Lê Thị Kim Cương', 5, 'Hoa giao rất tươi và đẹp y như hình chụp, người yêu mình thích lắm. Cảm ơn shop nhiều!', 1, NOW() - INTERVAL 5 DAY),
(2, 1, 4, 'Trần Hoàng Vàng', 5, 'Bó hoa được đóng gói rất cẩn thận, shipper giao đúng giờ hẹn. Sẽ tiếp tục ủng hộ shop.', 1, NOW() - INTERVAL 4 DAY),
(3, 5, 3, 'Lê Thị Kim Cương', 5, 'Tulip trắng siêu đẹp và sang trọng, giữ được tươi rất lâu!', 1, NOW() - INTERVAL 3 DAY),
(4, 7, 5, 'Phạm Ngọc Bạc', 4, 'Hướng dương nở to, đều bông rất tươi. Giấy gói hoa đẹp mắt.', 1, NOW() - INTERVAL 2 DAY),
(5, 9, 4, 'Trần Hoàng Vàng', 5, 'Hoa bó mix màu pastel xinh xỉu, chụp hình sống ảo hết nước chấm!', 1, NOW() - INTERVAL 1 DAY)
ON DUPLICATE KEY UPDATE `id`=VALUES(`id`);

-- 6. Đơn hàng mẫu phục vụ thống kê doanh thu
INSERT INTO `donhang` (`id`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `customer_address`, `note`, `subtotal`, `voucher_code`, `discount_amount`, `total_price`, `payment_method`, `status`, `created_at`) VALUES
(1001, 3, 'Lê Thị Kim Cương', 'diamond@bloomshop.vn', '0912345678', '123 Lê Duẩn, Q1, TP.HCM', 'Giao vào giờ hành chính', 1230000, 'BLOOM10', 123000, 1107000, 'COD', 'thanh_cong', NOW() - INTERVAL 6 DAY),
(1002, 4, 'Trần Hoàng Vàng', 'gold@bloomshop.vn', '0987654321', '456 Nguyễn Thị Minh Khai, Q3', 'Nhớ kèm thiệp chúc mừng', 840000, 'GIAM50K', 50000, 790000, 'Chuyển khoản', 'thanh_cong', NOW() - INTERVAL 4 DAY),
(1003, 5, 'Phạm Ngọc Bạc', 'silver@bloomshop.vn', '0933221100', '789 CMT8, Tân Bình', 'Gọi trước khi đến 15 phút', 550000, NULL, 0, 550000, 'COD', 'thanh_cong', NOW() - INTERVAL 2 DAY),
(1004, 3, 'Lê Thị Kim Cương', 'diamond@bloomshop.vn', '0912345678', '123 Lê Duẩn, Q1, TP.HCM', '', 750000, 'FREESHIP', 30000, 720000, 'COD', 'dang_giao', NOW() - INTERVAL 1 DAY),
(1005, 4, 'Trần Hoàng Vàng', 'gold@bloomshop.vn', '0987654321', '456 Nguyễn Thị Minh Khai, Q3', 'Hoa tặng sinh nhật', 650000, NULL, 0, 650000, 'COD', 'cho_xac_nhan', NOW())
ON DUPLICATE KEY UPDATE `id`=VALUES(`id`);

-- 7. Chi tiết đơn hàng mẫu
INSERT INTO `chitiet_donhang` (`order_id`, `product_id`, `product_name`, `product_image`, `price`, `quantity`, `total`) VALUES
(1001, 1, 'Bó Hoa Hồng Đỏ Lãng Mạn', 'rose1.webp', 320000, 2, 640000),
(1001, 9, 'Bó Hoa Mix Nghệ Thuật Pastel Dreams', 'mixed1.webp', 580000, 1, 580000),
(1002, 7, 'Bó Hướng Dương Nắng Sớm Rạng Ngời', 'sunflower1.webp', 340000, 1, 340000),
(1002, 5, 'Bó Hoa Tulip Trắng Tinh Khôi', 'tulip2.webp', 480000, 1, 480000),
(1003, 5, 'Bó Hoa Tulip Trắng Tinh Khôi', 'tulip2.webp', 480000, 1, 480000),
(1004, 10, 'Bó Hoa Phối Phong Cách Sang Trọng', 'mixed2.jpg', 750000, 1, 750000),
(1005, 9, 'Bó Hoa Mix Nghệ Thuật Pastel Dreams', 'mixed1.webp', 580000, 1, 580000);

-- 8. Lịch sử tracking mẫu
INSERT INTO `order_tracking` (`order_id`, `status`, `description`, `created_at`) VALUES
(1001, 'cho_xac_nhan', 'Đơn hàng đã được đặt thành công.', NOW() - INTERVAL 6 DAY),
(1001, 'xac_nhan', 'BloomShop đã xác nhận đơn hàng và chuẩn bị hoa tươi.', NOW() - INTERVAL 6 DAY + INTERVAL 1 HOUR),
(1001, 'dang_giao', 'Shipper đang trên đường giao hoa đến quý khách.', NOW() - INTERVAL 5 DAY),
(1001, 'da_giao', 'Hoa đã được giao tận tay người nhận.', NOW() - INTERVAL 5 DAY + INTERVAL 2 HOUR),
(1001, 'thanh_cong', 'Đơn hàng hoàn tất. BloomShop chân thành cảm ơn quý khách!', NOW() - INTERVAL 5 DAY + INTERVAL 3 HOUR),

(1004, 'cho_xac_nhan', 'Đơn hàng đã được đặt thành công.', NOW() - INTERVAL 1 DAY),
(1004, 'xac_nhan', 'BloomShop đã xác nhận đơn hàng.', NOW() - INTERVAL 1 DAY + INTERVAL 30 MINUTE),
(1004, 'dang_giao', 'Đang vận chuyển hoa đến địa chỉ nhận.', NOW() - INTERVAL 2 HOUR),

(1005, 'cho_xac_nhan', 'Đơn hàng mới tạo, đang chờ BloomShop xác nhận.', NOW());
