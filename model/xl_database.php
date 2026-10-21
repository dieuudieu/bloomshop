<?php
require_once __DIR__ . '/database.php';

class xl_data extends database
{
    public function readitem($sql, $params = [])
    {
        $conn = $this->connection_database();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function execute_item($sql, $params = [])
    {
        $conn = $this->connection_database();
        $stmt = $conn->prepare($sql);
        return $stmt->execute($params);
    }
}
