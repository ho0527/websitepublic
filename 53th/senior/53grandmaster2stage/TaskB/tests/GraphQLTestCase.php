<?php
/**
 * 測試基底類別
 *
 * 每個測試案例執行前都會自動把資料庫還原成初始狀態（試題允許自動或手動還原），
 * 因此每個測試都互相獨立、可重複執行。
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

abstract class GraphQLTestCase extends TestCase
{
    /** 內建管理者帳號 */
    protected const ADMIN_EMAIL    = 'admin@localhost';
    protected const ADMIN_PASSWORD = 'adminpass';

    /** 內建一般會員帳號 */
    protected const USER_EMAIL    = 'user1@localhost';
    protected const USER_PASSWORD = 'user1pass';

    /** 一個格式正確但不存在於資料庫的權杖 */
    protected const INVALID_TOKEN = 'd5563a8962cfc11dd3341a1cb16ee5fbd95c04f00af1916bd37220aea22a6ead';

    /**
     * 每個測試前還原資料庫
     */
    protected function setUp(): void
    {
        parent::setUp();

        Installer::install();
        AuthService::setToken(null);
    }

    /**
     * 執行一次 GraphQL 請求並取得回應陣列
     */
    protected function graphql(string $query, ?string $token = null, array $variables = []): array
    {
        return App::handle($query, $variables, null, $token);
    }

    /**
     * 以指定帳號登入並取得 user_token
     */
    protected function loginAs(string $email, string $password): string
    {
        $response = $this->graphql(
            'mutation userLogin { login(email: "' . $email . '", password: "' . $password . '") { user_token } }'
        );

        $this->assertArrayHasKey('data', $response, '登入應該要成功');

        return $response['data']['login']['user_token'];
    }

    /**
     * 斷言回應為指定的錯誤訊息（且不含 data）
     */
    protected function assertGraphQLError(string $expectedMessage, array $response): void
    {
        $this->assertArrayNotHasKey('data', $response, '錯誤回應不應包含 data');
        $this->assertArrayHasKey('errors', $response, '回應應包含 errors');
        $this->assertSame($expectedMessage, $response['errors'][0]['message']);
    }
}
