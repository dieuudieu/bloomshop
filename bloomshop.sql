-- ==============================================================
-- BLOOMSHOP DATABASE DUMP - TỰ ĐỘNG ĐỒNG BỘ CHO HOSTING
-- Cập nhật mới nhất lúc: 2026-10-23 09:53:53
-- Tương thích 100% với phpMyAdmin, cPanel, InfinityFree, XAMPP
-- ==============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Cấu trúc bảng `danhmuc`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `danhmuc`;
CREATE TABLE `danhmuc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dữ liệu bảng `danhmuc`
INSERT INTO `danhmuc` (`id`, `name`) VALUES
('1', 'Hoa Hồng Quyến Rũ'),
('2', 'Hoa Tulip Thanh Lịch'),
('3', 'Hoa Hướng Dương Rực Rỡ'),
('4', 'Hoa Bó Mix Nghệ Thuật');

-- --------------------------------------------------------
-- Cấu trúc bảng `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','user') NOT NULL DEFAULT 'user',
  `status` tinyint(1) DEFAULT 1,
  `tier` enum('silver','gold','diamond') DEFAULT 'silver',
  `total_spent` decimal(12,2) DEFAULT 0.00,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dữ liệu bảng `users`
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `tier`, `total_spent`, `phone`, `address`, `created_at`) VALUES
('1', 'admin', 'tranphuocdieu1410@gmail.com', '$2y$10$exo6tak8eA8jSuxtaTjvZeVLehkA3aQLwrechua/as1S/XsCAKB4u', 'admin', '1', 'silver', '0.00', NULL, NULL, '2026-10-23 12:27:24'),
('2', 'user', 'nicobro1410@gmail.com', '$2y$10$2oL6WlBF4NeCmCRNResbte64l9VkpUbbS/sp1WlvF9Y56d/xZMweS', 'user', '1', 'silver', '0.00', NULL, NULL, '2026-10-23 12:27:24'),
('3', 'Quản Trị Viên', 'admin@bloomshop.vn', '$2y$10$KAxIU4DnpqSNYGo61P9.POnusepGginOJHjlem9Dkjze2Qe.sg9pS', 'admin', '1', 'diamond', '15000000.00', '0901234567', 'Tòa nhà Bloom, Quận 1, TP.HCM', '2026-10-23 12:27:24'),
('5', 'Lê Thị Kim Cương', 'diamond@bloomshop.vn', '$2y$10$z1KeoyWrs.dfa2k..ghNHu23LXrIz.g23ts3fIExZyOQoOjY3Atfe', 'user', '1', 'diamond', '6800000.00', '0912345678', '123 Lê Duẩn, Quận 1, TP.HCM', '2026-10-23 12:27:24'),
('6', 'Trần Hoàng Vàng', 'gold@bloomshop.vn', '$2y$10$z1KeoyWrs.dfa2k..ghNHu23LXrIz.g23ts3fIExZyOQoOjY3Atfe', 'user', '1', 'gold', '2900000.00', '0987654321', '456 Nguyễn Thị Minh Khai, Quận 3', '2026-10-23 12:27:24'),
('7', 'Phạm Ngọc Bạc', 'silver@bloomshop.vn', '$2y$10$z1KeoyWrs.dfa2k..ghNHu23LXrIz.g23ts3fIExZyOQoOjY3Atfe', 'user', '1', 'silver', '550000.00', '0933221100', '789 CMT8, Tân Bình, TP.HCM', '2026-10-23 12:27:24'),
('8', 'Dieu', 'dieutran141005@gmail.com', '$2y$10$kjKT9de4yHR8f6JheVbYju5rK56dRJyaztR5GzXFkYTiCy89eJrRG', 'admin', '1', 'silver', '0.00', NULL, NULL, '2026-10-23 12:27:24');

