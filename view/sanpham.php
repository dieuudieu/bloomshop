<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1" style="color: #604b41;">
                <i class="bi bi-flower1"></i> Quản lý sản phẩm hoa
            </h2>
            <p class="text-muted small mb-0">Xem danh sách, thêm hoa mới, sửa thông tin và giá khuyến mãi (Ràng buộc toàn vẹn hóa đơn)</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="index.php?act=dongbodulieu" class="btn btn-outline-primary" title="Cập nhật toàn bộ CSDL ra bloomshop.sql sẵn sàng nạp lên Hosting">
                <i class="bi bi-cloud-arrow-up me-1"></i> Đồng bộ SQL Hosting
            </a>
            <a href="index.php?act=khuyenmai" class="btn btn-bloom-outline">
                <i class="bi bi-tag-fill me-1"></i> Quản lý khuyến mãi
            </a>
            <a href="index.php?act=themsp" class="btn btn-success">
                <i class="bi bi-plus-circle me-1"></i> + Thêm sản phẩm
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá gốc</th>
                        <th>Giá khuyến mãi</th>
                        <th>Trạng thái Sale</th>
                        <th class="text-center">Đơn hàng liên kết</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($danhsach)) { foreach ($danhsach as $sp) { 
                        $has_sale = (!empty($sp['is_sale']) && !empty($sp['sale_price']));
                        $orderCount = (int)($sp['total_orders'] ?? 0);
                    ?>
                        <tr>
                            <td>#<?= $sp['id'] ?></td>
                            <td>
                                <img width="65" height="65" class="rounded object-fit-cover border" src="<?= $asset_dir ?>/image/sanpham/<?= htmlspecialchars($sp['image']) ?>" alt="" onerror="this.src='<?= $asset_dir ?>/image/banner.jpg'">
                            </td>
                            <td>
                                <a href="index.php?act=chitiettsp&id=<?= $sp['id'] ?>" class="text-dark fw-semibold text-decoration-none">
                                    <?= htmlspecialchars($sp['name']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border"><?= htmlspecialchars($sp['tendanhmuc'] ?? 'Chưa phân loại') ?></span>
                            </td>
                            <td class="fw-semibold"><?= number_format($sp['price'], 0, ',', '.') ?>đ</td>
                            <td>
                                <?php if ($has_sale) { ?>
                                    <span class="fw-bold text-danger"><?= number_format($sp['sale_price'], 0, ',', '.') ?>đ</span>
                                <?php } else { ?>
                                    <span class="text-muted small">-</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($has_sale) { ?>
                                    <span class="badge bg-danger">Đang Sale</span>
                                <?php } else { ?>
                                    <span class="badge bg-light text-muted border">Bình thường</span>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <?php if ($orderCount > 0) { ?>
                                    <span class="badge bg-info text-dark" title="Sản phẩm đã xuất hiện trong <?= $orderCount ?> đơn hàng">
                                        <i class="bi bi-receipt me-1"></i><?= $orderCount ?> đơn
                                    </span>
                                <?php } else { ?>
                                    <span class="badge bg-light text-muted border">0 đơn</span>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <a class="btn btn-warning btn-sm" href="index.php?act=suasp&id=<?= $sp['id'] ?>" title="Sửa sản phẩm">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </a>
                                
                                <?php if ($orderCount > 0) { ?>
                                    <!-- Sản phẩm đã có trong hóa đơn của khách -> Khóa xóa để bảo toàn dữ liệu -->
                                    <button type="button" 
                                            class="btn btn-outline-secondary btn-sm" 
                                            onclick="alert('Ràng buộc toàn vẹn dữ liệu: Không thể xóa sản phẩm \'<?= htmlspecialchars(addslashes($sp['name'])) ?>\' vì đã có trong <?= $orderCount ?> đơn hàng/hóa đơn của khách hàng!\n\nQuy định thương mại điện tử yêu cầu giữ nguyên sản phẩm để khách xem lại lịch sử hóa đơn. Để ngừng bán, bạn có thể chỉnh sửa thông tin hoặc tạm ẩn sản phẩm.')" 
                                            title="Không thể xóa vì đã có trong đơn hàng của khách">
                                        <i class="bi bi-lock-fill"></i> Đã khóa
                                    </button>
                                <?php } else { ?>
                                    <!-- Sản phẩm chưa từng có đơn hàng -> Cho phép xóa -->
                                    <form method="POST" action="index.php?act=xoasp" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                        <input type="hidden" name="id" value="<?= $sp['id'] ?>">
                                        <button type="submit" name="xoa" class="btn btn-danger btn-sm" title="Xóa sản phẩm">
                                            <i class="bi bi-trash"></i> Xóa
                                        </button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php }} else { ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Chưa có sản phẩm nào.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
