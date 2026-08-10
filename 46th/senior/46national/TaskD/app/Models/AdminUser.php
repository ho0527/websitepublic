<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * 後台管理員帳號。
 *
 * @property int    $id
 * @property string $username
 * @property string $password_hash 密碼雜湊，不儲存明文
 */
final class AdminUser extends Model
{
    protected static string $table = 'admin_user';

    protected static array $fillable = ['username', 'password_hash'];

    /**
     * 以帳號取得管理員。
     */
    public static function findByUsername(string $username): ?AdminUser
    {
        return self::where('username', $username)->first();
    }
}
