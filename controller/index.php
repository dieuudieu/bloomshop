<?php
/**
 * controller/index.php - Điều hướng chuyển tiếp tương thích ngược
 * Tự động chuyển toàn bộ yêu cầu về Router chính tại thư mục gốc /index.php
 */
$queryString = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: ../index.php" . $queryString);
exit;
