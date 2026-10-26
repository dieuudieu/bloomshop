-- Bản bloomshop.sql trong ZIP đã có so_luong và các cột trả hàng.
-- Nếu bạn đã import bloomshop.sql từ ZIP này: KHÔNG chạy ALTER TABLE nào.
-- Kiểm tra cấu trúc bằng:
SHOW COLUMNS FROM sanpham LIKE 'so_luong';
SHOW COLUMNS FROM donhang LIKE 'stock_deducted';
SHOW COLUMNS FROM donhang LIKE 'inventory_restored';
SHOW COLUMNS FROM donhang LIKE 'voucher_restored';
SHOW COLUMNS FROM donhang LIKE 'return_reason';
-- Nhập số lượng tồn kho thực tế trong trang Admin trước khi đặt hàng thử.
