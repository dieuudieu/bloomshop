-- ==============================================================
-- BLOOMSHOP DATABASE DUMP - TỰ ĐỘNG ĐỒNG BỘ CHO HOSTING
-- Cập nhật mới nhất lúc: 2026-10-26 09:42:08
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dữ liệu bảng `users`
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `tier`, `total_spent`, `phone`, `address`, `created_at`) VALUES
('1', 'admin', 'tranphuocdieu1410@gmail.com', '$2y$10$exo6tak8eA8jSuxtaTjvZeVLehkA3aQLwrechua/as1S/XsCAKB4u', 'admin', '1', 'silver', '0.00', NULL, NULL, '2026-10-23 12:33:18'),
('2', 'user', 'nicobro1410@gmail.com', '$2y$10$2oL6WlBF4NeCmCRNResbte64l9VkpUbbS/sp1WlvF9Y56d/xZMweS', 'user', '1', 'diamond', '8475000.00', NULL, NULL, '2026-10-23 12:33:18'),
('3', 'Quản Trị Viên', 'admin@bloomshop.vn', '$2y$10$KAxIU4DnpqSNYGo61P9.POnusepGginOJHjlem9Dkjze2Qe.sg9pS', 'admin', '1', 'diamond', '15000000.00', '0901234567', 'Tòa nhà Bloom, Quận 1, TP.HCM', '2026-10-23 12:33:18'),
('5', 'Lê Thị Kim Cương', 'diamond@bloomshop.vn', '$2y$10$z1KeoyWrs.dfa2k..ghNHu23LXrIz.g23ts3fIExZyOQoOjY3Atfe', 'user', '1', 'diamond', '6800000.00', '0912345678', '123 Lê Duẩn, Quận 1, TP.HCM', '2026-10-23 12:33:18'),
('6', 'Trần Hoàng Vàng', 'gold@bloomshop.vn', '$2y$10$z1KeoyWrs.dfa2k..ghNHu23LXrIz.g23ts3fIExZyOQoOjY3Atfe', 'user', '1', 'gold', '2900000.00', '0987654321', '456 Nguyễn Thị Minh Khai, Quận 3', '2026-10-23 12:33:18'),
('7', 'Phạm Ngọc Bạc', 'silver@bloomshop.vn', '$2y$10$z1KeoyWrs.dfa2k..ghNHu23LXrIz.g23ts3fIExZyOQoOjY3Atfe', 'user', '1', 'silver', '550000.00', '0933221100', '789 CMT8, Tân Bình, TP.HCM', '2026-10-23 12:33:18'),
('8', 'Dieu', 'dieutran141005@gmail.com', '$2y$10$DaV735ITSdyhoK2KJ2Nqz.xdQbPNAt2kA6kPzy64MdQtnqPpF7ZGO', 'admin', '1', 'silver', '0.00', NULL, NULL, '2026-10-23 12:33:18');

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
  `so_luong` int(11) NOT NULL DEFAULT 0 COMMENT 'Ton kho hien tai',
  PRIMARY KEY (`id`),
  KEY `id_dm` (`id_dm`),
  CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`id_dm`) REFERENCES `danhmuc` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dữ liệu bảng `sanpham`
