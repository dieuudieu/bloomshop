# BloomShop - Hệ Thống Website Bán Hoa Tươi Thuần PHP

Dự án website bán hoa tươi trực tuyến được xây dựng bằng **PHP thuần (Vanilla PHP) kết hợp MySQL và Bootstrap 5**, cấu trúc mã nguồn MVC đơn giản, rõ ràng, dễ bảo trì và dễ chấm điểm.

---

## 📁 Cấu Trúc Mã Nguồn (Mô Hình MVC Thuần PHP)

```
bloomshop/
├── index.php                         # Điều hướng người dùng vào controller/index.php
├── bloomshop.sql                     # Kịch bản cơ sở dữ liệu mẫu hoàn chỉnh
│
├── controller/                       # TẦNG ĐIỀU HƯỚNG & XỬ LÝ NGHIỆP VỤ (CONTROLLER)
│   ├── index.php                     # Bộ điều hướng trung tâm (Router) siêu gọn (~65 dòng)
│   ├── c_taikhoan.php                # Xử lý Đăng ký, Đăng nhập, Đăng xuất
│   ├── c_sanpham.php                 # Xử lý Trang chủ, Tìm kiếm, Phân trang, Chi tiết & Đánh giá
│   ├── c_giohang.php                 # Xử lý Giỏ hàng, Áp Voucher, Thanh toán & Gửi hóa đơn Mail
│   ├── c_donhang.php                 # Xử lý Lịch sử đơn hàng, Tiến trình giao hàng (Tracking) & Hủy đơn
│   └── c_quantri.php                 # Xử lý Quản trị: Đơn hàng, Voucher, Sale, Đánh giá, Khách hàng, Nhân viên, Doanh thu
│
├── model/                            # TẦNG DỮ LIỆU & TIỆN ÍCH (MODEL)
│   ├── database.php                  # Kết nối PDO & Cơ chế tự động khởi tạo / nâng cấp bảng (Auto-Migration)
│   ├── xl_database.php               # Các hàm truy vấn, lấy ID, tự động tính hạng thành viên VIP
│   └── mailer.php                    # Trình tạo & gửi hóa đơn điện tử HTML qua Email
│
├── view/                             # TẦNG GIAO DIỆN (VIEW & BOOTSTRAP 5)
│   ├── css/style.css                 # Giao diện hoa tươi, stepper tracking, biểu đồ doanh thu thuần CSS
│   ├── header.php, footer.php        # Header & Footer dùng chung
│   ├── dangnhap.php, dangky.php      # Giao diện tài khoản
│   ├── giohang.php, thanhtoan.php    # Giỏ hàng & Thanh toán
│   ├── chitietsp.php, dssanpham.php  # Chi tiết hoa & Danh sách sản phẩm
│   ├── lichsudonhang.php             # Lịch sử đơn hàng của người mua
│   ├── theodoigiaohang.php           # Quá trình giao hàng 5 bước
│   ├── tracuudonhang.php             # Tra cứu đơn hàng nhanh
│   ├── hoadon.php                    # Giao diện hóa đơn bán hàng & in ấn chuẩn MVC
│   └── admin_*.php                   # Toàn bộ màn hình quản trị của Admin & Staff
```

### 1. Phía Khách Hàng (USER)
- **Tìm kiếm & Phân trang**:
  - Tìm kiếm hoa theo từ khóa ngay trên thanh header hoặc trang danh sách sản phẩm.
  - Lọc theo danh mục, sắp xếp theo giá (tăng/giảm), sản phẩm mới nhất hoặc sản phẩm đang khuyến mãi.
  - Phân trang gọn gàng (8 sản phẩm/trang).
- **Chi tiết sản phẩm**:
  - Xem ảnh, giá gốc, giá khuyến mãi (nếu có), mô tả chi tiết, số lượng đặt mua.
  - Gợi ý các sản phẩm liên quan cùng danh mục.
