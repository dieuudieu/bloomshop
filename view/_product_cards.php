<div class="row g-2 g-md-3 g-lg-4">
    <?php if (!empty($sanpham)) { ?>
        <?php foreach ($sanpham as $sp) { 
            $has_sale = (!empty($sp['is_sale']) && !empty($sp['sale_price']) && $sp['sale_price'] < $sp['price']);
            $current_price = $has_sale ? $sp['sale_price'] : $sp['price'];
            $discount_pct = $has_sale ? round((($sp['price'] - $sp['sale_price']) / $sp['price']) * 100) : 0;
            $ton_kho = max(0, (int)($sp['so_luong'] ?? 0));
            $het_hang = ($ton_kho <= 0);
        ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 product-card position-relative border-0 shadow-sm">
                    <?php if ($has_sale) { ?>
                        <span class="badge-sale">-<?= $discount_pct ?>%</span>
                    <?php } ?>
                    
                    <a href="index.php?act=chitiettsp&id=<?= $sp['id'] ?>" class="text-decoration-none d-block overflow-hidden">
                        <img class="card-img-top product-image" src="<?= $asset_dir ?>/image/sanpham/<?= htmlspecialchars($sp['image']) ?>" alt="<?= htmlspecialchars($sp['name']) ?>" onerror="this.src='<?= $asset_dir ?>/image/banner.jpg'">
                    </a>

                    <div class="card-body d-flex flex-column text-center p-2 p-md-3">
                        <h6 class="card-title mb-1 mb-md-2">
                            <a href="index.php?act=chitiettsp&id=<?= $sp['id'] ?>" class="text-dark text-decoration-none fw-semibold product-title-link">
                                <?= htmlspecialchars($sp['name']) ?>
                            </a>
                        </h6>

                        <div class="mt-auto">
                            <div class="mb-2">
                                <span class="price-text"><?= number_format($current_price, 0, ',', '.') ?>đ</span>
                                <?php if ($has_sale) { ?>
                                    <span class="old-price ms-1 d-none d-sm-inline"><?= number_format($sp['price'], 0, ',', '.') ?>đ</span>
                                <?php } ?>
                            </div>

                            <div class="d-flex gap-1 gap-md-2 justify-content-center">
                                <a href="index.php?act=chitiettsp&id=<?= $sp['id'] ?>" class="btn btn-outline-secondary btn-sm flex-fill px-1 px-md-2 text-nowrap">
                                    Chi tiết
                                </a>
                                                                
                                <?php if (!$het_hang): ?>

                                    <form method="POST"
                                        action="index.php?act=themgiohang"
                                        class="m-0 flex-fill">

                                        <input type="hidden"
                                            name="id"
                                            value="<?= (int)$sp['id'] ?>">

                                        <input type="hidden"
                                            name="qty"
                                            value="1">

                                        <button type="submit"
                                                name="themgiohang"
                                                class="btn btn-bloom btn-sm w-100 px-1 px-md-2 text-nowrap">

                                            <i class="bi bi-cart-plus"></i>
                                            <span class="d-none d-sm-inline">Mua</span>
                                        </button>
                                    </form>

                                <?php else: ?>

                                    <button type="button"
                                            class="btn btn-outline-secondary btn-sm flex-fill"
                                            disabled>

                                        <i class="bi bi-cart-x"></i>
                                        Hết hàng
                                    </button>

                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="col-12 py-5 text-center text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary"></i>
            <p class="mb-2">Không tìm thấy sản phẩm nào phù hợp.</p>
            <a href="index.php?act=sanpham" class="btn btn-outline-secondary btn-sm">Xem tất cả hoa</a>
        </div>
    <?php } ?>
</div>
