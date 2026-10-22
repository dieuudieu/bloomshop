<?php
// model/mailer.php - Hỗ trợ xuất và gửi email hóa đơn đơn hàng

class Mailer
{
    public static function send_order_invoice($order, $items)
    {
        $admin_email = 'admin@bloomshop.vn';
        $order_id = $order['id'];
        $subject = "[BloomShop] Hóa đơn xác nhận đơn hàng #$order_id";

        // Tạo nội dung HTML hóa đơn
        $html = self::generate_invoice_html($order, $items);

        // Lưu bản sao hóa đơn HTML vào thư mục invoices để xem trực tiếp / in ấn
        $invoice_dir = __DIR__ . '/../invoices';
        if (!is_dir($invoice_dir)) {
            @mkdir($invoice_dir, 0777, true);
        }
        $invoice_file = $invoice_dir . "/hoa_don_{$order_id}.html";
        @file_put_contents($invoice_file, $html);

        // Thiết lập header gửi mail HTML tiếng Việt
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: BloomShop <noreply@bloomshop.vn>\r\n";
        $headers .= "Reply-To: noreply@bloomshop.vn\r\n";

        // Gửi mail cho Admin và Khách hàng (sử dụng @ để không crash nếu localhost XAMPP chưa cấu hình sendmail)
        $sent_admin = @mail($admin_email, $subject . " (Thông báo cho Quản trị viên)", $html, $headers);
        $sent_customer = false;
        if (!empty($order['customer_email'])) {
            $sent_customer = @mail($order['customer_email'], $subject, $html, $headers);
        }

        return [
            'sent_admin' => $sent_admin,
            'sent_customer' => $sent_customer,
            'invoice_file' => "invoices/hoa_don_{$order_id}.html"
        ];
    }

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
