<?php
/**
 * 應用程式核心：把「查詢字串 + 權杖」轉成 GraphQL 回應陣列。
 *
 * 這一層刻意與 HTTP 解耦，模組 B 的單元測試可以直接呼叫 App::handle()，
 * 不需要真的啟動網頁伺服器即可測試完整流程。
 */
class App
{
    /**
     * 執行一次 GraphQL 請求
     *
     * @param  string  $query         查詢字串
     * @param  array   $variables     變數
     * @param  ?string $operationName 指定操作名稱
     * @param  ?string $token         Authorization: Bearer 之後的權杖
     * @return array   成功時為 ['data' => ...]，失敗時為 ['errors' => [['message' => ...]]]
     */
    public static function handle(string $query, array $variables = [], ?string $operationName = null, ?string $token = null): array
    {
        AuthService::setToken($token);

        try {
            if (trim($query) === '') {
                throw new GraphQLError('syntax error: query is required');
            }

            $operations = (new GraphQLParser())->parse($query);
            $executor   = new GraphQLExecutor(Schema::build());
            $data       = $executor->execute($operations, $variables, $operationName);

            return ['data' => $data];
        } catch (GraphQLError $error) {
            return ['errors' => [['message' => $error->getMessage()]]];
        } catch (Throwable $error) {
            // 未預期的錯誤（例如資料庫連線失敗）同樣以 GraphQL 錯誤格式回應
            return ['errors' => [['message' => $error->getMessage()]]];
        }
    }

    /**
     * 從 Authorization 標頭取出 Bearer 權杖
     */
    public static function extractBearerToken(?string $authorizationHeader): ?string
    {
        if ($authorizationHeader === null) {
            return null;
        }

        if (preg_match('/^\s*Bearer\s+(\S+)\s*$/i', $authorizationHeader, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }
}
