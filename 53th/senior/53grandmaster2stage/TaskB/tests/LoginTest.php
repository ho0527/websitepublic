<?php
/**
 * 測試項目 1：訪客登入
 *   a. 成功登入
 *   b. Email 有誤
 *   c. 密碼有誤
 */

declare(strict_types=1);

final class LoginTest extends GraphQLTestCase
{
    /** 登入用的查詢字串 */
    private function loginQuery(string $email, string $password): string
    {
        return 'mutation userLogin {
            login(email: "' . $email . '", password: "' . $password . '") {
                user_token
            }
        }';
    }

    /** a. 成功登入時應回傳 user_token */
    public function testLoginSuccessReturnsUserToken(): void
    {
        $response = $this->graphql($this->loginQuery(self::ADMIN_EMAIL, self::ADMIN_PASSWORD));

        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('login', $response['data']);
        $this->assertArrayHasKey('user_token', $response['data']['login']);
        $this->assertArrayNotHasKey('errors', $response);
    }

    /** a. user_token 必須是 Email 的 sha256 */
    public function testLoginTokenIsSha256OfEmail(): void
    {
        $response = $this->graphql($this->loginQuery(self::ADMIN_EMAIL, self::ADMIN_PASSWORD));

        $this->assertSame(
            hash('sha256', self::ADMIN_EMAIL),
            $response['data']['login']['user_token'],
            'user_token 應為 Email 進行 sha256 產生'
        );
    }

    /** a. 登入後權杖必須寫入資料庫，讓後續請求可以通過認證 */
    public function testLoginStoresTokenSoAuthenticatedQueryWorks(): void
    {
        $token = $this->graphql($this->loginQuery(self::USER_EMAIL, self::USER_PASSWORD))['data']['login']['user_token'];

        $response = $this->graphql('query getUser { user { email } }', $token);

        $this->assertSame(self::USER_EMAIL, $response['data']['user']['email']);
    }

    /** a. 一般會員也可以正常登入 */
    public function testLoginSuccessForNormalUser(): void
    {
        $response = $this->graphql($this->loginQuery(self::USER_EMAIL, self::USER_PASSWORD));

        $this->assertSame(hash('sha256', self::USER_EMAIL), $response['data']['login']['user_token']);
    }

    /** b. Email 有誤時回傳 user not found */
    public function testLoginWithUnknownEmailReturnsUserNotFound(): void
    {
        $response = $this->graphql($this->loginQuery('nouser@localhost', 'nouser'));

        $this->assertGraphQLError('user not found', $response);
    }

    /** c. 密碼有誤時同樣回傳 user not found（不透露帳號是否存在） */
    public function testLoginWithWrongPasswordReturnsUserNotFound(): void
    {
        $response = $this->graphql($this->loginQuery(self::ADMIN_EMAIL, 'wrong-password'));

        $this->assertGraphQLError('user not found', $response);
    }

    /** b/c. 各種錯誤輸入都不應該產生權杖 */
    public function testLoginFailureDoesNotCreateToken(): void
    {
        $this->graphql($this->loginQuery(self::ADMIN_EMAIL, 'wrong-password'));

        $stored = Database::selectOne('SELECT user_token FROM users WHERE email = ?', [self::ADMIN_EMAIL]);

        $this->assertNull($stored['user_token'], '登入失敗不應寫入權杖');
    }

    /**
     * b/c. 以資料提供者一次涵蓋多種錯誤輸入
     *
     * @dataProvider invalidCredentialsProvider
     */
    public function testLoginWithInvalidCredentials(string $email, string $password): void
    {
        $this->assertGraphQLError('user not found', $this->graphql($this->loginQuery($email, $password)));
    }

    /** @return array<string, array{0:string, 1:string}> */
    public static function invalidCredentialsProvider(): array
    {
        return [
            'Email 不存在'      => ['nouser@localhost', 'nouser'],
            'Email 格式不正確'  => ['admin', 'adminpass'],
            '密碼錯誤'          => ['admin@localhost', 'user1pass'],
            '密碼為空'          => ['admin@localhost', ''],
            'Email 為空'        => ['', 'adminpass'],
            'SQL Injection 嘗試' => ["admin@localhost' OR '1'='1", 'adminpass'],
        ];
    }

    /** 缺少必要參數時應回報參數錯誤而不是丟出例外 */
    public function testLoginWithoutPasswordArgumentReturnsError(): void
    {
        $response = $this->graphql('mutation userLogin { login(email: "admin@localhost") { user_token } }');

        $this->assertArrayHasKey('errors', $response);
        $this->assertStringContainsString('password', $response['errors'][0]['message']);
    }
}
