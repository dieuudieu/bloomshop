<div class="container my-5">
    <h2 class="mb-4">Quản lý sản phẩm</h2>
    <a href="index.php?act=themsp" class="btn btn-success mb-3">+ Thêm sản phẩm</a>
    <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead><tr><th>ID</th><th>Ảnh</th><th>Tên sản phẩm</th><th>Giá</th><th>Danh mục</th><th>Thao tác</th></tr></thead>
        <tbody>
        <?php foreach ($danhsach as $sp) { ?>
            <tr>
                <td><?= $sp['id'] ?></td>
                <td><img width="80" height="75" style="object-fit:cover" src="/bloomshop/view/image/sanpham/<?= htmlspecialchars($sp['image']) ?>" alt=""></td>
                <td><?= htmlspecialchars($sp['name']) ?></td>
                <td><?= number_format($sp['price'], 0, ',', '.') ?>đ</td>
                <td><?= htmlspecialchars($sp['tendanhmuc']) ?></td>
                <td>
                    <a class="btn btn-warning btn-sm" href="index.php?act=suasp&id=<?= $sp['id'] ?>">Sửa</a>
                    <form method="POST" action="index.php?act=xoasp" class="d-inline" onsubmit="return confirm('Xóa sản phẩm này?')">
                        <input type="hidden" name="id" value="<?= $sp['id'] ?>">
                        <button type="submit" name="xoa" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    </div>
</div>
