<?php
namespace App\Core;

/**
 * 所有 Model 的共同父類別，統一持有資料庫連線。
 */
abstract class Model
{
    /** @var Database 資料庫存取物件 */
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}
