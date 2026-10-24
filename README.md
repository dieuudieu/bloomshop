# BloomShop - Hệ Thống Website Bán Hoa Tươi Trực Tuyến (PHP OOP & MVC)

Dự án website bán hoa tươi trực tuyến được xây dựng theo chuẩn **Kiến trúc MVC (Model - View - Controller)** và **Lập trình Hướng đối tượng (OOP)** bằng **PHP thuần & MySQL**. Mã nguồn được tổ chức trong sáng, mạch lạc, dễ hiểu, không có file thừa và sẵn sàng để báo cáo / chấm điểm đồ án.

---

## 🔐 Tài Khoản Trải Nghiệm Mẫu

| Vai trò | Email | Mật khẩu | Quyền hạn |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@bloomshop.vn` | `admin123` | Toàn quyền quản trị, thêm nhân viên & xem báo cáo doanh thu |
| **Nhân viên (Staff)** | `staff@bloomshop.vn` | `staff123` | Quản lý đơn hàng, sản phẩm, voucher, khách hàng, đánh giá |
| **Khách hàng Diamond** | `diamond@bloomshop.vn` | `123456` | Chi tiêu > 5.000.000đ (Phân hạng Kim Cương) |
| **Khách hàng Gold** | `gold@bloomshop.vn` | `123456` | Chi tiêu 1.000.000đ - 5.000.000đ (Phân hạng Vàng) |
| **Khách hàng Silver** | `silver@bloomshop.vn` | `123456` | Chi tiêu < 1.000.000đ (Phân hạng Bạc) |

---

## 🎟️ Mã Voucher Dùng Thử

- `BLOOM10`: Giảm 10% cho đơn từ 200.000đ
- `GIAM50K`: Giảm 50.000đ cho đơn từ 300.000đ
- `VIPMEMBER`: Giảm 100.000đ cho đơn từ 600.000đ
- `FREESHIP`: Giảm 30.000đ cho đơn từ 150.000đ

---

## 🏛️ Kiến Trúc Hệ Thống (Mô Hình MVC & OOP)

### 1. Luồng xử lý một Request (Request Lifecycle)

```
[ Người dùng / Trình duyệt ]
            │  gửi request (ví dụ: index.php?act=giohang)
            ▼
┌────────────────────────────────────────────────────────┐
│  Router / Front Controller (index.php ở thư mục gốc)    │
│  - Khởi động Session                                   │
│  - Nạp các Model & Controller                          │
│  - Kiểm tra Authentication / Authorization Middleware  │
│  - Điều phối đến Controller tương ứng                  │
└───────────────────────────┬────────────────────────────┘
                            │ (new GioHangController())->xemGioHang()
                            ▼
