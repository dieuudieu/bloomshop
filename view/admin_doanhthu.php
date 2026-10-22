<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1" style="color: #604b41;">
                <i class="bi bi-graph-up-arrow"></i> Báo cáo & Thống kê Doanh thu
            </h2>
            <p class="text-muted small mb-0">Phân tích hiệu quả kinh doanh, doanh thu tuần/tháng/năm và xếp hạng sản phẩm</p>
        </div>
        <div class="badge bg-light text-dark border p-2">
            Hôm nay: <?= date('d/m/Y') ?>
        </div>
    </div>

    <!-- TỔNG QUAN KPI THỐNG KÊ DOANH THU -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 bg-white rounded-3 border-start border-4 border-success">
                <div class="small text-muted fw-bold text-uppercase">Doanh thu Tuần này</div>
                <div class="fs-4 fw-bold text-success mt-1"><?= number_format($doanhthu_tuan, 0, ',', '.') ?>đ</div>
                <small class="text-muted">7 ngày gần nhất</small>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 bg-white rounded-3 border-start border-4 border-primary">
                <div class="small text-muted fw-bold text-uppercase">Doanh thu Tháng <?= date('m') ?></div>
                <div class="fs-4 fw-bold text-primary mt-1"><?= number_format($doanhthu_thang, 0, ',', '.') ?>đ</div>
                <small class="text-muted">Tháng <?= date('m/Y') ?></small>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 bg-white rounded-3 border-start border-4 border-warning">
                <div class="small text-muted fw-bold text-uppercase">Doanh thu Năm <?= date('Y') ?></div>
                <div class="fs-4 fw-bold text-warning mt-1"><?= number_format($doanhthu_nam, 0, ',', '.') ?>đ</div>
                <small class="text-muted">Toàn bộ năm <?= date('Y') ?></small>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 bg-white rounded-3 border-start border-4 border-danger">
                <div class="small text-muted fw-bold text-uppercase">Tổng tích lũy</div>
                <div class="fs-4 fw-bold price-text mt-1"><?= number_format($tong_doanhthu, 0, ',', '.') ?>đ</div>
                <small class="text-muted"><?= $tong_don_thanhcong ?> đơn thành công</small>
            </div>
        </div>
    </div>

    <!-- TABS XEM DOANH THU: TUẦN - THÁNG - NĂM -->
    <div class="card border-0 shadow-sm p-4 bg-white rounded-3 mb-5">
        <ul class="nav nav-pills mb-4" id="revenueTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-bold" id="week-tab" data-bs-toggle="pill" data-bs-target="#week-content" type="button">
                    <i class="bi bi-calendar-week me-1"></i> Doanh thu theo Tuần (7 ngày)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold" id="month-tab" data-bs-toggle="pill" data-bs-target="#month-content" type="button">
                    <i class="bi bi-calendar-month me-1"></i> Doanh thu theo Tháng (Năm <?= date('Y') ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold" id="year-tab" data-bs-toggle="pill" data-bs-target="#year-content" type="button">
                    <i class="bi bi-calendar3 me-1"></i> Doanh thu theo Năm
                </button>
            </li>
        </ul>

        <div class="tab-content" id="revenueTabsContent">
            <!-- TAB 1: DOANH THU THEO TUẦN -->
            <div class="tab-pane fade show active" id="week-content">
                <h5 class="fw-bold mb-3" style="color: #604b41;">Biểu đồ doanh thu 7 ngày qua</h5>
                
                <?php 
                    $max_val_week = 1;
                    foreach ($stats_7days as $d) {
                        if ($d['revenue'] > $max_val_week) $max_val_week = $d['revenue'];
                    }
                ?>
                <div class="chart-bar-container mb-4">
                    <?php foreach ($stats_7days as $day) { 
                        $pct = round(($day['revenue'] / $max_val_week) * 100);
                        if ($pct < 5 && $day['revenue'] > 0) $pct = 5;
                    ?>
                        <div class="chart-bar-item">
                            <div class="chart-bar-fill" style="height: <?= $pct ?>%;">
                                <?php if ($day['revenue'] > 0) { ?>
                                    <span class="chart-bar-val"><?= number_format($day['revenue'] / 1000, 0) ?>k</span>
                                <?php } ?>
                            </div>
                            <div class="chart-bar-label"><?= date('d/m', strtotime($day['date'])) ?></div>
                        </div>
                    <?php } ?>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped small align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Ngày</th>
                                <th class="text-center">Số đơn hàng</th>
                                <th class="text-end">Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats_7days as $day) { ?>
                                <tr>
                                    <td><?= date('d/m/Y (l)', strtotime($day['date'])) ?></td>
                                    <td class="text-center"><?= $day['orders_count'] ?> đơn</td>
                                    <td class="text-end fw-bold"><?= number_format($day['revenue'], 0, ',', '.') ?>đ</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: DOANH THU THEO THÁNG -->
            <div class="tab-pane fade" id="month-content">
                <h5 class="fw-bold mb-3" style="color: #604b41;">Doanh thu các tháng trong năm <?= date('Y') ?></h5>
                
                <?php 
                    $max_val_month = 1;
                    foreach ($stats_months as $m) {
                        if ($m['revenue'] > $max_val_month) $max_val_month = $m['revenue'];
                    }
                ?>
                <div class="chart-bar-container mb-4">
                    <?php foreach ($stats_months as $m) { 
                        $pct = round(($m['revenue'] / $max_val_month) * 100);
                        if ($pct < 5 && $m['revenue'] > 0) $pct = 5;
                    ?>
                        <div class="chart-bar-item">
                            <div class="chart-bar-fill" style="height: <?= $pct ?>%;">
                                <?php if ($m['revenue'] > 0) { ?>
                                    <span class="chart-bar-val"><?= number_format($m['revenue'] / 1000, 0) ?>k</span>
                                <?php } ?>
                            </div>
                            <div class="chart-bar-label">Th.<?= $m['month'] ?></div>
                        </div>
                    <?php } ?>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped small align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tháng</th>
                                <th class="text-center">Số đơn thành công</th>
                                <th class="text-end">Doanh thu tháng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats_months as $m) { ?>
                                <tr>
                                    <td>Tháng <?= $m['month'] ?> / <?= date('Y') ?></td>
                                    <td class="text-center"><?= $m['orders_count'] ?> đơn</td>
                                    <td class="text-end fw-bold"><?= number_format($m['revenue'], 0, ',', '.') ?>đ</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: DOANH THU THEO NĂM -->
            <div class="tab-pane fade" id="year-content">
                <h5 class="fw-bold mb-3" style="color: #604b41;">Doanh thu tổng hợp theo từng năm</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Năm</th>
                                <th class="text-center">Số lượng đơn hàng</th>
                                <th class="text-end">Tổng doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($stats_years)) { foreach ($stats_years as $y) { ?>
                                <tr>
                                    <td class="fw-bold fs-6">Năm <?= $y['year'] ?></td>
                                    <td class="text-center"><?= $y['orders_count'] ?> đơn</td>
                                    <td class="text-end fw-bold fs-6 price-text"><?= number_format($y['revenue'], 0, ',', '.') ?>đ</td>
                                </tr>
                            <?php }} else { ?>
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">Chưa có dữ liệu năm trước.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- XẾP HẠNG SẢN PHẨM: BÁN CHẠY NHẤT & Ế NHẤT -->
    <div class="row g-4">
        <!-- SẢN PHẨM BÁN CHẠY NHẤT -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 overflow-hidden">
                <div class="p-3 bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-fire me-1"></i> Top sản phẩm bán chạy nhất
                    </h5>
                    <span class="badge bg-light text-success fw-bold">Best Sellers</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Top</th>
                                <th>Sản phẩm</th>
                                <th class="text-center">Đã bán</th>
                                <th class="text-end">Doanh thu mang lại</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($sp_banchay)) { $rank = 1; foreach ($sp_banchay as $bc) { ?>
                                <tr>
                                    <td class="fw-bold text-center">
                                        <?php if ($rank == 1) { echo '🥇'; }
                                        elseif ($rank == 2) { echo '🥈'; }
                                        elseif ($rank == 3) { echo '🥉'; }
                                        else { echo '#' . $rank; } ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="/bloomshop/view/image/sanpham/<?= htmlspecialchars($bc['image']) ?>" width="45" height="45" class="rounded object-fit-cover" alt="" onerror="this.src='/bloomshop/view/image/banner.jpg'">
                                            <div>
                                                <a href="index.php?act=chitiettsp&id=<?= $bc['id'] ?>" class="text-dark fw-semibold small text-decoration-none">
                                                    <?= htmlspecialchars($bc['name']) ?>
                                                </a>
                                                <div class="small text-muted"><?= number_format($bc['price'], 0, ',', '.') ?>đ</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success fs-6"><?= $bc['total_sold'] ?></span>
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        <?= number_format($bc['total_revenue'], 0, ',', '.') ?>đ
                                    </td>
                                </tr>
                            <?php $rank++; }} else { ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Chưa có dữ liệu bán hàng.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SẢN PHẨM Ế NHẤT (BÁN CHẬM HOẶC CHƯA BÁN ĐƯỢC) -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 overflow-hidden">
                <div class="p-3 bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-hourglass-bottom me-1"></i> Sản phẩm bán chậm / ế nhất
                    </h5>
                    <span class="badge bg-light text-secondary fw-bold">Cần kích cầu</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Danh mục</th>
                                <th class="text-center">Đã bán</th>
                                <th class="text-end">Giá hiện tại</th>
                                <th class="text-center">Gợi ý</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($sp_enhat)) { foreach ($sp_enhat as $e) { ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="/bloomshop/view/image/sanpham/<?= htmlspecialchars($e['image']) ?>" width="45" height="45" class="rounded object-fit-cover" alt="" onerror="this.src='/bloomshop/view/image/banner.jpg'">
                                            <div>
                                                <a href="index.php?act=chitiettsp&id=<?= $e['id'] ?>" class="text-dark fw-semibold small text-decoration-none">
                                                    <?= htmlspecialchars($e['name']) ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="small text-muted"><?= htmlspecialchars($e['tendanhmuc'] ?? 'Hoa tươi') ?></td>
                                    <td class="text-center">
                                        <span class="badge <?= ($e['total_sold'] == 0) ? 'bg-danger' : 'bg-warning text-dark' ?>">
                                            <?= $e['total_sold'] ?> đã bán
                                        </span>
                                    </td>
                                    <td class="text-end small fw-semibold">
                                        <?= number_format($e['price'], 0, ',', '.') ?>đ
                                    </td>
                                    <td class="text-center">
                                        <a href="index.php?act=khuyenmai" class="btn btn-outline-danger btn-sm" title="Tạo khuyến mãi giảm giá">
                                            <i class="bi bi-tag"></i> Tạo Sale
                                        </a>
                                    </td>
                                </tr>
                            <?php }} else { ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Không có sản phẩm nào ế.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
