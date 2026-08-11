<?php
/**
 * 精選房屋申請相關 API
 * API 10 申請精選房屋、API 11 取消申請、API 12 取得申請列表、API 13 審核申請
 */
class ApplicationController
{
    /** @var PDO 資料庫連線 */
    private PDO $pdo;

    /** @var Auth 身分驗證 */
    private Auth $auth;

    /** @var HouseRepository 房屋資料存取 */
    private HouseRepository $houses;

    /** @var array 系統設定 */
    private array $config;

    public function __construct(PDO $pdo, Auth $auth, HouseRepository $houses, array $config)
    {
        $this->pdo    = $pdo;
        $this->auth   = $auth;
        $this->houses = $houses;
        $this->config = $config;
    }

    /**
     * API 10：申請精選房屋 [POST] /api/application
     * 只能替自己的房屋申請
     */
    public function store(Request $request): void
    {
        $user  = $this->auth->user($request->token());
        $input = $request->all();

        Validator::required($input, ['house_id']);
        Validator::integers($input, ['house_id']);

        $houseId = (int) $input['house_id'];
        $house   = $this->houses->findOrFail($houseId);

        if ((int) $house['user_id'] !== (int) $user['id']) {
            throw new ApiException('MSG_PERMISSION_DENY', 403);
        }

        // 已有審核中的申請
        $pending = $this->pdo->prepare(
            'SELECT id FROM applications WHERE house_id = :house_id AND status IS NULL LIMIT 1'
        );
        $pending->execute([':house_id' => $houseId]);
        if ($pending->fetch() !== false) {
            throw new ApiException('MSG_HOUSE_APPLIED', 409);
        }

        // 已經是有效期內的精選房屋
        $advertised = $this->pdo->prepare(
            'SELECT id FROM ads WHERE house_id = :house_id AND expired_at > NOW() LIMIT 1'
        );
        $advertised->execute([':house_id' => $houseId]);
        if ($advertised->fetch() !== false) {
            throw new ApiException('MSG_HOUSE_ADVERTISED', 409);
        }

        $insert = $this->pdo->prepare(
            'INSERT INTO applications (house_id, status, applied_at) VALUES (:house_id, NULL, NOW())'
        );
        $insert->execute([':house_id' => $houseId]);

        Response::success(['application' => ['id' => (int) $this->pdo->lastInsertId()]]);
    }

    /**
     * API 11：取消申請 [DELETE] /api/application/:application_id
     * 只有申請者本人可以取消，且僅限尚未審核的申請
     */
    public function destroy(Request $request, int $applicationId): void
    {
        $user        = $this->auth->user($request->token());
        $application = $this->findOrFail($applicationId);

        if ((int) $application['user_id'] !== (int) $user['id']) {
            throw new ApiException('MSG_PERMISSION_DENY', 403);
        }

        if ($application['status'] !== null) {
            throw new ApiException('MSG_ALREADY_ADVERTISED', 409);
        }

        $delete = $this->pdo->prepare('DELETE FROM applications WHERE id = :id');
        $delete->execute([':id' => $applicationId]);

        Response::success('');
    }

    /**
     * API 12：取得申請列表 [GET] /api/application
     * 僅管理員可使用，預設只顯示申請中（status = applied）的申請
     */
    public function index(Request $request): void
    {
        $user = $this->auth->user($request->token());
        $this->auth->requireAdmin($user);

        $query = $_GET;

        [$conditions, $bindings] = $this->houses->buildFilters($query);

        // 審核狀態：applied（審核中）/ approved（已同意）/ rejected（已拒絕），預設 applied
        $status = strtolower(trim((string) ($query['status'] ?? 'applied')));
        if ($status === 'approved') {
            $conditions[] = "ap.status = 'APPROVE'";
        } elseif ($status === 'rejected') {
            $conditions[] = "ap.status = 'REJECT'";
        } elseif ($status === 'all') {
            // 不加條件，顯示全部
        } else {
            $conditions[] = 'ap.status IS NULL';
        }

        $where = $conditions === [] ? '' : ' WHERE ' . implode(' AND ', $conditions);

        $countSql = 'SELECT COUNT(*) FROM applications ap INNER JOIN houses h ON h.id = ap.house_id' . $where;
        $countStatement = $this->pdo->prepare($countSql);
        $countStatement->execute($bindings);
        $totalCount = (int) $countStatement->fetchColumn();

        // 申請列表依照申請時間排序，預設降冪
        $orderDirection = $this->houses->buildOrderDirection($query);
        $page           = $this->houses->buildPage($query);
        $perPage        = $this->houses->perPage();
        $offset         = ($page - 1) * $perPage;

        $sql = "SELECT ap.id, ap.status, ap.applied_at, h.id AS house_id, h.title, h.price, h.square, h.room
                FROM applications ap
                INNER JOIN houses h ON h.id = ap.house_id
                {$where}
                ORDER BY ap.applied_at {$orderDirection}, ap.id {$orderDirection}
                LIMIT {$perPage} OFFSET {$offset}";

        $statement = $this->pdo->prepare($sql);
        $statement->execute($bindings);

        $applications = [];
        foreach ($statement->fetchAll() as $row) {
            $applications[] = [
                'id'         => (int) $row['id'],
                'status'     => $row['status'],
                'applied_at' => $row['applied_at'],
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
            'applications' => $applications,
            'total_count'  => $totalCount,
        ]);
    }

    /**
     * API 13：審核申請 [PUT] /api/application/:application_id
     * 同意後系統自動將該房屋設定為精選房屋 7 天
     */
    public function review(Request $request, int $applicationId): void
    {
        $user = $this->auth->user($request->token());
        $this->auth->requireAdmin($user);

        $input = $request->all();
        Validator::required($input, ['approve']);
        $approve = Validator::boolean($input, 'approve');

        $application = $this->findOrFail($applicationId);

        if ($application['status'] !== null) {
            throw new ApiException('MSG_ALREADY_ADVERTISED', 409);
        }

        $this->pdo->beginTransaction();
        try {
            $update = $this->pdo->prepare('UPDATE applications SET status = :status WHERE id = :id');
            $update->execute([
                ':status' => $approve ? 'APPROVE' : 'REJECT',
                ':id'     => $applicationId,
            ]);

            if ($approve) {
                $insert = $this->pdo->prepare(
                    'INSERT INTO ads (house_id, expired_at) VALUES (:house_id, DATE_ADD(NOW(), INTERVAL :days DAY))'
                );
                $insert->bindValue(':house_id', (int) $application['house_id'], PDO::PARAM_INT);
                $insert->bindValue(':days', (int) $this->config['ad_days'], PDO::PARAM_INT);
                $insert->execute();
            }

            $this->pdo->commit();
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }

        Response::success('');
    }

    /**
     * 取得申請（含房屋擁有者），不存在時拋出錯誤
     *
     * @throws ApiException MSG_APPLICATION_NOT_EXISTS (404)
     */
    private function findOrFail(int $applicationId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT ap.*, h.user_id
             FROM applications ap
             INNER JOIN houses h ON h.id = ap.house_id
             WHERE ap.id = :id
             LIMIT 1'
        );
        $statement->execute([':id' => $applicationId]);
        $application = $statement->fetch();

        if ($application === false) {
            throw new ApiException('MSG_APPLICATION_NOT_EXISTS', 404);
        }

        return $application;
    }
}