INSERT INTO `sanpham` (`id`, `name`, `price`, `image`, `id_dm`, `sale_price`, `is_sale`, `description`, `so_luong`) VALUES
('1', 'Rose1', '250000.00', 'rose1.webp', '1', NULL, '0', NULL, '0'),
('2', 'Rose2', '350000.00', 'rose2.webp', '1', NULL, '0', NULL, '0'),
('3', 'Rose3', '550000.00', 'rose3.webp', '1', NULL, '0', NULL, '0'),
('4', 'Tulip1', '150000.00', 'tulip1.webp', '2', NULL, '0', NULL, '0'),
('5', 'Tulip2', '260000.00', 'tulip2.webp', '2', NULL, '0', NULL, '0'),
('6', 'Tulip3', '180000.00', 'tulip3.jpg', '2', NULL, '0', NULL, '0'),
('7', 'Sunflower1', '740000.00', 'sunflower1.webp', '3', NULL, '0', NULL, '0'),
('8', 'Sunflower2', '625000.00', 'sunflower2.jpeg', '3', NULL, '0', NULL, '0'),
('9', 'Mixed1', '585000.00', 'mixed1.webp', '4', '500000.00', '1', '', '1'),
('10', 'Mixed2', '645000.00', 'mixed2.jpg', '4', NULL, '0', NULL, '0'),
('11', 'Mixed3', '260000.00', 'mixed3.jpg', '4', NULL, '0', NULL, '0'),
('12', 'Mixed4', '860000.00', 'mixed4.webp', '4', NULL, '0', NULL, '0'),
('13', 'rose4', '690000.00', 'hoa_6adf121a66d9c.jpg', '1', NULL, '0', '', '0');

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
('1', 'BLOOM10', 'percent', '10.00', '200000.00', '100', '0', '2026-12-31', '1', '2026-10-23 00:23:56'),
('2', 'GIAM50K', 'fixed', '50000.00', '300000.00', '100', '0', '2026-12-31', '1', '2026-10-23 00:23:56'),
('3', 'VIPMEMBER', 'fixed', '100000.00', '600000.00', '50', '1', '2026-12-31', '1', '2026-10-23 00:23:56'),
('4', 'FREESHIP', 'fixed', '30000.00', '150000.00', '200', '0', '2026-12-31', '1', '2026-10-23 00:23:56');

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
  `status` enum('cho_xac_nhan','xac_nhan','dang_giao','da_giao','thanh_cong','da_huy','yeu_cau_tra','da_tra_hang','tu_choi_tra') NOT NULL DEFAULT 'cho_xac_nhan',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stock_deducted` tinyint(1) NOT NULL DEFAULT 0,
  `inventory_restored` tinyint(1) NOT NULL DEFAULT 0,
  `voucher_restored` tinyint(1) NOT NULL DEFAULT 0,
  `return_reason` text DEFAULT NULL,
  `return_requested_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `donhang_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `donhang`
INSERT INTO `donhang` (`id`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `customer_address`, `note`, `subtotal`, `voucher_code`, `discount_amount`, `total_price`, `payment_method`, `status`, `created_at`, `updated_at`, `stock_deducted`, `inventory_restored`, `voucher_restored`, `return_reason`, `return_requested_at`) VALUES
('1', '2', 'user', 'nicobro1410@gmail.com', '0767144663', '123', 'đừng giao', '645000.00', NULL, '0.00', '645000.00', 'COD', 'thanh_cong', '2026-10-22 21:18:06', '2026-10-22 23:29:25', '0', '0', '0', NULL, NULL),
('2', '2', 'user', 'nicobro1410@gmail.com', '0767144663', '123 10th street', '', '6020000.00', NULL, '0.00', '6020000.00', 'Chuyển khoản ngân hàng', 'thanh_cong', '2026-10-22 23:28:21', '2026-10-22 23:29:31', '0', '0', '0', NULL, NULL),
('3', '2', 'user', 'nicobro1410@gmail.com', '0767144663', '123 10th street', '', '1390000.00', 'VIPMEMBER', '100000.00', '1290000.00', 'COD', 'thanh_cong', '2026-10-23 01:12:14', '2026-10-26 00:44:22', '0', '0', '0', NULL, NULL),
('4', '2', 'user', 'nicobro1410@gmail.com', '0767144663', '123 10th street', '', '260000.00', NULL, '0.00', '260000.00', 'COD', 'thanh_cong', '2026-10-23 11:13:00', '2026-10-26 00:44:19', '0', '0', '0', NULL, NULL),
('5', '2', 'user', 'nicobro1410@gmail.com', '0767144663', '123 10th street', '', '260000.00', NULL, '0.00', '260000.00', 'COD', 'thanh_cong', '2026-10-23 11:16:17', '2026-10-26 00:44:12', '0', '0', '0', NULL, NULL),
('6', '2', 'user', 'nicobro1410@gmail.com', '0767144663', '123 10th street', '', '260000.00', NULL, '0.00', '260000.00', 'Chuyển khoản ngân hàng', 'da_huy', '2026-10-23 11:28:51', '2026-10-26 00:30:09', '0', '0', '1', NULL, NULL),
('7', '2', 'user', 'nicobro1410@gmail.com', '0767144663', '123 10th street', '', '180000.00', 'FREESHIP', '30000.00', '150000.00', 'COD', 'da_huy', '2026-10-23 12:04:18', '2026-10-26 00:44:06', '0', '0', '0', NULL, NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `chitiet_donhang`
INSERT INTO `chitiet_donhang` (`id`, `order_id`, `product_id`, `product_name`, `product_image`, `price`, `quantity`, `total`) VALUES
('1', '1', '10', 'Mixed2', 'mixed2.jpg', '645000.00', '1', '645000.00'),
('2', '2', '12', 'Mixed4', 'mixed4.webp', '860000.00', '7', '6020000.00'),
('3', '3', '12', 'Mixed4', 'mixed4.webp', '860000.00', '1', '860000.00'),
('4', '3', '6', 'Tulip3', 'tulip3.jpg', '180000.00', '1', '180000.00'),
('5', '3', '2', 'Rose2', 'rose2.webp', '350000.00', '1', '350000.00'),
('6', '4', '11', 'Mixed3', 'mixed3.jpg', '260000.00', '1', '260000.00'),
('7', '5', '11', 'Mixed3', 'mixed3.jpg', '260000.00', '1', '260000.00'),
('8', '6', '11', 'Mixed3', 'mixed3.jpg', '260000.00', '1', '260000.00'),
('9', '7', '6', 'Tulip3', 'tulip3.jpg', '180000.00', '1', '180000.00');

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
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `order_tracking`
INSERT INTO `order_tracking` (`id`, `order_id`, `status`, `description`, `created_at`) VALUES
('1', '1', 'cho_xac_nhan', 'Đơn hàng mới được tạo thành công, đang chờ BloomShop xác nhận.', '2026-10-22 21:18:06'),
('2', '1', 'xac_nhan', 'BloomShop đã xác nhận đơn hàng và đang chuẩn bị hoa tươi.', '2026-10-22 21:21:31'),
('3', '1', 'cho_xac_nhan', 'Đang chờ BloomShop xác nhận đơn hàng.', '2026-10-22 21:21:34'),
('4', '1', 'xac_nhan', 'BloomShop đã xác nhận đơn hàng và đang chuẩn bị hoa tươi.', '2026-10-22 21:21:37'),
('5', '1', 'xac_nhan', 'BloomShop đã xác nhận đơn hàng và đang chuẩn bị hoa tươi.', '2026-10-22 21:21:50'),
('6', '2', 'cho_xac_nhan', 'Đơn hàng mới được tạo thành công, đang chờ BloomShop xác nhận.', '2026-10-22 23:28:21'),
('7', '1', 'dang_giao', 'Hoa tươi đã xuất kho và bàn giao cho Shipper đang trên đường vận chuyển tới địa chỉ nhận.', '2026-10-22 23:29:07'),
('8', '2', 'xac_nhan', 'BloomShop đã xác nhận đơn hàng và đang cắm hoa tươi chuẩn bị xuất kho.', '2026-10-22 23:29:12'),
('9', '1', 'da_giao', 'Shipper đã giao hoa tươi thành công đến tận tay người nhận.', '2026-10-22 23:29:18'),
('10', '1', 'thanh_cong', 'Đơn hàng hoàn tất và quyết toán thành công. Khách hàng đã được tích lũy chi tiêu VIP!', '2026-10-22 23:29:25'),
('11', '2', 'dang_giao', 'Hoa tươi đã xuất kho và bàn giao cho Shipper đang trên đường vận chuyển tới địa chỉ nhận.', '2026-10-22 23:29:27'),
('12', '2', 'da_giao', 'Shipper đã giao hoa tươi thành công đến tận tay người nhận.', '2026-10-22 23:29:29'),
('13', '2', 'thanh_cong', 'Đơn hàng hoàn tất và quyết toán thành công. Khách hàng đã được tích lũy chi tiêu VIP!', '2026-10-22 23:29:31'),
('14', '3', 'cho_xac_nhan', 'Đơn hàng mới được tạo thành công, đang chờ BloomShop xác nhận.', '2026-10-23 01:12:14'),
('15', '4', 'cho_xac_nhan', 'Đơn hàng mới được tạo thành công, đang chờ BloomShop xác nhận.', '2026-10-23 11:13:00'),
('16', '5', 'cho_xac_nhan', 'Đơn hàng mới được tạo thành công, đang chờ BloomShop xác nhận.', '2026-10-23 11:16:17'),
('17', '5', 'xac_nhan', 'BloomShop đã xác nhận đơn hàng và đang cắm hoa tươi chuẩn bị xuất kho.', '2026-10-23 11:17:46'),
('18', '4', 'xac_nhan', 'BloomShop đã xác nhận đơn hàng và đang cắm hoa tươi chuẩn bị xuất kho.', '2026-10-23 11:17:51'),
('19', '3', 'xac_nhan', 'BloomShop đã xác nhận đơn hàng và đang cắm hoa tươi chuẩn bị xuất kho.', '2026-10-23 11:17:52'),
('20', '3', 'dang_giao', 'Hoa tươi đã xuất kho và bàn giao cho Shipper đang trên đường vận chuyển tới địa chỉ nhận.', '2026-10-23 11:24:38'),
('21', '5', 'dang_giao', 'Hoa tươi đã xuất kho và bàn giao cho Shipper đang trên đường vận chuyển tới địa chỉ nhận.', '2026-10-23 11:24:41'),
('22', '6', 'cho_xac_nhan', 'Đơn hàng mới được tạo thành công, đang chờ BloomShop xác nhận.', '2026-10-23 11:28:52'),
('23', '6', 'da_huy', 'Khách hàng đã hủy đơn hàng.', '2026-10-23 12:03:45'),
('24', '7', 'cho_xac_nhan', 'Đơn hàng mới được tạo thành công, đang chờ BloomShop xác nhận.', '2026-10-23 12:04:18'),
('25', '7', 'xac_nhan', 'BloomShop đã xác nhận đơn hàng và đang cắm hoa tươi chuẩn bị xuất kho.', '2026-10-26 00:37:45'),
('26', '7', 'dang_giao', 'Hoa tươi đã xuất kho và bàn giao cho Shipper đang trên đường vận chuyển tới địa chỉ nhận.', '2026-10-26 00:37:47'),
('27', '5', 'da_giao', 'Shipper đã giao hoa tươi thành công đến tận tay người nhận.', '2026-10-26 00:37:49'),
('28', '4', 'dang_giao', 'Hoa tươi đã xuất kho và bàn giao cho Shipper đang trên đường vận chuyển tới địa chỉ nhận.', '2026-10-26 00:37:51'),
('29', '7', 'da_huy', 'Đơn hàng đã bị hủy. Thực hiện bởi: admin.', '2026-10-26 00:44:06'),
('30', '5', 'thanh_cong', 'Đơn hàng hoàn tất và quyết toán thành công. Khách hàng đã được tích lũy chi tiêu VIP!', '2026-10-26 00:44:12'),
('31', '4', 'da_giao', 'Shipper đã giao hoa tươi thành công đến tận tay người nhận.', '2026-10-26 00:44:14'),
('32', '3', 'da_giao', 'Shipper đã giao hoa tươi thành công đến tận tay người nhận.', '2026-10-26 00:44:17'),
('33', '4', 'thanh_cong', 'Đơn hàng hoàn tất và quyết toán thành công. Khách hàng đã được tích lũy chi tiêu VIP!', '2026-10-26 00:44:19'),
('34', '3', 'thanh_cong', 'Đơn hàng hoàn tất và quyết toán thành công. Khách hàng đã được tích lũy chi tiêu VIP!', '2026-10-26 00:44:22');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `danhgia`
INSERT INTO `danhgia` (`id`, `product_id`, `user_id`, `user_name`, `rating`, `content`, `status`, `created_at`) VALUES
('1', '10', '2', 'user', '5', 'đẹp', '1', '2026-10-22 23:53:48');

SET FOREIGN_KEY_CHECKS = 1;
