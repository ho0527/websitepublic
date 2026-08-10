<?php
/**
 * JSON API 控制器
 *
 * 對應試題的：
 *   GET /products.json            產品列表（含分頁與關鍵字查詢）
 *   GET /products/[GTIN].json     單一產品
 *
 * 隱藏的產品不會出現在 API 中，查詢隱藏或不存在的產品一律回傳 404。
 */

declare(strict_types=1);

final class ApiController
{
    private CompanyRepository $companies;

    private ProductRepository $products;

    public function __construct()
    {
        $this->companies = new CompanyRepository();
        $this->products  = new ProductRepository();
    }

    /**
     * GET /products.json
     */
    public function productList(): void
    {
        $keyword     = (string) ($_GET['query'] ?? '');
        $perPage     = ProductRepository::API_PAGE_SIZE;
        $currentPage = max(1, (int) ($_GET['page'] ?? 1));

        $queryOptions = [
            'includeHidden' => false,
            'keyword'       => $keyword,
        ];

        $totalRecords = $this->products->countAll($queryOptions);
        $totalPages   = $totalRecords === 0 ? 0 : (int) ceil($totalRecords / $perPage);

        $productRows = $this->products->findAll($queryOptions + [
            'limit'  => $perPage,
            'offset' => ($currentPage - 1) * $perPage,
        ]);

        $data = [];
        foreach ($productRows as $productRow) {
            $data[] = $this->buildProductPayload($productRow);
        }

        respondWithJson([
            'data'       => $data,
            'pagination' => [
                'current_page'  => $currentPage,
                'total_pages'   => $totalPages,
                'per_page'      => $perPage,
                'next_page_url' => $currentPage < $totalPages
                    ? $this->buildPageUrl($currentPage + 1, $keyword)
                    : null,
                'prev_page_url' => $currentPage > 1
                    ? $this->buildPageUrl($currentPage - 1, $keyword)
                    : null,
            ],
        ]);
    }

    /**
     * GET /products/[GTIN].json
     */
    public function productDetail(string $gtin): void
    {
        // 只找沒有被隱藏的產品，找不到就是 404
        $product = $this->products->findByGtin($gtin, false);

        if ($product === null) {
            respondWithJson(['error' => 'Not Found', 'message' => 'Product not found.'], 404);
        }

        respondWithJson($this->buildProductPayload($product));
    }

    /**
     * 把產品資料列組成試題定義的 JSON 結構（含所屬公司）。
     *
     * @param array<string,mixed> $productRow
     * @return array<string,mixed>
     */
    private function buildProductPayload(array $productRow): array
    {
        $company    = $this->companies->findById((int) $productRow['company_id']);
        $companyApi = $company === null ? [] : $this->companies->toApiArray($company);

        return $this->products->toApiArray($productRow, $companyApi);
    }

    /**
     * 組出分頁用的絕對網址。
     */
    private function buildPageUrl(int $pageNumber, string $keyword): string
    {
        $queryParameters = ['page' => $pageNumber];

        if ($keyword !== '') {
            $queryParameters['query'] = $keyword;
        }

        return currentSchemeAndHost() . urlFor('/products.json', $queryParameters);
    }
}