┌────────────────────────────────────────────────────────┐
│  Controller Layer (controller/*Controller.php)         │
│  - Tiếp nhận input từ Request ($_GET, $_POST)          │
│  - Gọi Model lấy / ghi dữ liệu                         │
│  - Gọi $this->renderView('giohang.php', $data)         │
└─────────────┬───────────────────────────▲──────────────┘
              │                           │
   Gọi method │                           │ Trả về dữ liệu
              ▼                           │
┌──────────────────────────────┐          │
│  Model Layer (model/*Model)  │──────────┘
│  - Kế thừa BaseModel         │
│  - Truy vấn CSDL qua PDO     │
│  - Prepared Statements       │
└──────────────────────────────┘
              │
              │ Controller truyền $data sang View
              ▼
┌────────────────────────────────────────────────────────┐
│  View Layer (view/*.php)                               │
│  - Nạp header.php, nội dung trang, footer.php          │
│  - Chỉ hiển thị dữ liệu (KHÔNG viết SQL trong View)    │
│  - Trả về mã HTML hoàn chỉnh cho trình duyệt           │
└────────────────────────────────────────────────────────┘
```

---

### 2. Cấu trúc thư mục dự án

```
bloomshop/
├── index.php                      # [Front Controller / Router] Điểm vào duy nhất của toàn bộ hệ sinh thái
├── .htaccess                      # Cấu hình bảo mật URL và chặn xem cây thư mục
├── bloomshop.sql                  # CSDL MySQL (tự động cập nhật khi thao tác)
├── README.md                      # Tài liệu hướng dẫn & giải thích luồng xử lý
│
├── controller/                    # [CONTROLLER LAYER - ĐIỀU HƯỚNG THEO OOP]
│   ├── BaseController.php         # Lớp cơ sở cung cấp renderView(), redirect(), flash messages, post(), get()
│   ├── SanPhamController.php      # Kế thừa BaseController: Trang chủ, Danh sách hoa, Chi tiết, Gửi đánh giá
│   ├── GioHangController.php      # Kế thừa BaseController: Thêm/sửa/xóa giỏ, Áp voucher, Đặt hàng checkout
│   ├── DonHangController.php      # Kế thừa BaseController: Lịch sử đơn, Tracking 5 bước, Tra cứu, In hóa đơn
│   ├── TaiKhoanController.php     # Kế thừa BaseController: Đăng ký, Đăng nhập, Đăng xuất
│   ├── QuanTriController.php      # Kế thừa BaseController: Quản lý Danh mục, Sản phẩm, Đơn hàng, Voucher, Nhân viên, Doanh thu
│   └── index.php                  # Chuyển tiếp an toàn về ../index.php
│
├── model/                         # [MODEL LAYER - DỮ LIỆU & NGHIỆP VỤ THEO OOP]
│   ├── config.php                 # File cấu hình CSDL đa môi trường (Localhost & Hosting)
│   ├── database.php               # Quản lý kết nối CSDL qua PDO, tự động tạo bảng nếu chưa có
│   ├── BaseModel.php              # Kế thừa database: queryAll(), queryOne(), execute(), insertGetId(), recalculate_user_tier()
│   ├── SanPhamModel.php           # Kế thừa BaseModel: Thao tác bảng sanpham và danhgia
│   ├── DanhMucModel.php           # Kế thừa BaseModel: Thao tác bảng danhmuc
│   ├── DonHangModel.php           # Kế thừa BaseModel: Thao tác bảng donhang, chitiet_donhang, order_tracking
│   ├── GioHangModel.php           # Đóng gói toàn bộ nghiệp vụ giỏ hàng Shopping Cart lưu Session
│   ├── TaiKhoanModel.php          # Kế thừa BaseModel: Thao tác bảng users (Khách hàng & Nhân viên)
│   ├── ThongKeModel.php           # Kế thừa BaseModel: Thống kê doanh thu, báo cáo ngày/tháng/năm, hoa bán chạy/bán ế
│   ├── VoucherModel.php           # Kế thừa BaseModel: Thao tác bảng vouchers (mã giảm giá, hạn dùng, lượt dùng)
│   ├── mailer.php                 # Lớp Mailer static gửi email hóa đơn tự động qua PHPMailer
│   └── PHPMailer/                 # Thư viện gửi mail chuẩn quốc tế
│
└── view/                          # [VIEW LAYER - GIAO DIỆN HIỂN THỊ]
    ├── header.php                 # Header dùng chung (Menu, Giỏ hàng, Tìm kiếm, Phân quyền)
    ├── footer.php                 # Footer chân trang
    ├── trangchu.php               # Trang chủ hiển thị hoa tươi nổi bật
    ├── dssanpham.php              # Danh sách hoa tươi, bộ lọc theo danh mục, sắp xếp giá, phân trang
    ├── chitietsp.php              # Chi tiết hoa tươi, hoa cùng loại, đánh giá & bình luận
    ├── giohang.php                # Giao diện giỏ hàng, áp dụng mã voucher
    ├── thanhtoan.php              # Form nhập địa chỉ nhận hàng & chọn phương thức thanh toán
    ├── dathangthanhcong.php       # Màn hình chúc mừng đặt hàng thành công
    ├── lichsudonhang.php          # Lịch sử các đơn hàng của khách hàng đã mua
    ├── chitietdonhang.php         # Chi tiết một đơn hàng cụ thể
    ├── theodoigiaohang.php        # Stepper 5 bước theo dõi hành trình đơn hàng (Tracking timeline)
    ├── tracuudonhang.php          # Tra cứu nhanh bằng mã đơn hàng và số điện thoại
    ├── hoadon.php                 # Mẫu phiếu hóa đơn bán hàng chuẩn in ấn
    ├── sanpham.php                # Trang quản trị danh sách sản phẩm (Admin/Staff)
    ├── danhmuc.php                # Trang quản trị danh mục hoa (Admin/Staff)
    ├── themsp.php / suasp.php     # Form thêm và cập nhật hoa tươi kèm upload ảnh
    ├── themdm.php / suadm.php     # Form thêm và sửa danh mục hoa
    ├── admin_donhang.php          # Quản lý đơn hàng, đổi trạng thái theo State Machine
    ├── admin_voucher.php          # Quản lý mã giảm giá voucher
    ├── admin_themvoucher.php      # Form thêm mã giảm giá
    ├── admin_suavoucher.php       # Form sửa mã giảm giá
    ├── admin_khuyenmai.php        # Cập nhật nhanh giá khuyến mãi sản phẩm
    ├── admin_danhgia.php          # Quản lý, kiểm duyệt đánh giá của khách
    ├── admin_khachhang.php        # Danh sách khách hàng, cấp bậc VIP, khóa tài khoản
    ├── admin_nhanvien.php         # Quản lý tài khoản nhân viên (Admin only)
    ├── admin_themnv.php           # Thêm nhân viên mới (Admin only)
    ├── admin_suanv.php            # Sửa thông tin nhân viên (Admin only)
    ├── admin_doanhthu.php         # Biểu đồ và KPI doanh thu theo tuần/tháng/năm (Admin only)
    ├── dangky.php                 # Form đăng ký tài khoản khách hàng
    ├── dangnhap.php               # Form đăng nhập hệ thống
    ├── css/style.css              # Tệp định kiểu CSS tùy biến
    └── image/                     # Thư mục chứa banner và ảnh sản phẩm hoa tươi
```

---

## 🎯 Cách Trả Lời Vấn Đáp Giảng Viên Về MVC & OOP

1. **Câu hỏi: Mô hình MVC trong đồ án được tổ chức như thế nào?**
   - **Trả lời:** Dự án tuân thủ chặt chẽ 3 tầng:
     - **Model (M)**: Đặt trong thư mục `model/`, kế thừa `BaseModel`. Chỉ chịu trách nhiệm kết nối CSDL qua PDO Prepared Statements, lấy và lưu dữ liệu. Tuyệt đối không chứa HTML hay lệnh điều hướng trang.
     - **View (V)**: Đặt trong thư mục `view/`, chỉ chứa HTML, CSS, JavaScript và cú pháp PHP ngắn để in dữ liệu (`<?= $bien ?>`). Tuyệt đối không viết câu lệnh SQL trong View.
     - **Controller (C)**: Đặt trong thư mục `controller/`, kế thừa `BaseController`. Đóng vai trò cầu nối: nhận request từ người dùng, gọi Model lấy dữ liệu, rồi truyền dữ liệu sang View bằng `$this->renderView('ten_view.php', $data)`.
     - **Front Controller (Router)**: Đặt tại file `index.php` ở thư mục gốc, tiếp nhận tham số `act` qua URL và điều phối đến Controller tương ứng.

2. **Câu hỏi: Tính kế thừa (Inheritance) và Đóng gói (Encapsulation) trong OOP thể hiện ở đâu?**
   - **Tính kế thừa:**
     - Các Controller (`SanPhamController`, `GioHangController`, `DonHangController`, `TaiKhoanController`, `QuanTriController`) đều kế thừa từ `BaseController` để tái sử dụng các phương thức chung: `renderView()`, `redirect()`, `setFlashError()`, `setFlashSuccess()`, `isPost()`, `post()`, `get()`.
     - Các Model (`SanPhamModel`, `DanhMucModel`, `DonHangModel`, `TaiKhoanModel`, `ThongKeModel`, `VoucherModel`) đều kế thừa từ `BaseModel`, và `BaseModel` kế thừa từ `database` để tái sử dụng kết nối PDO và các phương thức `queryAll()`, `queryOne()`, `execute()`, `insertGetId()`.
   - **Tính đóng gói:**
     - Thuộc tính kết nối CSDL và các biến trạng thái được bảo vệ (`private`, `protected`). Các thao tác nghiệp vụ (như tính toán giảm giá voucher, tính điểm sao trung bình, cập nhật trạng thái đơn hàng theo luồng khép kín) đều được đóng gói thành các phương thức riêng biệt trong Model và Controller.
