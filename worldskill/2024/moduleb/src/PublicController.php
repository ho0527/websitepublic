<?php
/**
 * 公開頁面控制器
 *
 * 對應試題的兩個公開頁面：
 *   1. GTIN 批量驗證頁面
 *   2. 公開產品頁面 /01/[GTIN]
 */

declare(strict_types=1);

final class PublicController
{
    private ProductRepository $products;

    public function __construct()
    {
        $this->products = new ProductRepository();
    }

    /**
     * GTIN 批量驗證頁面（GET 顯示表單，POST 顯示驗證結果）。
     */
    public function gtinVerification(): void
    {
        $submittedText = (string) ($_POST['gtin_list'] ?? '');
        $results       = null;
        $allValid      = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $results  = $this->verifyGtinList($submittedText);
            $allValid = $results !== [] && !in_array(false, array_column($results, 'isValid'), true);
        }

        View::renderAdminPage('gtinverify', 'GTIN Bulk Verification', [
            'submittedText' => $submittedText,
            'results'       => $results,
            'allValid'      => $allValid,
        ]);
    }

    /**
     * 公開產品頁面 /01/[GTIN]。
     */
    public function publicProductPage(string $gtin): void
    {
        // 公開頁面看不到被隱藏的產品
        $product = $this->products->findByGtin($gtin, false);

        if ($product === null) {
            respondWithErrorPage(404, 'Not Found', 'This product is not available.');
        }

        // 語言選擇：預設英文，?lang=fr 切換成法文
        $localeCode = strtolower((string) ($_GET['lang'] ?? 'en'));

        if (!in_array($localeCode, ProductRepository::SUPPORTED_LOCALES, true)) {
            $localeCode = 'en';
        }

        View::render('publicproduct', [
            'product'    => $product,
            'localeCode' => $localeCode,
        ]);
    }

    /**
     * 逐行檢查使用者輸入的 GTIN。
     *
     * 有效的定義：格式正確、存在於資料庫、而且沒有被隱藏。
     *
     * @return array<int,array{gtin:string,isValid:bool,reason:string,productName:string}>
     */
    private function verifyGtinList(string $submittedText): array
    {
        $lines   = preg_split('/\r\n|\r|\n/', $submittedText) ?: [];
        $results = [];

        foreach ($lines as $line) {
            $gtin = trim($line);

            if ($gtin === '') {
                continue;
            }

            if (!isValidGtinFormat($gtin)) {
                $results[] = [
                    'gtin'        => $gtin,
                    'isValid'     => false,
                    'reason'      => 'Invalid format (must be 13 or 14 digits)',
                    'productName' => '',
                ];

                continue;
            }

            $product = $this->products->findByGtin($gtin, false);

            $results[] = [
                'gtin'        => $gtin,
                'isValid'     => $product !== null,
                'reason'      => $product !== null ? 'Registered' : 'Not registered',
                'productName' => $product === null ? '' : (string) ($product['translations']['en']['name'] ?? ''),
            ];
        }

        return $results;
    }
}
