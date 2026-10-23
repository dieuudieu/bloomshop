<?php
// model/mailer.php - Gửi email hóa đơn tự động bằng thư viện PHPMailer (theo mẫu d:\guiemail.php)

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Nạp các file thư viện PHPMailer từ D:\PHPMailer hoặc model/PHPMailer
if (file_exists(__DIR__ . '/PHPMailer/src/PHPMailer.php')) {
    require_once __DIR__ . '/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/src/SMTP.php';
} elseif (file_exists('d:/PHPMailer/src/PHPMailer.php')) {
    require_once 'd:/PHPMailer/src/Exception.php';
    require_once 'd:/PHPMailer/src/PHPMailer.php';
    require_once 'd:/PHPMailer/src/SMTP.php';
}

// ==============================================================================
// ⚙️ CẤU HÌNH GMAIL GỬI HÓA ĐƠN TỰ ĐỘNG (BẠN ĐIỀN THÔNG TIN TẠI ĐÂY)
// ==============================================================================

// 1. Điền địa chỉ Gmail của bạn vào đây (người gửi hóa đơn):
if (!defined('SMTP_USER')) define('SMTP_USER', 'dieutran141005@gmail.com');

// 2. Điền Mật khẩu ứng dụng Google (App Password - 16 ký tự tạo trong tài khoản Google):
//    Ví dụ: 'abcd efgh ijkl mnop'
if (!defined('SMTP_PASS')) define('SMTP_PASS', 'oulw nndd eiug chbe');

// 3. Tên cửa hàng hiển thị trong hộp thư người nhận:
if (!defined('SMTP_FROM_NAME')) define('SMTP_FROM_NAME', 'BloomShop Flowers');

// 4. Email Admin nhận thông báo mỗi khi có đơn hàng mới (hỗ trợ nhiều email cách nhau bằng dấu phẩy):
if (!defined('ADMIN_EMAIL')) define('ADMIN_EMAIL', 'tranphuocdieu1410@gmail.com, dn629691@gmail.com');

// ==============================================================================

class Mailer
{
    /**
     * Khởi tạo đối tượng PHPMailer cấu hình sẵn SMTP Gmail
     */
    private static function get_mailer_instance()
    {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = str_replace(' ', '', SMTP_PASS);
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
        return $mail;
    }

    /**
     * Gửi hóa đơn đơn hàng tự động đến Khách hàng và Quản trị viên bằng PHPMailer
     */
    public static function send_order_invoice($order, $items)
    {
        $order_id = $order['id'];
        $subject = "Hóa đơn xác nhận đơn hàng #$order_id - BloomShop";

        // Tạo nội dung bảng HTML chi tiết hóa đơn
        $html = self::generate_invoice_html($order, $items);

        $sent_customer = false;
        $sent_admin = false;

        // Kiểm tra xem đã cấu hình Gmail và Mật khẩu ứng dụng chưa
        $is_configured = (SMTP_USER !== 'email_cua_cac_ban@gmail.com' && SMTP_PASS !== 'app_password_16_ky_tu');

        if ($is_configured && class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            // 1. GỬI EMAIL XÁC NHẬN CHO KHÁCH HÀNG (Khối try-catch riêng biệt)
            $cust_email = trim($order['customer_email'] ?? '');
            if (!empty($cust_email) && filter_var($cust_email, FILTER_VALIDATE_EMAIL)) {
                try {
                    $mail_cust = self::get_mailer_instance();
                    $mail_cust->addAddress($cust_email, $order['customer_name'] ?? 'Khách hàng');
                    $mail_cust->isHTML(true);
                    $mail_cust->Subject = $subject;
                    $mail_cust->Body = $html;
                    $mail_cust->AltBody = 'Cảm ơn quý khách đã đặt hàng tại BloomShop. Đơn hàng #' . $order_id . ' đã được ghi nhận.';
                    
                    $mail_cust->send();
                    $sent_customer = true;
                } catch (\Throwable $e) {
                    error_log("PHPMailer Customer Send Error: " . $e->getMessage());
                }
            }

            // 2. GỬI EMAIL THÔNG BÁO CHO QUẢN TRỊ VIÊN (Khối try-catch riêng biệt, độc lập)
            if (!empty(ADMIN_EMAIL)) {
                try {
                    $mail_admin = self::get_mailer_instance();
                    $admin_list = array_map('trim', explode(',', ADMIN_EMAIL));
                    $has_recipient = false;

                    foreach ($admin_list as $adm_mail) {
                        if (filter_var($adm_mail, FILTER_VALIDATE_EMAIL)) {
                            $mail_admin->addAddress($adm_mail, 'Quản Trị Viên BloomShop');
                            $has_recipient = true;
                        }
                    }

                    if ($has_recipient) {
                        $mail_admin->isHTML(true);
                        $mail_admin->Subject = "[Thông báo đơn mới] Đơn hàng #$order_id từ " . ($order['customer_name'] ?? 'Khách hàng');
                        $mail_admin->Body = $html;
                        $mail_admin->AltBody = "Có đơn hàng mới #$order_id vừa được đặt trên hệ thống BloomShop.";

                        $mail_admin->send();
                        $sent_admin = true;
                    }
                } catch (\Throwable $e) {
                    error_log("PHPMailer Admin Send Error: " . $e->getMessage());
                }
            }
        }

        return [
            'sent_customer' => $sent_customer,
            'sent_admin'    => $sent_admin,
            'is_configured' => $is_configured
        ];
    }

