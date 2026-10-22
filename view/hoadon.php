<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hóa đơn bán hàng #<?= $order['id'] ?> - BloomShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f5f2;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            color: #444;
        }
        .invoice-box {
            max-width: 800px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .invoice-header {
            background: #a87768;
            color: #ffffff;
            padding: 30px;
        }
        .invoice-body {
            padding: 35px;
        }
        .price-color {
            color: #a87768;
        }
        @media print {
            body {
                background: #ffffff;
            }
            .invoice-box {
                margin: 0;
                box-shadow: none;
                border: none;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
            .invoice-header {
                background: #f5efe8 !important;
                color: #604b41 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Nút điều hướng không in ra giấy -->
    <div class="d-flex justify-content-between align-items-center max-width-box my-4 no-print" style="max-width: 800px; margin: 20px auto 0;">
        <button onclick="window.history.back()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Quay lại
        </button>
        <button onclick="window.print()" class="btn btn-success btn-sm px-3 fw-bold">
            <i class="bi bi-printer-fill me-1"></i> In hóa đơn này
        </button>
    </div>

    <div class="invoice-box">
        <!-- Header Hóa đơn -->
        <div class="invoice-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="fw-bold mb-1" style="letter-spacing: 1px;">BloomShop</h2>
                <div class="small opacity-75">Gửi yêu thương qua từng đóa hoa tươi</div>
                <div class="small opacity-75">Hotline: 0901.234.567 | Website: bloomshop.vn</div>
            </div>
            <div class="text-end">
                <h4 class="mb-1 text-uppercase fw-bold">Hóa Đơn Bán Hàng</h4>
                <div class="fs-5 fw-bold">#<?= $order['id'] ?></div>
                <div class="small opacity-75">Ngày: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
            </div>
        </div>

        <div class="invoice-body">
            <!-- Thông tin khách hàng & Phương thức thanh toán -->
            <div class="row g-3 mb-4 pb-3 border-bottom">
                <div class="col-sm-7">
                    <h6 class="fw-bold text-uppercase text-muted small mb-2">Khách hàng nhận hoa</h6>
                    <div class="fw-bold fs-6 text-dark"><?= htmlspecialchars($order['customer_name']) ?></div>
                    <div><i class="bi bi-telephone text-muted me-1"></i> <?= htmlspecialchars($order['customer_phone']) ?></div>
                    <div><i class="bi bi-envelope text-muted me-1"></i> <?= htmlspecialchars($order['customer_email']) ?></div>
                    <div class="text-secondary small mt-1"><i class="bi bi-geo-alt text-muted me-1"></i> <?= htmlspecialchars($order['customer_address']) ?></div>
                    <?php if (!empty($order['note'])) { ?>
                        <div class="small text-muted fst-italic mt-1">Ghi chú: <?= htmlspecialchars($order['note']) ?></div>
                    <?php } ?>
                </div>
                <div class="col-sm-5 text-sm-end">
                    <h6 class="fw-bold text-uppercase text-muted small mb-2">Thông tin thanh toán</h6>
                    <div>Hình thức: <strong><?= htmlspecialchars($order['payment_method']) ?></strong></div>
                    <div>Trạng thái: 
                        <?php 
                            $st = $order['status'];
                            $st_badge = 'bg-secondary'; $st_name = $st;
                            if ($st == 'cho_xac_nhan') { $st_badge = 'bg-warning text-dark'; $st_name = 'Chờ xác nhận'; }
                            elseif ($st == 'xac_nhan') { $st_badge = 'bg-info text-dark'; $st_name = 'Đã xác nhận'; }
                            elseif ($st == 'dang_giao') { $st_badge = 'bg-primary'; $st_name = 'Đang giao'; }
                            elseif ($st == 'da_giao') { $st_badge = 'bg-info text-white'; $st_name = 'Đã giao'; }
                            elseif ($st == 'thanh_cong') { $st_badge = 'bg-success'; $st_name = 'Thành công'; }
                            elseif ($st == 'da_huy') { $st_badge = 'bg-danger'; $st_name = 'Đã hủy'; }
                        ?>
                        <span class="badge <?= $st_badge ?>"><?= $st_name ?></span>
                    </div>
                </div>
            </div>

            <!-- Bảng danh sách sản phẩm -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th class="text-center" style="width: 50px;">STT</th>
                            <th>Tên hoa tươi</th>
                            <th class="text-center" style="width: 80px;">SL</th>
                            <th class="text-end" style="width: 140px;">Đơn giá</th>
                            <th class="text-end" style="width: 140px;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($order_items as $item) { ?>
                            <tr>
                                <td class="text-center"><?= $i++ ?></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($item['product_name']) ?></td>
                                <td class="text-center"><?= $item['quantity'] ?></td>
                                <td class="text-end"><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                <td class="text-end fw-bold"><?= number_format($item['total'], 0, ',', '.') ?>đ</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-semibold">Tạm tính:</td>
                            <td class="text-end fw-semibold"><?= number_format($order['subtotal'], 0, ',', '.') ?>đ</td>
                        </tr>
                        <?php if ($order['discount_amount'] > 0) { ?>
                            <tr class="text-success">
                                <td colspan="4" class="text-end">Mã giảm giá (<?= htmlspecialchars($order['voucher_code']) ?>):</td>
                                <td class="text-end fw-bold">-<?= number_format($order['discount_amount'], 0, ',', '.') ?>đ</td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="4" class="text-end fs-6 fw-bold text-dark">TỔNG THANH TOÁN:</td>
                            <td class="text-end fs-5 fw-bold price-color"><?= number_format($order['total_price'], 0, ',', '.') ?>đ</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Lời cảm ơn chân thành -->
            <div class="text-center py-3 bg-light rounded-3 border">
                <p class="fw-bold mb-1" style="color: #604b41;">Cảm ơn quý khách đã tin tưởng và đồng hành cùng BloomShop!</p>
                <small class="text-muted">Mọi thắc mắc về đơn hàng, quý khách vui lòng liên hệ hotline 0901.234.567 để được hỗ trợ 24/7.</small>
            </div>
        </div>
    </div>
</div>

</body>
</html>
