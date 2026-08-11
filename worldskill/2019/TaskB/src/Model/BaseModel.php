<?php
/**
 * 資料模型基底類別
 */

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;

abstract class BaseModel
{
    public function __construct(protected Database $db)
    {
    }
}