- **Bình luận & Đánh giá sản phẩm**:
  - Chấm điểm sao (1 - 5 sao ⭐) và gửi nhận xét.
  - Tính điểm đánh giá trung bình và hiển thị danh sách đánh giá của khách hàng.
- **Mua hàng & Giỏ hàng**:
  - Thêm sản phẩm vào giỏ, nút "Mua ngay" chuyển thẳng sang thanh toán.
  - Tăng/giảm số lượng trực tiếp trong giỏ hàng, xóa từng món hoặc xóa tất cả.
- **Áp dụng Voucher giảm giá**:
  - Nhập mã voucher (theo % hoặc số tiền cố định), kiểm tra số lượng và hạn sử dụng.
  - Tính toán và trừ tiền giảm giá ngay lập tức trên hóa đơn tạm tính.
- **Thanh toán & Đặt hàng**:
  - Nhập thông tin người nhận: Họ tên, số điện thoại, email, địa chỉ, ghi chú viết thiệp.
  - Phương thức thanh toán: Thanh toán khi nhận hàng (COD) hoặc Chuyển khoản ngân hàng.
- **Gửi Email Hóa Đơn Tự Động**:
  - Tự động tạo và gửi hóa đơn chi tiết qua email cho Quản trị viên (`admin@bloomshop.vn`) và Khách hàng ngay khi đặt hàng thành công.
  - Xuất và lưu trữ hóa đơn điện tử HTML tại thư mục `/bloomshop/invoices/` cho phép xem và in ấn trực tiếp.
- **Xem lịch sử mua hàng**:
  - Danh sách các đơn hàng đã đặt kèm trạng thái, ngày đặt và tổng tiền.
  - Xem chi tiết từng đơn hàng và in hóa đơn bán hàng.
- **Xem quá trình giao hàng (Tracking Timeline)**:
  - Thanh tiến trình 5 bước trực quan:
    `Chờ xác nhận -> Xác nhận đơn hàng -> Đang giao -> Đã giao -> Thành công`
  - Nhật ký chi tiết từng mốc thời gian cập nhật.
- **Hủy mua hàng**:
  - Khách hàng có thể chủ động hủy đơn khi đơn hàng đang ở trạng thái "Chờ xác nhận". Hệ thống tự động hoàn lại lượt dùng voucher.
- **Tra cứu đơn hàng không cần đăng nhập**:
  - Tra cứu nhanh bằng Mã đơn hàng và Số điện thoại đặt hàng.

---

### 2. Phía Quản Trị Viên (ADMIN & STAFF)
- **Quản lý đơn hàng (Sửa trạng thái đơn hàng)**:
  - Lọc đơn hàng theo từng trạng thái.
  - Cập nhật quy trình chuẩn: `Chờ xác nhận -> Xác nhận đơn hàng -> Đang giao -> Đã giao -> Thành công` (hoặc Hủy đơn).
  - Tự động ghi nhật ký tracking hành trình tương ứng.
- **Quản lý khách hàng (Xem, Khóa) & Phân cấp bậc VIP**:
  - Theo dõi danh sách khách hàng, tổng chi tiêu tích lũy.
  - Phân hạng khách hàng:
    - 💎 **Diamond (Kim cương)**: Chi tiêu từ 5.000.000đ trở lên.
    - 🥇 **Gold (Vàng)**: Chi tiêu từ 1.000.000đ đến 5.000.000đ.
    - 🥈 **Silver (Bạc)**: Chi tiêu dưới 1.000.000đ.
  - Tự động nâng hạng khi đơn hàng chuyển sang trạng thái "Thành công".
  - Chức năng Khóa / Mở khóa tài khoản khách hàng (khách bị khóa sẽ không thể đăng nhập).
- **Quản lý Voucher (Số lượng, Thời hạn)**:
  - Thêm, sửa, xóa mã giảm giá.
  - Cài đặt hình thức giảm (% hoặc VNĐ), mức giảm, đơn tối thiểu, tổng số lượng phát hành, thời hạn sử dụng.
  - Theo dõi số lượng đã dùng và số lượng còn lại.
