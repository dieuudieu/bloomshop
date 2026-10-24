<?php
// model/ThongKeModel.php - Quản lý Thống kê & Báo cáo doanh thu theo OOP
require_once __DIR__ . '/BaseModel.php';

class ThongKeModel extends BaseModel
{
    public function getOverview()
    {
        $tong_doanhthu = (float)($this->readsingle("SELECT COALESCE(SUM(total_price), 0) AS total FROM donhang WHERE status = 'thanh_cong'")['total'] ?? 0);
        $tong_don_thanhcong = (int)($this->readsingle("SELECT COUNT(*) AS total FROM donhang WHERE status = 'thanh_cong'")['total'] ?? 0);
        $doanhthu_tuan = (float)($this->readsingle("SELECT COALESCE(SUM(total_price), 0) AS total FROM donhang WHERE status = 'thanh_cong' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")['total'] ?? 0);
        $doanhthu_thang = (float)($this->readsingle("SELECT COALESCE(SUM(total_price), 0) AS total FROM donhang WHERE status = 'thanh_cong' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())")['total'] ?? 0);
        $doanhthu_nam = (float)($this->readsingle("SELECT COALESCE(SUM(total_price), 0) AS total FROM donhang WHERE status = 'thanh_cong' AND YEAR(created_at) = YEAR(CURRENT_DATE())")['total'] ?? 0);

        return [
            'tong_doanhthu' => $tong_doanhthu,
            'tong_don_thanhcong' => $tong_don_thanhcong,
            'doanhthu_tuan' => $doanhthu_tuan,
            'doanhthu_thang' => $doanhthu_thang,
            'doanhthu_nam' => $doanhthu_nam
        ];
    }

    public function getStats7Days()
    {
        $stats = [];
        for ($i = 6; $i >= 0; $i--) {
            $day_date = date('Y-m-d', strtotime("-$i days"));
            $row = $this->readsingle("SELECT COALESCE(SUM(total_price), 0) AS rev, COUNT(*) AS cnt 
                                      FROM donhang 
                                      WHERE status = 'thanh_cong' AND DATE(created_at) = :d", [':d' => $day_date]);
            $stats[] = [
                'date' => $day_date,
                'revenue' => (float)($row['rev'] ?? 0),
                'orders_count' => (int)($row['cnt'] ?? 0)
            ];
        }
        return $stats;
    }

    public function getStatsMonths()
    {
        $stats = [];
        for ($m = 1; $m <= 12; $m++) {
            $row = $this->readsingle("SELECT COALESCE(SUM(total_price), 0) AS rev, COUNT(*) AS cnt 
                                      FROM donhang 
                                      WHERE status = 'thanh_cong' AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND MONTH(created_at) = :m", [':m' => $m]);
            $stats[] = [
                'month' => $m,
                'revenue' => (float)($row['rev'] ?? 0),
                'orders_count' => (int)($row['cnt'] ?? 0)
            ];
        }
        return $stats;
    }

    public function getStatsYears()
    {
        $sql = "SELECT YEAR(created_at) AS year, COALESCE(SUM(total_price), 0) AS revenue, COUNT(*) AS orders_count 
                FROM donhang 
                WHERE status = 'thanh_cong' 
                GROUP BY YEAR(created_at) 
                ORDER BY year DESC";
        return $this->readitem($sql);
    }

    public function getBestSellers($limit = 5)
    {
        $sql = "SELECT sanpham.id, sanpham.name, sanpham.image, sanpham.price,
                       COALESCE(SUM(chitiet_donhang.quantity), 0) AS total_sold,
                       COALESCE(SUM(chitiet_donhang.total), 0) AS total_revenue
                FROM sanpham
                JOIN chitiet_donhang ON sanpham.id = chitiet_donhang.product_id
                JOIN donhang ON chitiet_donhang.order_id = donhang.id
                WHERE donhang.status = 'thanh_cong'
                GROUP BY sanpham.id
                ORDER BY total_sold DESC
                LIMIT " . (int)$limit;
        return $this->readitem($sql);
    }

    public function getLeastSellers($limit = 6)
    {
        $sql = "SELECT sanpham.id, sanpham.name, sanpham.image, sanpham.price, danhmuc.name AS tendanhmuc,
                       COALESCE(SUM(CASE WHEN donhang.status = 'thanh_cong' THEN chitiet_donhang.quantity ELSE 0 END), 0) AS total_sold
                FROM sanpham
                LEFT JOIN danhmuc ON sanpham.id_dm = danhmuc.id
                LEFT JOIN chitiet_donhang ON sanpham.id = chitiet_donhang.product_id
                LEFT JOIN donhang ON chitiet_donhang.order_id = donhang.id
                GROUP BY sanpham.id
                ORDER BY total_sold ASC, sanpham.id DESC
                LIMIT " . (int)$limit;
        return $this->readitem($sql);
    }
}
