<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BloomShop - Cửa hàng hoa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/bloomshop/view/css/style.css" rel="stylesheet">
</head>
<body>
<div class="top-bar">BloomShop - Gửi yêu thương qua từng đóa hoa</div>
<nav class="navbar navbar-expand-lg bg-white">
    <div class="container">
        <a class="navbar-brand" href="index.php?act=trangchu">Bloom<span>Shop</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="index.php?act=trangchu">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?act=sanpham">Sản phẩm</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?act=giohang">

                        <i class="bi bi-cart3"></i>
                        Giỏ hàng

                        <?php
                        $soLuongGio = array_sum($_SESSION['giohang'] ?? []);
                        ?>

                        <span class="badge bg-danger">
                            <?= $soLuongGio ?>
                        </span>

                    </a>
                </li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?act=danhmuc">Quản lý danh mục</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?act=quanlysp">Quản lý sản phẩm</a></li>
                <?php } ?>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <?php if (isset($_SESSION['id'])) { ?>
                    <span class="small">Xin chào, <?= htmlspecialchars($_SESSION['name']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)</span>
                    <form method="POST" action="index.php?act=dangxuat" class="m-0">
                        <button type="submit" name="dangxuat" class="btn btn-outline-secondary btn-sm">Đăng xuất</button>
                    </form>
                <?php } else { ?>
                    <a href="index.php?act=dangnhap" class="btn btn-outline-secondary btn-sm">Đăng nhập</a>
                    <a href="index.php?act=dangky" class="btn btn-bloom btn-sm">Đăng ký</a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>
