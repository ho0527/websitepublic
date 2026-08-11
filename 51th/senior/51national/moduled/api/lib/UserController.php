<?php
/**
 * 會員相關 API
 * API 1 會員登入、API 2 會員登出、API 3 會員註冊
 */
class UserController
{
    /** @var PDO 資料庫連線 */
    private PDO $pdo;

    /** @var Auth 身分驗證 */
    private Auth $auth;

    public function __construct(PDO $pdo, Auth $auth)
    {
        $this->pdo  = $pdo;
        $this->auth = $auth;
    }

    /**
     * API 1：會員登入 [POST] /api/user/login
     * 成功時回傳 sha256 hash 過的 email 作為 token
     */
    public function login(Request $request): void
    {
        $input = $request->all();

        Validator::required($input, ['email', 'password']);
        Validator::strings($input, ['email', 'password']);
        Validator::email($input, 'email');

        $email    = (string) $input['email'];
        $password = (string) $input['password'];

        $statement = $this->pdo->prepare(
            'SELECT id, email, password, nickname, role FROM users WHERE email = :email LIMIT 1'
        );
        $statement->execute([':email' => $email]);
        $user = $statement->fetch();

        // 使用者不存在或密碼錯誤，一律回傳相同錯誤避免帳號列舉
        if ($user === false || !$this->verifyPassword($password, (string) $user['password'])) {
            throw new ApiException('MSG_INVALID_LOGIN', 403);
        }

        $token = Auth::makeToken($user['email']);

        $update = $this->pdo->prepare('UPDATE users SET token = :token WHERE id = :id');
        $update->execute([':token' => $token, ':id' => $user['id']]);

        Response::success([
            'id'       => (int) $user['id'],
            'nickname' => $user['nickname'],
            'email'    => $user['email'],
            'role'     => $user['role'],
            'token'    => $token,
        ]);
    }

    /**
     * API 2：會員登出 [POST] /api/user/logout
     * 將使用者的 token 清空
     */
    public function logout(Request $request): void
    {
        $user = $this->auth->user($request->token());

        $statement = $this->pdo->prepare('UPDATE users SET token = NULL WHERE id = :id');
        $statement->execute([':id' => $user['id']]);

        Response::success('');
    }

    /**
     * API 3：會員註冊 [POST] /api/user/register
     * 新註冊的使用者身分固定為 USER
     */
    public function register(Request $request): void
    {
        $input = $request->all();

        Validator::required($input, ['email', 'password', 'nickname']);
        Validator::strings($input, ['email', 'password', 'nickname']);
        Validator::email($input, 'email');

        $email = (string) $input['email'];

        $exists = $this->pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $exists->execute([':email' => $email]);
        if ($exists->fetch() !== false) {
            throw new ApiException('MSG_USER_EXISTS', 409);
        }

        $insert = $this->pdo->prepare(
            'INSERT INTO users (email, password, nickname, role) VALUES (:email, :password, :nickname, \'USER\')'
        );
        $insert->execute([
            ':email'    => $email,
            ':password' => password_hash((string) $input['password'], PASSWORD_BCRYPT),
            ':nickname' => (string) $input['nickname'],
        ]);

        Response::success('');
    }

    /**
     * 驗證密碼
     * 支援 bcrypt 雜湊，若資料庫中為明碼（測試資料）也可比對
     */
    private function verifyPassword(string $password, string $stored): bool
    {
        if (preg_match('/^\$2[aby]\$/', $stored) === 1) {
            return password_verify($password, $stored);
        }

        return hash_equals($stored, $password);
    }
}
