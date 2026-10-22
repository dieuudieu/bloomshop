<div class="row g-4">
    <?php if (!empty($sanpham)) { ?>
        <?php foreach ($sanpham as $sp) { 
            $has_sale = (!empty($sp['is_sale']) && !empty($sp['sale_price']) && $sp['sale_price'] < $sp['price']);
            $current_price = $has_sale ? $sp['sale_price'] : $sp['price'];
            $discount_pct = $has_sale ? round((($sp['price'] - $sp['sale_price']) / $sp['price']) * 100) : 0;
        ?>
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 product-card position-relative">
                    <?php if ($has_sale) { ?>
                        <span class="badge-sale">-<?= $discount_pct ?>%</span>
                    <?php } ?>
                    
                    <a href="index.php?act=chitiettsp&id=<?= $sp['id'] ?>" class="text-decoration-none">
                        <img class="card-img-top product-image" src="/bloomshop/view/image/sanpham/<?= htmlspecialchars($sp['image']) ?>" alt="<?= htmlspecialchars($sp['name']) ?>" onerror="this.src='/bloomshop/view/image/banner.jpg'">
                    </a>

                    <div class="card-body d-flex flex-column text-center">
                        <h6 class="card-title mb-2">
                            <a href="index.php?act=chitiettsp&id=<?= $sp['id'] ?>" class="text-dark text-decoration-none fw-semibold">
                                <?= htmlspecialchars($sp['name']) ?>
                            </a>
                        </h6>

                        <div class="mt-auto">
                            <div class="mb-2">
                                <span class="price-text"><?= number_format($current_price, 0, ',', '.') ?>đ</span>
                                <?php if ($has_sale) { ?>
                                    <span class="old-price ms-1"><?= number_format($sp['price'], 0, ',', '.') ?>đ</span>
                                <?php } ?>
                            </div>

                            <div class="d-flex gap-2 justify-content-center">
                                <a href="index.php?act=chitiettsp&id=<?= $sp['id'] ?>" class="btn btn-outline-secondary btn-sm flex-fill">
                                    Chi tiết
                                </a>
                                <form method="POST" action="index.php?act=themgiohang" class="m-0 flex-fill">
                                    <input type="hidden" name="id" value="<?= $sp['id'] ?>">
                                    <input type="hidden" name="qty" value="1">
                                    <button type="submit" name="themgiohang" class="btn btn-bloom btn-sm w-100" title="Thêm vào giỏ hàng">
                                        <i class="bi bi-cart-plus"></i> Mua
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="col-12 py-5 text-center text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
            <p>Không tìm thấy sản phẩm nào phù hợp.</p>
            <a href="index.php?act=sanpham" class="btn btn-outline-secondary btn-sm">Xem tất cả hoa</a>
        </div>
    <?php } ?>
</div>