-- --------------------------------------------------------
-- Cấu trúc bảng `sanpham`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `sanpham`;
CREATE TABLE `sanpham` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `id_dm` int(11) NOT NULL,
  `sale_price` decimal(12,2) DEFAULT NULL,
  `is_sale` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_dm` (`id_dm`),
  CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`id_dm`) REFERENCES `danhmuc` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dữ liệu bảng `sanpham`
INSERT INTO `sanpham` (`id`, `name`, `price`, `image`, `id_dm`, `sale_price`, `is_sale`, `description`) VALUES
('1', 'Rose1', '250000.00', 'rose1.webp', '1', NULL, '0', NULL),
('2', 'Rose2', '350000.00', 'rose2.webp', '1', NULL, '0', NULL),
('3', 'Rose3', '550000.00', 'rose3.webp', '1', NULL, '0', NULL),
('4', 'Tulip1', '150000.00', 'tulip1.webp', '2', NULL, '0', NULL),
('5', 'Tulip2', '260000.00', 'tulip2.webp', '2', NULL, '0', NULL),
('6', 'Tulip3', '180000.00', 'tulip3.jpg', '2', NULL, '0', NULL),
('7', 'Sunflower1', '740000.00', 'sunflower1.webp', '3', NULL, '0', NULL),
('8', 'Sunflower2', '625000.00', 'sunflower2.jpeg', '3', NULL, '0', NULL),
('9', 'Mixed1', '585000.00', 'mixed1.webp', '4', NULL, '0', NULL),
('10', 'Mixed2', '645000.00', 'mixed2.jpg', '4', NULL, '0', NULL),
('11', 'Mixed3', '260000.00', 'mixed3.jpg', '4', NULL, '0', NULL),
('12', 'Mixed4', '860000.00', 'mixed4.webp', '4', NULL, '0', NULL);

-- --------------------------------------------------------
-- Cấu trúc bảng `vouchers`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `vouchers`;
CREATE TABLE `vouchers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('percent','fixed') DEFAULT 'fixed' COMMENT 'percent: %, fixed: VNĐ',
  `discount_value` decimal(12,2) NOT NULL,
  `min_order_value` decimal(12,2) DEFAULT 0.00 COMMENT 'Giá trị đơn tối thiểu',
  `quantity` int(11) NOT NULL DEFAULT 100 COMMENT 'Số lượng ban đầu',
  `used_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Đã sử dụng',
  `expiry_date` date NOT NULL COMMENT 'Thời hạn',
  `status` tinyint(1) DEFAULT 1 COMMENT '1: Hoạt động, 0: Khóa',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `vouchers`
INSERT INTO `vouchers` (`id`, `code`, `discount_type`, `discount_value`, `min_order_value`, `quantity`, `used_count`, `expiry_date`, `status`, `created_at`) VALUES
('1', 'BLOOM10', 'percent', '10.00', '200000.00', '100', '0', '2026-12-31', '1', '2026-10-23 00:26:01'),
('2', 'GIAM50K', 'fixed', '50000.00', '300000.00', '100', '0', '2026-12-31', '1', '2026-10-23 00:26:01'),
('3', 'VIPMEMBER', 'fixed', '100000.00', '600000.00', '50', '0', '2026-12-31', '1', '2026-10-23 00:26:01'),
('4', 'FREESHIP', 'fixed', '30000.00', '150000.00', '200', '0', '2026-12-31', '1', '2026-10-23 00:26:01');

-- --------------------------------------------------------
-- Cấu trúc bảng `donhang`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `donhang`;
CREATE TABLE `donhang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_address` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `voucher_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(12,2) DEFAULT 0.00,
  `total_price` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'COD',
  `status` enum('cho_xac_nhan','xac_nhan','dang_giao','da_giao','thanh_cong','da_huy') DEFAULT 'cho_xac_nhan',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `donhang_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `donhang`
INSERT INTO `donhang` (`id`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `customer_address`, `note`, `subtotal`, `voucher_code`, `discount_amount`, `total_price`, `payment_method`, `status`, `created_at`, `updated_at`) VALUES
('1', '5', 'Lê Thị Kim Cương', 'diamond@bloomshop.vn', '0912345678', '123 Lê Duẩn, Quận 1, TP.HCM', '', '860000.00', NULL, '0.00', '860000.00', 'COD', 'cho_xac_nhan', '2026-10-23 11:55:17', '2026-10-23 11:55:17'),
('2', '5', 'Lê Thị Kim Cương', 'dn629691@gmail.com', '0912345678', '123 Lê Duẩn, Quận 1, TP.HCM', '', '860000.00', NULL, '0.00', '860000.00', 'COD', 'cho_xac_nhan', '2026-10-23 11:55:43', '2026-10-23 11:55:43');

-- --------------------------------------------------------
-- Cấu trúc bảng `chitiet_donhang`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `chitiet_donhang`;
CREATE TABLE `chitiet_donhang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `chitiet_donhang_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `donhang` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `chitiet_donhang`
INSERT INTO `chitiet_donhang` (`id`, `order_id`, `product_id`, `product_name`, `product_image`, `price`, `quantity`, `total`) VALUES
('1', '1', '12', 'Mixed4', 'mixed4.webp', '860000.00', '1', '860000.00'),
('2', '2', '12', 'Mixed4', 'mixed4.webp', '860000.00', '1', '860000.00');

-- --------------------------------------------------------
-- Cấu trúc bảng `order_tracking`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `order_tracking`;
CREATE TABLE `order_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_tracking_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `donhang` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `order_tracking`
INSERT INTO `order_tracking` (`id`, `order_id`, `status`, `description`, `created_at`) VALUES
('1', '1', 'cho_xac_nhan', 'Đơn hàng mới được tạo thành công, đang chờ BloomShop xác nhận.', '2026-10-23 11:55:17'),
('2', '2', 'cho_xac_nhan', 'Đơn hàng mới được tạo thành công, đang chờ BloomShop xác nhận.', '2026-10-23 11:55:43');

-- --------------------------------------------------------
-- Cấu trúc bảng `danhgia`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `danhgia`;
CREATE TABLE `danhgia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 5 COMMENT '1 - 5 sao',
  `content` text NOT NULL,
  `status` tinyint(1) DEFAULT 1 COMMENT '1: Hiển thị, 0: Ẩn',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `danhgia_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `sanpham` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
