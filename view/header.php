<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($asset_dir)) {
    $asset_dir = file_exists('view/css/style.css') ? 'view' : (file_exists('../view/css/style.css') ? '../view' : 'view');
}
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += (int)($item['qty'] ?? 1);
    }
}
$act = $_REQUEST['act'] ?? 'trangchu';
$user_role = $_SESSION['role'] ?? '';
$user_tier = $_SESSION['tier'] ?? 'silver';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BloomShop - Cửa hàng hoa tươi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= $asset_dir ?>/css/style.css?v=<?= file_exists(__DIR__ . '/css/style.css') ? filemtime(__DIR__ . '/css/style.css') : '2.1' ?>" rel="stylesheet">
</head>
<body>
<div class="top-bar d-flex justify-content-between px-4 align-items-center flex-wrap">
    <span>🌸 BloomShop - Gửi yêu thương qua từng đóa hoa tươi | Hotline: 0901.234.567</span>
    <span>
        <a href="index.php?act=tracuudonhang" class="text-decoration-none text-muted small me-3">
            <i class="bi bi-geo-alt"></i> Tra cứu đơn hàng
        </a>
        <a href="index.php?act=giohang" class="text-decoration-none text-muted small">
            <i class="bi bi-ticket-perforated"></i> Voucher & Khuyến mãi
        </a>
    </span>
</div>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php?act=trangchu">Bloom<span>Shop</span></a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <!-- Form Tìm kiếm -->
            <form action="index.php" method="GET" class="d-flex header-search-form mx-lg-4 my-2 my-lg-0 flex-grow-1">
                <input type="hidden" name="act" value="sanpham">
                <div class="input-group">
                    <input class="form-control form-control-sm" type="search" name="q" placeholder="Tìm hoa hồng, tulip, hướng dương..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    <button class="btn btn-bloom btn-sm" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>

            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?= ($act == 'trangchu') ? 'active' : '' ?>" href="index.php?act=trangchu">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($act == 'sanpham' || $act == 'chitiettsp') ? 'active' : '' ?>" href="index.php?act=sanpham">Sản phẩm</a>
                </li>

                <!-- Menu cho Quản trị viên và Nhân viên -->
                <?php if ($user_role == 'admin' || $user_role == 'staff') { ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-primary fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear-fill"></i> Quản trị
                        </a>
                        <ul class="dropdown-menu shadow">
                            <li><h6 class="dropdown-header">Bán hàng</h6></li>
                            <li><a class="dropdown-item" href="index.php?act=quanlydonhang"><i class="bi bi-receipt"></i> Quản lý đơn hàng</a></li>
                            <li><a class="dropdown-item" href="index.php?act=quanlyvoucher"><i class="bi bi-ticket-perforated"></i> Quản lý voucher</a></li>
                            <li><a class="dropdown-item" href="index.php?act=khuyenmai"><i class="bi bi-tag-fill"></i> Khuyến mãi sản phẩm</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Kho & Dữ liệu</h6></li>
                            <li><a class="dropdown-item" href="index.php?act=quanlysp"><i class="bi bi-flower1"></i> Quản lý sản phẩm</a></li>
                            <li><a class="dropdown-item" href="index.php?act=danhmuc"><i class="bi bi-folder"></i> Quản lý danh mục</a></li>
                            <li><a class="dropdown-item" href="index.php?act=quanlydanhgia"><i class="bi bi-chat-heart"></i> Quản lý đánh giá</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Khách hàng & Báo cáo</h6></li>
                            <li><a class="dropdown-item" href="index.php?act=quanlykh"><i class="bi bi-people"></i> Quản lý khách hàng</a></li>
                            <?php if ($user_role == 'admin') { ?>
                                <li><a class="dropdown-item" href="index.php?act=quanlynv"><i class="bi bi-person-badge"></i> Quản lý nhân viên</a></li>
                                <li><a class="dropdown-item" href="index.php?act=doanhthu"><i class="bi bi-bar-chart-line"></i> Quản lý doanh thu</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- Giỏ hàng -->
                <li class="nav-item">
                    <a class="nav-link position-relative" href="index.php?act=giohang">
                        <i class="bi bi-bag-heart-fill fs-5 text-bloom"></i>
                        <span class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $cart_count ?>
                        </span>
                    </a>
                </li>

                <!-- Tài khoản -->
                <?php if (isset($_SESSION['id'])) { ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5"></i>
                            <span><?= htmlspecialchars($_SESSION['name']) ?></span>
                            <?php if ($user_tier == 'diamond') { ?>
                                <span class="badge bg-primary text-white" style="font-size: 10px;">💎 Diamond</span>
                            <?php } elseif ($user_tier == 'gold') { ?>
                                <span class="badge bg-warning text-dark" style="font-size: 10px;">🥇 Gold</span>
                            <?php } else { ?>
                                <span class="badge bg-secondary text-white" style="font-size: 10px;">🥈 Silver</span>
                            <?php } ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li class="px-3 py-2 small text-muted border-bottom">
                                Quyền: <strong><?= strtoupper(htmlspecialchars($_SESSION['role'])) ?></strong>
                            </li>
                            <li><a class="dropdown-item" href="index.php?act=lichsudonhang"><i class="bi bi-bag-check"></i> Đơn mua của tôi</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="index.php?act=dangxuat" class="m-0">
                                    <button type="submit" name="dangxuat" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                <?php } else { ?>
                    <li class="nav-item d-flex gap-2 ms-lg-2">
                        <a href="index.php?act=dangnhap" class="btn btn-outline-secondary btn-sm">Đăng nhập</a>
                        <a href="index.php?act=dangky" class="btn btn-bloom btn-sm">Đăng ký</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>