- **Khuyến mãi sản phẩm**:
  - Quản lý nhanh giá khuyến mãi (`sale_price`) và bật/tắt trạng thái khuyến mãi (`is_sale`).
  - Sản phẩm khuyến mãi hiển thị huy hiệu giảm giá `-X%` nổi bật trên toàn hệ thống.
- **Quản lý đánh giá**:
  - Xem toàn bộ nhận xét, lọc theo số sao (1-5 sao).
  - Bật/tắt ẩn hiện đánh giá hoặc xóa đánh giá vi phạm tiêu chuẩn.
- **Quản lý nhân viên (Thêm, Xóa, Sửa - Dành riêng cho Admin)**:
  - Thêm nhân viên mới, phân quyền `staff`.
  - Chỉnh sửa thông tin, đổi mật khẩu và xóa nhân viên.
- **Quản lý doanh thu (Dành riêng cho Admin)**:
  - Thống kê doanh thu theo **Tuần** (7 ngày gần nhất) kèm biểu đồ cột trực quan.
  - Thống kê doanh thu theo **Tháng** (12 tháng của năm hiện tại) kèm biểu đồ cột trực quan.
  - Thống kê doanh thu theo **Năm**.
  - Xếp hạng **Sản phẩm bán chạy nhất** (Top best-sellers theo số lượng bán và doanh số).
  - Xếp hạng **Sản phẩm ế nhất** (bán chậm hoặc 0 đơn bán) kèm gợi ý tạo khuyến mãi kích cầu.
- **Quản lý danh mục & sản phẩm**:
  - Thêm, sửa, xóa danh mục hoa và sản phẩm hoa tươi kèm hình ảnh.

---

## 🔐 Tài Khoản Trải Nghiệm Mẫu

| Vai trò | Email | Mật khẩu | Ghi chú |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@bloomshop.vn` | `admin123` | Toàn quyền quản trị hệ thống & xem doanh thu |
| **Nhân viên (Staff)** | `staff@bloomshop.vn` | `staff123` | Quản lý đơn hàng, sản phẩm, voucher, khách hàng |
| **Khách hàng Diamond** | `diamond@bloomshop.vn` | `123456` | Chi tiêu > 5.000.000đ (Hạng Kim Cương) |
| **Khách hàng Gold** | `gold@bloomshop.vn` | `123456` | Chi tiêu 1.000.000đ - 5.000.000đ (Hạng Vàng) |
| **Khách hàng Silver** | `silver@bloomshop.vn` | `123456` | Chi tiêu < 1.000.000đ (Hạng Bạc) |

---

## 🎟️ Mã Voucher Dùng Thử

- `BLOOM10`: Giảm 10% cho đơn từ 200.000đ
- `GIAM50K`: Giảm 50.000đ cho đơn từ 300.000đ
- `VIPMEMBER`: Giảm 100.000đ cho đơn từ 600.000đ
- `FREESHIP`: Giảm 30.000đ cho đơn từ 150.000đ

---

## 🚀 Hướng Dẫn Cài Đặt & Chạy Website

1. Khởi động **Apache** và **MySQL** trong **XAMPP Control Panel**.
2. Thư mục mã nguồn đặt tại: `d:\XAMPP\htdocs\bloomshop`.
3. Mở trình duyệt web và truy cập:
   ```
   http://localhost/bloomshop
   ```
4. **Hệ thống tự động kiểm tra và khởi tạo cơ sở dữ liệu** `bloomshop` cùng đầy đủ dữ liệu mẫu ban đầu (sản phẩm, tài khoản, voucher, đơn hàng mẫu).
   *(Bạn cũng có thể import trực tiếp file [bloomshop.sql](file:///d:/XAMPP/htdocs/bloomshop/bloomshop.sql) qua phpMyAdmin nếu muốn reset dữ liệu)*.
