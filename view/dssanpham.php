<div class="container my-5">

    <h2 class="text-center mb-4">Tất cả sản phẩm</h2>

    <!-- FORM TÌM KIẾM VÀ LỌC DANH MỤC -->
    <form action="index.php" method="GET" class="row g-3 mb-4">

        <input type="hidden" name="act" value="sanpham">

        <!-- Tìm theo tên -->
        <div class="col-md-6">
            <input
                type="text"
                name="keyword"
                class="form-control"
                placeholder="Nhập tên sản phẩm cần tìm..."
                value="<?= htmlspecialchars($keyword) ?>"
            >
        </div>

        <!-- Lọc danh mục -->
        <div class="col-md-3">
            <select name="id_dm" class="form-select">

                <option value="0">Tất cả danh mục</option>

                <?php foreach ($danhmuc as $dm) { ?>

                    <option
                        value="<?= $dm['id'] ?>"
                        <?= $id_dm == $dm['id'] ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($dm['name']) ?>
                    </option>

                <?php } ?>

            </select>
        </div>

        <!-- Nút tìm kiếm -->
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">
                Tìm kiếm
            </button>
        </div>

    </form>

    <!-- SỐ LƯỢNG SẢN PHẨM -->
    <p class="text-muted mb-4">
        Tìm thấy <?= $tongSanPham ?> sản phẩm
    </p>

    <!-- HIỂN THỊ DANH SÁCH SẢN PHẨM -->
    <?php include __DIR__ . '/_product_cards.php'; ?>

    <!-- PHÂN TRANG -->
    <?php if ($tongTrang > 1) { ?>

        <nav class="mt-5">
            <ul class="pagination justify-content-center">

                <!-- Trang trước -->
                <?php if ($page > 1) { ?>
                    <li class="page-item">
                        <a
                            class="page-link"
                            href="index.php?<?= http_build_query([
                                'act' => 'sanpham',
                                'keyword' => $keyword,
                                'id_dm' => $id_dm,
                                'page' => $page - 1
                            ]) ?>"
                        >
                            Trước
                        </a>
                    </li>
                <?php } ?>

                <!-- Các số trang -->
                <?php for ($i = 1; $i <= $tongTrang; $i++) { ?>

                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">

                        <a
                            class="page-link"
                            href="index.php?<?= http_build_query([
                                'act' => 'sanpham',
                                'keyword' => $keyword,
                                'id_dm' => $id_dm,
                                'page' => $i
                            ]) ?>"
                        >
                            <?= $i ?>
                        </a>

                    </li>

                <?php } ?>

                <!-- Trang sau -->
                <?php if ($page < $tongTrang) { ?>
                    <li class="page-item">
                        <a
                            class="page-link"
                            href="index.php?<?= http_build_query([
                                'act' => 'sanpham',
                                'keyword' => $keyword,
                                'id_dm' => $id_dm,
                                'page' => $page + 1
                            ]) ?>"
                        >
                            Sau
                        </a>
                    </li>
                <?php } ?>

            </ul>
        </nav>

    <?php } ?>

</div>