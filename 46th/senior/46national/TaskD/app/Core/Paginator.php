<?php

declare(strict_types=1);

namespace App\Core;

/**
 * 分頁計算與連結產生。
 *
 * 前台訂票查詢每頁 3 筆、後台訂票紀錄每頁 5 筆，兩者共用同一套邏輯。
 */
final class Paginator
{
    /** @var array<int, mixed> 本頁的資料 */
    private array $items;

    private int $totalItems;

    private int $perPage;

    private int $currentPage;

    private string $baseUrl;

    /** @var array<string, mixed> 需要保留在分頁連結上的查詢條件 */
    private array $queryParameters;

    /**
     * @param array<int, mixed>    $items
     * @param array<string, mixed> $queryParameters
     */
    public function __construct(
        array $items,
        int $totalItems,
        int $perPage,
        int $currentPage,
        string $baseUrl,
        array $queryParameters = []
    ) {
        $this->items           = $items;
        $this->totalItems      = $totalItems;
        $this->perPage         = max(1, $perPage);
        $this->currentPage     = $currentPage;
        $this->baseUrl         = $baseUrl;
        $this->queryParameters = $queryParameters;
    }

    /**
     * 依總筆數推算頁碼合法範圍後建立分頁器。
     *
     * @param callable(int $limit, int $offset): array<int, mixed> $itemLoader
     * @param array<string, mixed>                                $queryParameters
     */
    public static function make(
        int $totalItems,
        int $perPage,
        int $requestedPage,
        callable $itemLoader,
        string $baseUrl,
        array $queryParameters = []
    ): Paginator {
        $perPage    = max(1, $perPage);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));
        $page       = min(max(1, $requestedPage), $totalPages);
        $items      = $itemLoader($perPage, ($page - 1) * $perPage);

        return new self($items, $totalItems, $perPage, $page, $baseUrl, $queryParameters);
    }

    /**
     * @return array<int, mixed>
     */
    public function items(): array
    {
        return $this->items;
    }

    public function totalItems(): int
    {
        return $this->totalItems;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function totalPages(): int
    {
        return max(1, (int) ceil($this->totalItems / $this->perPage));
    }

    public function hasPages(): bool
    {
        return $this->totalPages() > 1;
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    /**
     * 產生指定頁碼的連結，並保留目前的查詢條件。
     */
    public function urlForPage(int $page): string
    {
        $parameters         = $this->queryParameters;
        $parameters['page'] = $page;

        return $this->baseUrl . '?' . http_build_query($parameters);
    }

    /**
     * 取得要顯示的頁碼清單。
     *
     * @return array<int, int>
     */
    public function pageNumbers(): array
    {
        return range(1, $this->totalPages());
    }
}
