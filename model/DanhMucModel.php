<?php
// model/DanhMucModel.php - Quản lý Danh mục hoa theo OOP
require_once __DIR__ . '/BaseModel.php';

class DanhMucModel extends BaseModel
{
    public function getAll()
    {
        return $this->readitem("SELECT danhmuc.*, 
                                       (SELECT COUNT(*) FROM sanpham WHERE sanpham.id_dm = danhmuc.id) AS total_products 
                                FROM danhmuc 
                                ORDER BY danhmuc.id DESC");
    }

    public function getById($id)
    {
        return $this->readsingle("SELECT * FROM danhmuc WHERE id = :id", [':id' => (int)$id]);
    }

    public function create($name)
    {
        $res = $this->execute_item("INSERT INTO danhmuc (name) VALUES (:name)", [':name' => trim($name)]);
        $this->exportSqlDump();
        return $res;
    }

    public function update($id, $name)
    {
        $res = $this->execute_item("UPDATE danhmuc SET name = :name WHERE id = :id", [
            ':name' => trim($name),
            ':id' => (int)$id
        ]);
        $this->exportSqlDump();
        return $res;
    }

    public function delete($id)
    {
        $id = (int)$id;
        $check = $this->readsingle("SELECT COUNT(*) AS total FROM sanpham WHERE id_dm = :id", [':id' => $id]);
        if ($check && (int)$check['total'] > 0) {
            throw new Exception("Không thể xóa danh mục này vì đang chứa " . $check['total'] . " sản phẩm hoa!");
        }

        $res = $this->execute_item("DELETE FROM danhmuc WHERE id = :id", [':id' => $id]);
        $this->exportSqlDump();
        return $res;
    }
}
