<div class="container my-5">
    <h2 class="mb-4">Quản lý danh mục</h2>
    <a href="index.php?act=themdm" class="btn btn-success mb-3">+ Thêm danh mục</a>
    <table class="table table-bordered table-striped align-middle">
        <thead><tr><th>ID</th><th>Tên danh mục</th><th>Thao tác</th></tr></thead>
        <tbody>
        <?php foreach ($danhsach as $dm) { ?>
            <tr>
                <td><?= $dm['id'] ?></td>
                <td><?= htmlspecialchars($dm['name']) ?></td>
                <td>
                    <a class="btn btn-warning btn-sm" href="index.php?act=suadm&id=<?= $dm['id'] ?>">Sửa</a>
                    <form method="POST" action="index.php?act=xoadm" class="d-inline" onsubmit="return confirm('Xóa danh mục này?')">
                        <input type="hidden" name="id" value="<?= $dm['id'] ?>">
                        <button type="submit" name="xoa" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
