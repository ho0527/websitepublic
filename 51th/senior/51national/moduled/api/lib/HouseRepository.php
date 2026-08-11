<?php
/**
 * 房屋資料存取
 * 集中處理搜尋條件、排序、分頁與房屋欄位組裝，供房屋列表 / 刊登列表 / 申請列表 / 精選列表共用
 */
class HouseRepository
{
    /** @var PDO 資料庫連線 */
    private PDO $pdo;

    /** @var array 系統設定 */
    private array $config;

    /** @var string 圖片網址前綴 */
    private string $baseUrl;

    public function __construct(PDO $pdo, array $config, string $baseUrl)
    {
        $this->pdo     = $pdo;
        $this->config  = $config;
        $this->baseUrl = $baseUrl;
    }

    /**
     * 依查詢字串組出 WHERE 條件
     *
     * @param array  $query 查詢字串參數
     * @param string $alias 房屋資料表別名
     * @return array{0: string[], 1: array} [條件片段, 綁定參數]
     */
    public function buildFilters(array $query, string $alias = 'h'): array
    {
        $conditions = [];
        $bindings   = [];

        // 標題關鍵字
        $title = trim((string) ($query['title'] ?? ''));
        if ($title !== '') {
            $conditions[]      = "{$alias}.title LIKE :title";
            $bindings[':title'] = '%' . $title . '%';
        }

        // 價格區間
        if (isset($query['min_price']) && is_numeric($query['min_price'])) {
            $conditions[]          = "{$alias}.price >= :min_price";
            $bindings[':min_price'] = (int) $query['min_price'];
        }
        if (isset($query['max_price']) && is_numeric($query['max_price'])) {
            $conditions[]          = "{$alias}.price <= :max_price";
            $bindings[':max_price'] = (int) $query['max_price'];
        }

        // 房數：room 為完全相符；另提供 min_room / max_room 供前端「3 房以上」使用
        if (isset($query['room']) && is_numeric($query['room'])) {
            $conditions[]      = "{$alias}.room = :room";
            $bindings[':room'] = (int) $query['room'];
        }
        if (isset($query['min_room']) && is_numeric($query['min_room'])) {
            $conditions[]          = "{$alias}.room >= :min_room";
            $bindings[':min_room'] = (int) $query['min_room'];
        }
        if (isset($query['max_room']) && is_numeric($query['max_room'])) {
            $conditions[]          = "{$alias}.room <= :max_room";
            $bindings[':max_room'] = (int) $query['max_room'];
        }

        // 屋齡區間
        if (isset($query['min_age']) && is_numeric($query['min_age'])) {
            $conditions[]         = "{$alias}.age >= :min_age";
            $bindings[':min_age'] = (int) $query['min_age'];
        }
        if (isset($query['max_age']) && is_numeric($query['max_age'])) {
            $conditions[]         = "{$alias}.age <= :max_age";
            $bindings[':max_age'] = (int) $query['max_age'];
        }

        return [$conditions, $bindings];
    }

    /**
     * 取得排序欄位（白名單，避免 SQL injection）
     *
     * @param array  $query   查詢字串參數
     * @param string $alias   房屋資料表別名
     * @param string $default 預設排序欄位
     */
    public function buildOrderColumn(array $query, string $alias = 'h', string $default = 'published_at'): string
    {
        $allowed = [
            'published_at' => "{$alias}.published_at",
            'price'        => "{$alias}.price",
            'square'       => "{$alias}.square",
        ];

        $sortBy = (string) ($query['sort_by'] ?? $default);

        return $allowed[$sortBy] ?? $allowed[$default];
    }

    /** 取得排序方向（僅允許 asc / desc，預設 desc） */
    public function buildOrderDirection(array $query, string $default = 'desc'): string
    {
        $order = strtolower((string) ($query['order'] ?? $default));

        return $order === 'asc' ? 'ASC' : 'DESC';
    }

    /** 取得頁碼（最小為 1） */
    public function buildPage(array $query): int
    {
        $page = (int) ($query['page'] ?? 1);

        return $page > 0 ? $page : 1;
    }

    /** 每頁筆數 */
    public function perPage(): int
    {
        return (int) $this->config['per_page'];
    }

    /**
     * 房屋是否為有效期內的精選房屋（SQL 片段）
     */
    public function isAdExpression(string $alias = 'h'): string
    {
        return "EXISTS (SELECT 1 FROM ads a WHERE a.house_id = {$alias}.id AND a.expired_at > NOW())";
    }

    /**
     * 取得房屋的封面圖片網址
     */
    public function coverImageUrl(int $houseId): ?string
    {
        $statement = $this->pdo->prepare(
            'SELECT path FROM images WHERE house_id = :house_id ORDER BY is_cover DESC, sort_order ASC LIMIT 1'
        );
        $statement->execute([':house_id' => $houseId]);
        $path = $statement->fetchColumn();

        return $path === false ? null : $this->toUrl((string) $path);
    }

    /** 取得房屋的全部圖片網址（依排序） */
    public function imageUrls(int $houseId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT path FROM images WHERE house_id = :house_id ORDER BY sort_order ASC'
        );
        $statement->execute([':house_id' => $houseId]);

        return array_map([$this, 'toUrl'], $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    /** 將圖片相對路徑轉為完整網址 */
    public function toUrl(string $path): string
    {
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        return $this->baseUrl . '/' . ltrim($path, '/');
    }

    /**
     * 將房屋資料列組成列表用的欄位
     *
     * @param array $row 需含 id/title/price/square/room，可含 is_ad
     */
    public function formatSummary(array $row): array
    {
        return [
            'id'              => (int) $row['id'],
            'title'           => $row['title'],
            'cover_image_url' => $this->coverImageUrl((int) $row['id']),
            'price'           => (int) $row['price'],
            'square'          => (int) $row['square'],
            'room'            => (int) $row['room'],
        ];
    }

    /**
     * 依 id 取得房屋（含刊登者），不存在時回傳 null
     */
    public function find(int $houseId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT h.*, u.nickname AS publisher_nickname, u.email AS publisher_email
             FROM houses h
             INNER JOIN users u ON u.id = h.user_id
             WHERE h.id = :id
             LIMIT 1'
        );
        $statement->execute([':id' => $houseId]);
        $house = $statement->fetch();

        return $house === false ? null : $house;
    }

    /**
     * 依 id 取得房屋，不存在時拋出錯誤
     *
     * @throws ApiException MSG_HOUSE_NOT_EXISTS (404)
     */
    public function findOrFail(int $houseId): array
    {
        $house = $this->find($houseId);

        if ($house === null) {
            throw new ApiException('MSG_HOUSE_NOT_EXISTS', 404);
        }

        return $house;
    }
}
