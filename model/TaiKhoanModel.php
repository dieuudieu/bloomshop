<?php
// model/TaiKhoanModel.php - Quản lý Tài khoản Khách hàng & Nhân viên theo OOP
require_once __DIR__ . '/BaseModel.php';

class TaiKhoanModel extends BaseModel
{
    /**
     * Tìm người dùng theo email
     */
    public function findByEmail($email)
    {
        return $this->readsingle("SELECT * FROM users WHERE email = :email", [':email' => trim($email)]);
    }

    /**
     * Lấy thông tin người dùng theo ID
     */
    public function findById($id)
    {
        return $this->readsingle("SELECT * FROM users WHERE id = :id", [':id' => (int)$id]);
    }

    /**
     * Đăng ký tài khoản khách hàng mới
     */
    public function register($name, $email, $password)
    {
        $hashed_pwd = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password, role, status, tier, total_spent)
                VALUES (:name, :email, :password, 'user', 1, 'silver', 0)";
        $res = $this->execute_item($sql, [
            ':name' => trim($name),
            ':email' => trim($email),
            ':password' => $hashed_pwd
        ]);
        $this->exportSqlDump();
        return $res;
    }

    /**
     * Lấy danh sách khách hàng (Admin/Staff)
     */
    public function getAllCustomers($tier = '')
    {
        $where = "role = 'user'";
        $params = [];
        if (!empty($tier)) {
            $where .= " AND tier = :tier";
            $params[':tier'] = $tier;
        }
        $sql = "SELECT * FROM users WHERE $where ORDER BY total_spent DESC, id DESC";
        return $this->readitem($sql, $params);
    }

    /**
     * Đếm số lượng khách hàng theo từng phân hạng (Diamond, Gold, Silver)
     */
    public function countCustomersByTier()
    {
        $d = $this->readsingle("SELECT COUNT(*) as c FROM users WHERE role = 'user' AND tier = 'diamond'");
        $g = $this->readsingle("SELECT COUNT(*) as c FROM users WHERE role = 'user' AND tier = 'gold'");
        $s = $this->readsingle("SELECT COUNT(*) as c FROM users WHERE role = 'user' AND tier = 'silver'");

        return [
            'diamond' => $d['c'] ?? 0,
            'gold'    => $g['c'] ?? 0,
            'silver'  => $s['c'] ?? 0
        ];
    }

    /**
     * Khóa hoặc mở khóa tài khoản khách hàng
     */
    public function updateCustomerStatus($id, $status)
    {
        $sql = "UPDATE users SET status = :st WHERE id = :id AND role = 'user'";
        $res = $this->execute_item($sql, [
            ':st' => (int)$status,
            ':id' => (int)$id
        ]);
        $this->exportSqlDump();
        return $res;
    }

    /**
     * Lấy danh sách nhân viên (Admin only)
     */
    public function getAllStaff()
    {
        return $this->readitem("SELECT * FROM users WHERE role = 'staff' ORDER BY id DESC");
    }

    /**
     * Thêm mới tài khoản nhân viên
     */
    public function createStaff($name, $email, $phone, $password)
    {
        $hashed_pwd = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password, phone, role, status)
                VALUES (:name, :email, :password, :phone, 'staff', 1)";
        $res = $this->execute_item($sql, [
            ':name' => trim($name),
            ':email' => trim($email),
            ':password' => $hashed_pwd,
            ':phone' => trim($phone)
        ]);
        $this->exportSqlDump();
        return $res;
    }

    /**
     * Cập nhật thông tin nhân viên
     */
    public function updateStaff($id, $name, $email, $phone, $status, $password = null)
    {
        if (!empty($password)) {
            $hashed_pwd = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET name = :name, email = :email, phone = :phone, password = :pwd, status = :status WHERE id = :id AND role = 'staff'";
            $res = $this->execute_item($sql, [
                ':name' => trim($name),
                ':email' => trim($email),
                ':phone' => trim($phone),
                ':pwd' => $hashed_pwd,
                ':status' => (int)$status,
                ':id' => (int)$id
            ]);
        } else {
            $sql = "UPDATE users SET name = :name, email = :email, phone = :phone, status = :status WHERE id = :id AND role = 'staff'";
            $res = $this->execute_item($sql, [
                ':name' => trim($name),
                ':email' => trim($email),
                ':phone' => trim($phone),
                ':status' => (int)$status,
                ':id' => (int)$id
            ]);
        }
        $this->exportSqlDump();
        return $res;
    }

    /**
     * Xóa tài khoản nhân viên
     */
    public function deleteStaff($id)
    {
        $res = $this->execute_item("DELETE FROM users WHERE id = :id AND role = 'staff'", [':id' => (int)$id]);
        $this->exportSqlDump();
        return $res;
    }
}
