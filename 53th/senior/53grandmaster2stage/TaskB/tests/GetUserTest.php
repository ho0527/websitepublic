<?php
/**
 * 測試項目 4：取得會員本身資料
 *   a. 成功取得會員本身資料
 *   b. 使用者未認證
 */

declare(strict_types=1);

final class GetUserTest extends GraphQLTestCase
{
    /** 取得會員資料的查詢字串 */
    private const GET_USER_QUERY = 'query getUser {
        user {
            id
            email
            username
            role
        }
    }';

    /** a. 管理者可取得自己的完整資料 */
    public function testGetUserReturnsAdminProfile(): void
    {
        $token = $this->loginAs(self::ADMIN_EMAIL, self::ADMIN_PASSWORD);

        $response = $this->graphql(self::GET_USER_QUERY, $token);

        $this->assertArrayNotHasKey('errors', $response);
        $this->assertSame(
            ['id' => 1, 'email' => self::ADMIN_EMAIL, 'username' => 'admin', 'role' => 'ADMIN'],
            $response['data']['user']
        );
    }

    /** a. 一般會員取得的是自己的資料而非管理者的 */
    public function testGetUserReturnsOwnProfileForNormalUser(): void
    {
        $token = $this->loginAs(self::USER_EMAIL, self::USER_PASSWORD);

        $response = $this->graphql(self::GET_USER_QUERY, $token);

        $this->assertSame(self::USER_EMAIL, $response['data']['user']['email']);
        $this->assertSame('USER', $response['data']['user']['role']);
    }

    /** a. GraphQL 只回傳被查詢的欄位 */
    public function testGetUserReturnsOnlyRequestedFields(): void
    {
        $token = $this->loginAs(self::ADMIN_EMAIL, self::ADMIN_PASSWORD);

        $response = $this->graphql('query getUser { user { id } }', $token);

        $this->assertSame(['id' => 1], $response['data']['user']);
    }

    /** a. 回傳的 id 必須是數字型別 */
    public function testGetUserIdIsInteger(): void
    {
        $token = $this->loginAs(self::ADMIN_EMAIL, self::ADMIN_PASSWORD);

        $response = $this->graphql(self::GET_USER_QUERY, $token);

        $this->assertIsInt($response['data']['user']['id']);
    }

    /** a. 回應中不應洩漏密碼欄位 */
    public function testGetUserCannotQueryPassword(): void
    {
        $token = $this->loginAs(self::ADMIN_EMAIL, self::ADMIN_PASSWORD);

        $response = $this->graphql('query getUser { user { password } }', $token);

        $this->assertArrayHasKey('errors', $response);
        $this->assertStringContainsString('password', $response['errors'][0]['message']);
    }

    /** b. 未帶 Authorization 標頭時回傳 unauthorized user */
    public function testGetUserWithoutTokenReturnsUnauthorized(): void
    {
        $this->assertGraphQLError('unauthorized user', $this->graphql('query getUser { user { id } }'));
    }

    /**
     * b. 權杖無效時一律回傳 unauthorized user
     *
     * @dataProvider invalidTokenProvider
     */
    public function testGetUserWithInvalidTokenReturnsUnauthorized(?string $token): void
    {
        $this->assertGraphQLError('unauthorized user', $this->graphql('query getUser { user { id } }', $token));
    }

    /** @return array<string, array{0:?string}> */
    public static function invalidTokenProvider(): array
    {
        return [
            '無權杖'         => [null],
            '空字串權杖'     => [''],
            '未登入的權杖'   => [self::INVALID_TOKEN],
            'SQL Injection' => ["' OR '1'='1"],
        ];
    }

    /** b. 錯誤回應不應包含 data 欄位 */
    public function testUnauthorizedResponseHasNoDataKey(): void
    {
        $response = $this->graphql('query getUser { user { id } }');

        $this->assertArrayNotHasKey('data', $response);
        $this->assertCount(1, $response['errors']);
    }
}
