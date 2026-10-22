<div class="container my-5" style="max-width: 850px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?act=lichsudonhang">Lịch sử mua hàng</a></li>
            <li class="breadcrumb-item active">Quá trình giao hàng #<?= $order['id'] ?></li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="fw-bold mb-0" style="color: #604b41;">
            <i class="bi bi-geo-alt-fill text-bloom"></i> Quá trình giao hàng #<?= $order['id'] ?>
        </h2>
        <a href="index.php?act=chitietdonhang&id=<?= $order['id'] ?>" class="btn btn-outline-secondary btn-sm">
            Xem chi tiết đơn
        </a>
    </div>

    <!-- Stepper 5 bước theo yêu cầu -->
    <div class="card border-0 shadow-sm p-4 mb-4 bg-white rounded-4">
        <?php 
            $st = $order['status'];
            // Thứ tự các bước:
            // 1: cho_xac_nhan
            // 2: xac_nhan
            // 3: dang_giao
            // 4: da_giao
            // 5: thanh_cong
            $steps = [
                'cho_xac_nhan' => ['title' => 'Chờ xác nhận', 'icon' => 'bi-hourglass-split', 'index' => 1],
                'xac_nhan'     => ['title' => 'Xác nhận đơn', 'icon' => 'bi-clipboard-check', 'index' => 2],
                'dang_giao'    => ['title' => 'Đang giao', 'icon' => 'bi-truck', 'index' => 3],
                'da_giao'      => ['title' => 'Đã giao', 'icon' => 'bi-box-seam', 'index' => 4],
                'thanh_cong'   => ['title' => 'Thành công', 'icon' => 'bi-check2-circle', 'index' => 5],
            ];

            $current_index = $steps[$st]['index'] ?? 0;
            $is_cancelled = ($st == 'da_huy');
        ?>

        <?php if ($is_cancelled) { ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-0">
                <i class="bi bi-x-circle-fill fs-3"></i>
                <div>
                    <strong>Đơn hàng này đã bị hủy!</strong>
                    <div class="small">Đơn hàng không tiếp tục quá trình giao hàng.</div>
                </div>
            </div>
        <?php } else { ?>
            <div class="tracking-stepper">
                <?php foreach ($steps as $step_key => $step_info) { 
                    $cls = '';
                    if ($step_info['index'] < $current_index) {
                        $cls = 'completed';
                    } elseif ($step_info['index'] == $current_index) {
                        $cls = 'active';
                    }
                ?>
                    <div class="tracking-step <?= $cls ?>">
                        <div class="step-icon">
                            <i class="bi <?= $step_info['icon'] ?>"></i>
                        </div>
                        <div class="step-title"><?= $step_info['title'] ?></div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

    <!-- Chi tiết từng mốc thời gian giao hàng (Tracking Logs) -->
    <div class="card border-0 shadow-sm p-4 bg-white rounded-3">
        <h5 class="fw-bold mb-4" style="color: #604b41;">
            <i class="bi bi-clock-history"></i> Nhật ký hành trình đơn hàng
        </h5>

        <?php if (!empty($tracking_logs)) { ?>
            <ul class="list-group list-group-flush">
                <?php foreach ($tracking_logs as $log) { ?>
                    <li class="list-group-item px-0 py-3 d-flex gap-3 align-items-start">
                        <span class="badge bg-light text-bloom border p-2 rounded-circle mt-1">
                            <i class="bi bi-check2"></i>
                        </span>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <strong class="text-dark"><?= htmlspecialchars($log['description']) ?></strong>
                                <small class="text-muted"><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></small>
                            </div>
                            <small class="text-secondary">Trạng thái: <code><?= htmlspecialchars($log['status']) ?></code></small>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <p class="text-muted mb-0">Chưa có thông tin cập nhật hành trình cho đơn hàng này.</p>
        <?php } ?>
    </div>
</div>
