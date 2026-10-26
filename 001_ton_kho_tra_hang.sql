-- BloomShop 3 - migration database cho may B
-- Chay MOT LAN (co the chay lai an toan) trong phpMyAdmin > chon database > SQL.
-- KHONG DROP TABLE, KHONG xoa du lieu. Backup database truoc khi chay.
-- Database duoc chon trong phpMyAdmin phai la database cua BloomShop.

SET @db = DATABASE();

-- Them cot neu chua co, khong thay doi ton kho hien tai neu da co cot.
SET @stmt = IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='sanpham' AND COLUMN_NAME='so_luong') = 0,
  'ALTER TABLE `sanpham` ADD COLUMN `so_luong` INT NOT NULL DEFAULT 0',
  'SELECT ''Cot so_luong da ton tai'' AS thong_bao'
);
PREPARE migration FROM @stmt; EXECUTE migration; DEALLOCATE PREPARE migration;

-- Them cot theo doi tru kho va hoan kho (tranh hoan hai lan).
SET @stmt = IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='donhang' AND COLUMN_NAME='stock_deducted') = 0,
  'ALTER TABLE `donhang` ADD COLUMN `stock_deducted` TINYINT(1) NOT NULL DEFAULT 0',
  'SELECT ''Cot stock_deducted da ton tai'' AS thong_bao'
);
PREPARE migration FROM @stmt; EXECUTE migration; DEALLOCATE PREPARE migration;

SET @stmt = IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='donhang' AND COLUMN_NAME='inventory_restored') = 0,
  'ALTER TABLE `donhang` ADD COLUMN `inventory_restored` TINYINT(1) NOT NULL DEFAULT 0',
  'SELECT ''Cot inventory_restored da ton tai'' AS thong_bao'
);
PREPARE migration FROM @stmt; EXECUTE migration; DEALLOCATE PREPARE migration;

SET @stmt = IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='donhang' AND COLUMN_NAME='voucher_restored') = 0,
  'ALTER TABLE `donhang` ADD COLUMN `voucher_restored` TINYINT(1) NOT NULL DEFAULT 0',
  'SELECT ''Cot voucher_restored da ton tai'' AS thong_bao'
);
PREPARE migration FROM @stmt; EXECUTE migration; DEALLOCATE PREPARE migration;

SET @stmt = IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='donhang' AND COLUMN_NAME='return_reason') = 0,
  'ALTER TABLE `donhang` ADD COLUMN `return_reason` TEXT DEFAULT NULL',
  'SELECT ''Cot return_reason da ton tai'' AS thong_bao'
);
PREPARE migration FROM @stmt; EXECUTE migration; DEALLOCATE PREPARE migration;

SET @stmt = IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='donhang' AND COLUMN_NAME='return_requested_at') = 0,
  'ALTER TABLE `donhang` ADD COLUMN `return_requested_at` DATETIME DEFAULT NULL',
  'SELECT ''Cot return_requested_at da ton tai'' AS thong_bao'
);
PREPARE migration FROM @stmt; EXECUTE migration; DEALLOCATE PREPARE migration;

-- Cap nhat enum trang thai don hang. Cac gia tri duoc lay tu bloomshop.sql ban moi nhat.
-- Neu database cua ban B co trang thai TUY CHINH ngoai danh sach nay,
-- phai them gia tri do vao ENUM truoc khi chay lenh nay.
ALTER TABLE `donhang`
  MODIFY COLUMN `status`
  ENUM('cho_xac_nhan','xac_nhan','dang_giao','da_giao','thanh_cong','da_huy','yeu_cau_tra','da_tra_hang','tu_choi_tra')
  NOT NULL DEFAULT 'cho_xac_nhan';

-- Xac nhan ket qua:
SHOW COLUMNS FROM `sanpham` LIKE 'so_luong';
SHOW COLUMNS FROM `donhang` WHERE Field IN
('status','stock_deducted','inventory_restored','voucher_restored','return_reason','return_requested_at');

-- LUu Y:
-- San pham cu duoc them cot voi ton kho = 0; Admin can nhap ton kho thuc te.
-- Don hang cu co stock_deducted = 0; khong tu dong hoan kho cho don cu.
-- Migration nay KHONG chinh sua luot su dung voucher hien co.
