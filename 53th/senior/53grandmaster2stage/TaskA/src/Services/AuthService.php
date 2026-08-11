<?php
/**
 * 會員認證相關商業邏輯（登入／登出／註冊／取得自身資料）
 */
class AuthService
{
    /**
     * 目前請求的存取權杖（由 App 於每次請求開始時設定）
     */
    private static ?string $currentToken = null;

    /** 設定目前請求的權杖，null 代表未帶 Authorization 標頭 */
    public static function setToken(?string $token): void
    {
        self::$currentToken = $token;
    }

    /** 取得目前請求的權杖 */
    public static function token(): ?string
    {
        return self::$currentToken;
    }

    /**
     * 由 Email 產生使用者權杖（試題規定：user_token 為 Email 進行 sha256 產生）
     */
    public static function makeToken(string $email): string
    {
        return hash('sha256', $email);
    }

    /**
     * 依目前權杖取得登入中的使用者，未登入時回傳 null
     */
    public static function currentUser(): ?array
    {
        if (self::$currentToken === null || self::$currentToken === '') {
            return null;
        }

        return Database::selectOne(
            'SELECT id, email, username, role FROM users WHERE user_token = ? LIMIT 1',
            [self::$currentToken]
        );
    }

    /**
     * 要求必須已登入，否則拋出「unauthorized user」
     */
    public static function requireUser(): array
    {
        $user = self::currentUser();

        if ($user === null) {
            throw new GraphQLError('unauthorized user');
        }

        return $user;
    }

    /**
     * 要求必須為管理者，否則拋出「permission denied」
     */
    public static function requireAdmin(): array
    {
        $user = self::requireUser();

        if ($user['role'] !== 'ADMIN') {
            throw new GraphQLError('permission denied');
        }

        return $user;
    }

    /**
     * 訪客登入：成功回傳含 user_token 的陣列
     */
    public static function login(string $email, string $password): array
    {
        $user = Database::selectOne('SELECT id, email, password FROM users WHERE email = ? LIMIT 1', [$email]);

        // 帳號不存在或密碼錯誤，一律回報 user not found
        if ($user === null || !password_verify($password, $user['password'])) {
            throw new GraphQLError('user not found');
        }

        $token = self::makeToken($user['email']);
        Database::execute('UPDATE users SET user_token = ? WHERE id = ?', [$token, $user['id']]);

        return ['user_token' => $token];
    }

    /**
     * 訪客登出：清除資料庫中的權杖
     */
    public static function logout(): array
    {
        $user = self::requireUser();
        Database::execute('UPDATE users SET user_token = NULL WHERE id = ?', [$user['id']]);
        self::setToken(null);

        return ['message' => 'user logout success'];
    }

    /**
     * 訪客註冊，Email 重複時拋出「user already exists」
     */
    public static function register(string $email, string $password, string $username): array
    {
        $existing = Database::selectOne('SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1', [$email, $username]);

        if ($existing !== null) {
            throw new GraphQLError('user already exists');
        }

        Database::execute(
            'INSERT INTO users (email, password, username, role, created_at) VALUES (?, ?, ?, ?, ?)',
            [$email, password_hash($password, PASSWORD_DEFAULT), $username, 'USER', time()]
        );

        return ['message' => 'user register success'];
    }
}
