<?php
/**
 * 精選房屋相關 API
 * API 14 取得精選房屋列表、API 15 取消精選房屋
 */
class AdController
{
    /** @var PDO 資料庫連線 */
    private PDO $pdo;

    /** @var Auth 身分驗證 */
    private Auth $auth;

    /** @var HouseRepository 房屋資料存取 */
    private HouseRepository $houses;

    public function __construct(PDO $pdo, Auth $auth, HouseRepository $houses)
    {
        $this->pdo    = $pdo;
        $this->auth   = $auth;
        $this->houses = $houses;
    }

    /**
     * API 14：取得精選房屋列表 [GET] /api/ads
     * 僅管理員可使用，只列出仍在有效期內的精選房屋
     */
    public function index(Request $request): void
    {
        $user = $this->auth->user($request->token());
        $this->auth->requireAdmin($user);

        $query = $_GET;

        [$conditions, $bindings] = $this->houses->buildFilters($query);
        $conditions[] = 'ad.expired_at > NOW()';

        $where = ' WHERE ' . implode(' AND ', $conditions);

        $countStatement = $this->pdo->prepare(
            'SELECT COUNT(*) FROM ads ad INNER JOIN houses h ON h.id = ad.house_id' . $where
        );
        $countStatement->execute($bindings);
        $totalCount = (int) $countStatement->fetchColumn();

        $orderColumn    = $this->houses->buildOrderColumn($query);
        $orderDirection = $this->houses->buildOrderDirection($query);
        $page           = $this->houses->buildPage($query);
        $perPage        = $this->houses->perPage();
        $offset         = ($page - 1) * $perPage;

        $sql = "SELECT ad.id, ad.expired_at, h.id AS house_id, h.title, h.price, h.square, h.room
                FROM ads ad
                INNER JOIN houses h ON h.id = ad.house_id
                {$where}
                ORDER BY {$orderColumn} {$orderDirection}, ad.id DESC
                LIMIT {$perPage} OFFSET {$offset}";

        $statement = $this->pdo->prepare($sql);
        $statement->execute($bindings);

        $ads = [];
        foreach ($statement->fetchAll() as $row) {
            $ads[] = [
                'id'         => (int) $row['id'],
                'expired_at' => $row['expired_at'],
                'house'      => $this->houses->formatSummary([
                    'id'     => $row['house_id'],
                    'title'  => $row['title'],
                    'price'  => $row['price'],
                    'square' => $row['square'],
                    'room'   => $row['room'],
                ]),
            ];
        }

        Response::success([
            'ads'         => $ads,
            'total_count' => $totalCount,
        ]);
    }

    /**
     * API 15：取消精選房屋 [DELETE] /api/ads/:ad_id
     * 僅管理員可使用
     */
    public function destroy(Request $request, int $adId): void
    {
        $user = $this->auth->user($request->token());
        $this->auth->requireAdmin($user);

        $statement = $this->pdo->prepare('SELECT id FROM ads WHERE id = :id LIMIT 1');
        $statement->execute([':id' => $adId]);

        if ($statement->fetch() === false) {
            throw new ApiException('MSG_AD_NOT_EXISTS', 404);
        }

        $delete = $this->pdo->prepare('DELETE FROM ads WHERE id = :id');
        $delete->execute([':id' => $adId]);

        Response::success('');
    }
}
