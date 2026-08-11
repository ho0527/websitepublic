<?php
/**
 * 測試項目 2：訪客登出
 *   a. 成功登出
 *   b. 使用者未認證
 */

declare(strict_types=1);

final class LogoutTest extends GraphQLTestCase
{
    /** 登出用的查詢字串 */
    private const LOGOUT_QUERY = 'mutation userLogout {
        logout {
            message
        }
    }';

    /** a. 成功登出時回傳 user logout success */
    public function testLogoutSuccessReturnsMessage(): void
    {
        $token = $this->loginAs(self::ADMIN_EMAIL, self::ADMIN_PASSWORD);

        $response = $this->graphql(self::LOGOUT_QUERY, $token);

        $this->assertArrayNotHasKey('errors', $response);
        $this->assertSame('user logout success', $response['data']['logout']['message']);
    }

    /** a. 登出後資料庫中的權杖應被清除 */
    public function testLogoutClearsStoredToken(): void
    {
        $token = $this->loginAs(self::ADMIN_EMAIL, self::ADMIN_PASSWORD);
        $this->graphql(self::LOGOUT_QUERY, $token);

        $stored = Database::selectOne('SELECT user_token FROM users WHERE email = ?', [self::ADMIN_EMAIL]);

        $this->assertNull($stored['user_token'], '登出後不應保留權杖');
    }

    /** a. 登出後原本的權杖不能再通過認證 */
    public function testTokenIsRejectedAfterLogout(): void
    {
        $token = $this->loginAs(self::ADMIN_EMAIL, self::ADMIN_PASSWORD);
        $this->graphql(self::LOGOUT_QUERY, $token);

        $this->assertGraphQLError('unauthorized user', $this->graphql('query getUser { user { id } }', $token));
    }

    /** a. 一般會員也能正常登出 */
    public function testNormalUserCanLogout(): void
    {
        $token = $this->loginAs(self::USER_EMAIL, self::USER_PASSWORD);

        $response = $this->graphql(self::LOGOUT_QUERY, $token);

        $this->assertSame('user logout success', $response['data']['logout']['message']);
    }

    /** a. 只登出自己，不影響其他已登入的會員 */
    public function testLogoutDoesNotAffectOtherUsers(): void
    {
        $adminToken = $this->loginAs(self::ADMIN_EMAIL, self::ADMIN_PASSWORD);
        $userToken  = $this->loginAs(self::USER_EMAIL, self::USER_PASSWORD);

        $this->graphql(self::LOGOUT_QUERY, $adminToken);

        $response = $this->graphql('query getUser { user { email } }', $userToken);

        $this->assertSame(self::USER_EMAIL, $response['data']['user']['email']);
    }

    /** b. 未帶 Authorization 標頭時回傳 unauthorized user */
    public function testLogoutWithoutTokenReturnsUnauthorized(): void
    {
        $this->assertGraphQLError('unauthorized user', $this->graphql(self::LOGOUT_QUERY));
    }

    /**
     * b. 權杖無效時同樣回傳 unauthorized user
     *
     * @dataProvider invalidTokenProvider
     */
    public function testLogoutWithInvalidTokenReturnsUnauthorized(?string $token): void
    {
        $this->assertGraphQLError('unauthorized user', $this->graphql(self::LOGOUT_QUERY, $token));
    }

    /** @return array<string, array{0:?string}> */
    public static function invalidTokenProvider(): array
    {
        return [
            '空字串權杖'   => [''],
            '未登入的權杖' => [self::INVALID_TOKEN],
            '亂數權杖'     => ['not-a-real-token'],
            '無權杖'       => [null],
        ];
    }

    /** b. 尚未登入的會員，即使權杖等於 sha256(Email) 也不能登出 */
    public function testLogoutWithNotLoggedInTokenReturnsUnauthorized(): void
    {
        $token = hash('sha256', self::ADMIN_EMAIL);

        $this->assertGraphQLError('unauthorized user', $this->graphql(self::LOGOUT_QUERY, $token));
    }
}
