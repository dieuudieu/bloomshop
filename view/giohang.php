<div class="container my-5">

    <h2 class="text-center mb-4">Giỏ hàng của bạn</h2>

    <?php if (count($giohang) == 0) { ?>

        <div class="text-center py-5">
            <h5>Giỏ hàng đang trống!</h5>

            <a href="index.php?act=sanpham"
               class="btn btn-primary mt-3">
                Tiếp tục mua sắm
            </a>
        </div>

    <?php } else { ?>

        <div class="table-responsive">

            <table class="table table-bordered align-middle text-center">

                <thead class="table-light">
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($giohang as $sp) { ?>

                        <tr>
                            <td>
                                <img
                                    src="/bloomshop/view/image/sanpham/<?= htmlspecialchars($sp['image']) ?>"
                                    width="75"
                                    height="75"
                                    style="object-fit: cover;"
                                >

                                <div class="mt-2">
                                    <?= htmlspecialchars($sp['name']) ?>
                                </div>
                            </td>

                            <td>
                                <?= number_format($sp['price'], 0, ',', '.') ?>đ
                            </td>

                            <td>
                                <div class="d-flex justify-content-center align-items-center gap-2">

                                    <!-- Giảm số lượng -->
                                    <form method="POST" action="index.php?act=giohang">
                                        <input type="hidden" name="id"
                                               value="<?= $sp['id'] ?>">

                                        <button type="submit"
                                                name="thao_tac"
                                                value="giam"
                                                class="btn btn-outline-secondary btn-sm">
                                            -
                                        </button>
                                    </form>

                                    <strong><?= $sp['soluong'] ?></strong>

                                    <!-- Tăng số lượng -->
                                    <form method="POST" action="index.php?act=giohang">
                                        <input type="hidden" name="id"
                                               value="<?= $sp['id'] ?>">

                                        <button type="submit"
                                                name="thao_tac"
                                                value="tang"
                                                class="btn btn-outline-secondary btn-sm">
                                            +
                                        </button>
                                    </form>

                                </div>
                            </td>

                            <td class="fw-bold">
                                <?= number_format($sp['thanhtien'], 0, ',', '.') ?>đ
                            </td>

                            <td>
                                <form method="POST" action="index.php?act=giohang">

                                    <input type="hidden"
                                           name="id"
                                           value="<?= $sp['id'] ?>">

                                    <button type="submit"
                                            name="thao_tac"
                                            value="xoa"
                                            class="btn btn-danger btn-sm">
                                        Xóa
                                    </button>

                                </form>
                            </td>
                        </tr>

                    <?php } ?>

                </tbody>
            </table>

        </div>

        <div class="text-end mt-4">

            <h4>
                Tổng tiền:
                <span class="text-danger">
                    <?= number_format($tongTien, 0, ',', '.') ?>đ
                </span>
            </h4>

            <a href="index.php?act=sanpham"
               class="btn btn-outline-primary mt-3">
                Tiếp tục mua sắm
            </a>

            <button class="btn btn-success mt-3" disabled>
                Thanh toán (sắp có)
            </button>

        </div>

    <?php } ?>

</div>