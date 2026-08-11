<?php
/**
 * 測試項目 3：訪客註冊
 *   a. 成功註冊
 *   b. 重複的使用者
 */

declare(strict_types=1);

final class RegisterTest extends GraphQLTestCase
{
    /** 註冊用的查詢字串 */
    private function registerQuery(string $email, string $password, string $username): string
    {
        return 'mutation userRegister {
            register(email: "' . $email . '", password: "' . $password . '", username: "' . $username . '") {
                message
            }
        }';
    }

    /** a. 成功註冊時回傳 user register success */
    public function testRegisterSuccessReturnsMessage(): void
    {
        $response = $this->graphql($this->registerQuery('user2@localhost', 'user2pass', 'user2'));

        $this->assertArrayNotHasKey('errors', $response);
        $this->assertSame('user register success', $response['data']['register']['message']);
    }

    /** a. 註冊後資料必須寫入資料庫，且角色預設為 USER */
    public function testRegisterCreatesUserWithUserRole(): void
    {
        $this->graphql($this->registerQuery('user2@localhost', 'user2pass', 'user2'));

        $user = Database::selectOne('SELECT username, role FROM users WHERE email = ?', ['user2@localhost']);

        $this->assertNotNull($user, '註冊後應可在資料庫中查到該使用者');
        $this->assertSame('user2', $user['username']);
        $this->assertSame('USER', $user['role']);
    }

    /** a. 密碼必須以雜湊儲存，不能是明碼 */
    public function testRegisterStoresHashedPassword(): void
    {
        $this->graphql($this->registerQuery('user2@localhost', 'user2pass', 'user2'));

        $user = Database::selectOne('SELECT password FROM users WHERE email = ?', ['user2@localhost']);

        $this->assertNotSame('user2pass', $user['password'], '密碼不可以明碼儲存');
        $this->assertTrue(password_verify('user2pass', $user['password']));
    }

    /** a. 註冊完成的帳號可以直接登入 */
    public function testRegisteredUserCanLogin(): void
    {
        $this->graphql($this->registerQuery('user2@localhost', 'user2pass', 'user2'));

        $response = $this->graphql('mutation { login(email: "user2@localhost", password: "user2pass") { user_token } }');

        $this->assertSame(hash('sha256', 'user2@localhost'), $response['data']['login']['user_token']);
    }

    /** b. Email 重複時回傳 user already exists */
    public function testRegisterWithDuplicatedEmailReturnsError(): void
    {
        $response = $this->graphql($this->registerQuery(self::ADMIN_EMAIL, 'adminpass', 'admin'));

        $this->assertGraphQLError('user already exists', $response);
    }

    /** b. 使用者名稱重複時同樣視為重複的使用者 */
    public function testRegisterWithDuplicatedUsernameReturnsError(): void
    {
        $response = $this->graphql($this->registerQuery('another@localhost', 'anotherpass', 'admin'));

        $this->assertGraphQLError('user already exists', $response);
    }

    /** b. 註冊失敗時不可新增任何資料 */
    public function testFailedRegisterDoesNotInsertUser(): void
    {
        $before = Database::selectOne('SELECT COUNT(*) AS total FROM users')['total'];

        $this->graphql($this->registerQuery(self::ADMIN_EMAIL, 'adminpass', 'admin'));

        $after = Database::selectOne('SELECT COUNT(*) AS total FROM users')['total'];

        $this->assertSame($before, $after, '註冊失敗不應改變使用者數量');
    }

    /** b. 連續註冊同一個 Email，第二次必須失敗 */
    public function testRegisterSameEmailTwiceFailsOnSecondAttempt(): void
    {
        $first  = $this->graphql($this->registerQuery('user2@localhost', 'user2pass', 'user2'));
        $second = $this->graphql($this->registerQuery('user2@localhost', 'user2pass', 'user2x'));

        $this->assertSame('user register success', $first['data']['register']['message']);
        $this->assertGraphQLError('user already exists', $second);
    }

    /** 缺少必要參數時應回報參數錯誤 */
    public function testRegisterWithoutUsernameReturnsError(): void
    {
        $response = $this->graphql('mutation { register(email: "user3@localhost", password: "pass") { message } }');

        $this->assertArrayHasKey('errors', $response);
        $this->assertStringContainsString('username', $response['errors'][0]['message']);
    }
}