    /**
     * Tạo mã HTML bảng hóa đơn gửi qua email
     */
    public static function generate_invoice_html($order, $items)
    {
        $items_html = '';
        foreach ($items as $item) {
            $price_fmt = number_format($item['price'], 0, ',', '.') . 'đ';
            $total_fmt = number_format($item['total'], 0, ',', '.') . 'đ';
            $items_html .= "
            <tr>
                <td style='padding: 10px; border-bottom: 1px solid #eee;'>
                    <strong>" . htmlspecialchars($item['product_name']) . "</strong>
                </td>
                <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: center;'>{$item['quantity']}</td>
                <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>{$price_fmt}</td>
                <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold;'>{$total_fmt}</td>
            </tr>";
        }

        $subtotal_fmt = number_format($order['subtotal'], 0, ',', '.') . 'đ';
        $discount_fmt = number_format($order['discount_amount'], 0, ',', '.') . 'đ';
        $total_fmt = number_format($order['total_price'], 0, ',', '.') . 'đ';
        $created_at = date('d/m/Y H:i', strtotime($order['created_at']));

        $voucher_line = "";
        if (!empty($order['voucher_code']) && $order['discount_amount'] > 0) {
            $voucher_line = "
            <tr>
                <td colspan='3' style='padding: 8px 10px; text-align: right; color: #28a745;'>Mã giảm giá (" . htmlspecialchars($order['voucher_code']) . "):</td>
                <td style='padding: 8px 10px; text-align: right; color: #28a745; font-weight: bold;'>-{$discount_fmt}</td>
            </tr>";
        }

        return "<!DOCTYPE html>
        <html lang='vi'>
        <head>
            <meta charset='UTF-8'>
            <title>Hóa đơn #{$order['id']} - BloomShop</title>
        </head>
        <body style='font-family: Arial, sans-serif; background-color: #f7f5f2; margin: 0; padding: 20px; color: #444;'>
            <div style='max-width: 650px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08);'>
                <div style='background: #a87768; color: #ffffff; padding: 25px; text-align: center;'>
                    <h1 style='margin: 0; font-size: 26px; letter-spacing: 1px;'>BloomShop</h1>
                    <p style='margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;'>Gửi yêu thương qua từng đóa hoa tươi</p>
                </div>
                
                <div style='padding: 25px;'>
                    <div style='border-bottom: 2px dashed #e2d9d2; padding-bottom: 15px; margin-bottom: 20px;'>
                        <h2 style='color: #604b41; margin: 0 0 10px 0; font-size: 20px;'>HÓA ĐƠN BÁN HÀNG #{$order['id']}</h2>
                        <p style='margin: 3px 0; font-size: 13px; color: #777;'>Ngày đặt hàng: {$created_at}</p>
                        <p style='margin: 3px 0; font-size: 13px; color: #777;'>Hình thức thanh toán: " . htmlspecialchars($order['payment_method']) . "</p>
                    </div>

                    <div style='margin-bottom: 20px;'>
                        <h3 style='color: #604b41; font-size: 16px; margin: 0 0 10px 0;'>Thông tin nhận hàng</h3>
                        <p style='margin: 4px 0; font-size: 14px;'><strong>Khách hàng:</strong> " . htmlspecialchars($order['customer_name']) . "</p>
                        <p style='margin: 4px 0; font-size: 14px;'><strong>Số điện thoại:</strong> " . htmlspecialchars($order['customer_phone']) . "</p>
                        <p style='margin: 4px 0; font-size: 14px;'><strong>Email:</strong> " . htmlspecialchars($order['customer_email']) . "</p>
                        <p style='margin: 4px 0; font-size: 14px;'><strong>Địa chỉ giao:</strong> " . htmlspecialchars($order['customer_address']) . "</p>
                        " . (!empty($order['note']) ? "<p style='margin: 4px 0; font-size: 14px; color: #888;'><em>Ghi chú: " . htmlspecialchars($order['note']) . "</em></p>" : "") . "
                    </div>

                    <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px;'>
                        <thead>
                            <tr style='background: #fdfaf7; color: #604b41; border-bottom: 2px solid #ecdcd3;'>
                                <th style='padding: 10px; text-align: left;'>Sản phẩm</th>
                                <th style='padding: 10px; text-align: center; width: 60px;'>SL</th>
                                <th style='padding: 10px; text-align: right; width: 110px;'>Đơn giá</th>
                                <th style='padding: 10px; text-align: right; width: 110px;'>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$items_html}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan='3' style='padding: 12px 10px 6px; text-align: right;'>Tạm tính:</td>
                                <td style='padding: 12px 10px 6px; text-align: right; font-weight: bold;'>{$subtotal_fmt}</td>
                            </tr>
                            {$voucher_line}
                            <tr style='font-size: 16px; color: #a87768;'>
                                <td colspan='3' style='padding: 10px; text-align: right; font-weight: bold;'>TỔNG THANH TOÁN:</td>
                                <td style='padding: 10px; text-align: right; font-weight: bold; font-size: 18px;'>{$total_fmt}</td>
                            </tr>
                        </tfoot>
                    </table>

                    <div style='background: #fbf8f5; border: 1px solid #fae6dc; border-radius: 6px; padding: 15px; text-align: center; margin-top: 25px;'>
                        <p style='margin: 0; font-size: 14px; color: #604b41; font-weight: bold;'>Cảm ơn quý khách đã tin tưởng chọn hoa tại BloomShop!</p>
                        <p style='margin: 5px 0 0; font-size: 12px; color: #999;'>Hotline hỗ trợ: 0901.234.567 | Website: http://localhost/bloomshop</p>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    }
}
