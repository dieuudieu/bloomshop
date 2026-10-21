<div class="row g-4">
    <?php foreach ($sanpham as $sp) { ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 product-card">
                <img class="card-img-top product-image" src="/bloomshop/view/image/sanpham/<?= htmlspecialchars($sp['image']) ?>" alt="<?= htmlspecialchars($sp['name']) ?>">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= htmlspecialchars($sp['name']) ?></h5>
                    <p class="fw-bold price-text"><?= number_format($sp['price'], 0, ',', '.') ?>đ</p>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<?php if (!$sanpham) { ?><p class="text-center text-muted">Chưa có sản phẩm nào.</p><?php } ?>
