<?php
/**
 * 身分驗證
 * Token 為 sha256(email) 的小寫 hex，登入時寫入 users.token，登出時清空
 */
class Auth
{
    /** @var PDO 資料庫連線 */
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** 依 email 產生 token */
    public static function makeToken(string $email): string
    {
        return strtolower(hash('sha256', $email));
    }

    /**
     * 依 token 取得使用者，找不到時視為無效 Token
     *
     * @throws ApiException MSG_INVALID_TOKEN (401)
     */
    public function user(string $token): array
    {
        if ($token === '') {
            throw new ApiException('MSG_INVALID_TOKEN', 401);
        }

        $statement = $this->pdo->prepare(
            'SELECT id, email, nickname, role FROM users WHERE token = :token LIMIT 1'
        );
        $statement->execute([':token' => $token]);
        $user = $statement->fetch();

        if ($user === false) {
            throw new ApiException('MSG_INVALID_TOKEN', 401);
        }

        return $user;
    }

    /**
     * 要求必須為管理員
     *
     * @throws ApiException MSG_PERMISSION_DENY (403)
     */
    public function requireAdmin(array $user): void
    {
        if (($user['role'] ?? '') !== 'ADMIN') {
            throw new ApiException('MSG_PERMISSION_DENY', 403);
        }
    }
}
