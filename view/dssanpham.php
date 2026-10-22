<div class="container my-5">
    <!-- Breadcrumb & Tiêu đề -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1">Bộ sưu tập hoa tươi</h2>
            <p class="text-muted small mb-0">Tìm thấy <?= $total_products ?? count($sanpham) ?> sản phẩm</p>
        </div>
        
        <!-- Bộ lọc và sắp xếp -->
        <form method="GET" action="index.php" class="d-flex align-items-center gap-2 flex-wrap">
            <input type="hidden" name="act" value="sanpham">
            <?php if (!empty($_GET['q'])) { ?>
                <input type="hidden" name="q" value="<?= htmlspecialchars($_GET['q']) ?>">
            <?php } ?>

            <select name="id_dm" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">Tất cả danh mục</option>
                <?php if (!empty($danhmuc)) { foreach ($danhmuc as $dm) { ?>
                    <option value="<?= $dm['id'] ?>" <?= (isset($_GET['id_dm']) && $_GET['id_dm'] == $dm['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dm['name']) ?>
                    </option>
                <?php }} ?>
            </select>

            <select name="sort" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="newest" <?= (($_GET['sort'] ?? '') == 'newest') ? 'selected' : '' ?>>Mới nhất</option>
                <option value="price_asc" <?= (($_GET['sort'] ?? '') == 'price_asc') ? 'selected' : '' ?>>Giá tăng dần</option>
                <option value="price_desc" <?= (($_GET['sort'] ?? '') == 'price_desc') ? 'selected' : '' ?>>Giá giảm dần</option>
                <option value="sale" <?= (($_GET['sort'] ?? '') == 'sale') ? 'selected' : '' ?>>Đang khuyến mãi</option>
            </select>
        </form>
    </div>

    <!-- Danh sách sản phẩm -->
    <?php include __DIR__ . '/_product_cards.php'; ?>

    <!-- Phân trang -->
    <?php if (isset($total_pages) && $total_pages > 1) { ?>
        <nav class="mt-5">
            <ul class="pagination justify-content-center">
                <?php 
                    $query_params = $_GET; 
                ?>
                <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                    <?php $query_params['page'] = $current_page - 1; ?>
                    <a class="page-link" href="index.php?<?= http_build_query($query_params) ?>">« Trước</a>
                </li>

                <?php for ($i = 1; $i <= $total_pages; $i++) { 
                    $query_params['page'] = $i;
                ?>
                    <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                        <a class="page-link" href="index.php?<?= http_build_query($query_params) ?>"><?= $i ?></a>
                    </li>
                <?php } ?>

                <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                    <?php $query_params['page'] = $current_page + 1; ?>
                    <a class="page-link" href="index.php?<?= http_build_query($query_params) ?>">Sau »</a>
                </li>
            </ul>
        </nav>
    <?php } ?>
</div>
