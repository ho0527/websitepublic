<?php
/**
 * 房屋相關 API
 * API 4 房屋列表、API 5 查看房屋、API 6 自己的刊登列表、
 * API 7 刊登房屋、API 8 編輯房屋、API 9 刪除房屋
 */
class HouseController
{
    /** 刊登 / 編輯房屋的必填欄位 */
    private const REQUIRED_FIELDS = [
        'title', 'description', 'price', 'square', 'room',
        'floor', 'total_floor', 'age', 'address',
    ];

    /** 數值型欄位 */
    private const INTEGER_FIELDS = ['price', 'room', 'floor', 'total_floor', 'age'];

    /** @var PDO 資料庫連線 */
    private PDO $pdo;

    /** @var Auth 身分驗證 */
    private Auth $auth;

    /** @var HouseRepository 房屋資料存取 */
    private HouseRepository $houses;

    /** @var ImageService 圖片處理 */
    private ImageService $images;

    public function __construct(PDO $pdo, Auth $auth, HouseRepository $houses, ImageService $images)
    {
        $this->pdo    = $pdo;
        $this->auth   = $auth;
        $this->houses = $houses;
        $this->images = $images;
    }

    /**
     * API 4：取得房屋列表 [GET] /api/house
     * 精選房屋排在最上方，並依照搜尋、排序條件與分頁回傳
     */
    public function index(Request $request): void
    {
        Response::success($this->queryList($request, null));
    }

    /**
     * API 6：取得自己刊登的房屋列表 [GET] /api/user/house
     * 額外回傳審核中的申請編號 application_id
     */
    public function mine(Request $request): void
    {
        $user = $this->auth->user($request->token());

        Response::success($this->queryList($request, (int) $user['id']));
    }

    /**
     * 房屋列表查詢共用流程
     *
     * @param int|null $ownerId 指定時只取該使用者刊登的房屋
     */
    private function queryList(Request $request, ?int $ownerId): array
    {
        $query = $_GET;

        [$conditions, $bindings] = $this->houses->buildFilters($query);

        if ($ownerId !== null) {
            $conditions[]         = 'h.user_id = :owner_id';
            $bindings[':owner_id'] = $ownerId;
        }

        $where = $conditions === [] ? '' : ' WHERE ' . implode(' AND ', $conditions);

        // 總筆數（供分頁切換器使用）
        $countStatement = $this->pdo->prepare('SELECT COUNT(*) FROM houses h' . $where);
        $countStatement->execute($bindings);
        $totalCount = (int) $countStatement->fetchColumn();

        $orderColumn    = $this->houses->buildOrderColumn($query);
        $orderDirection = $this->houses->buildOrderDirection($query);
        $page           = $this->houses->buildPage($query);
        $perPage        = $this->houses->perPage();
        $offset         = ($page - 1) * $perPage;
        $isAd           = $this->houses->isAdExpression();

        // 精選房屋優先，其餘依照排序條件
        $sql = "SELECT h.id, h.title, h.price, h.square, h.room, {$isAd} AS is_ad
                FROM houses h
                {$where}
                ORDER BY is_ad DESC, {$orderColumn} {$orderDirection}, h.id DESC
                LIMIT {$perPage} OFFSET {$offset}";

        $statement = $this->pdo->prepare($sql);
        $statement->execute($bindings);

        $houses = [];
        foreach ($statement->fetchAll() as $row) {
            $house           = $this->houses->formatSummary($row);
            $house['is_ad']  = (bool) $row['is_ad'];

            if ($ownerId !== null) {
                $house['application_id'] = $this->pendingApplicationId((int) $row['id']);
            }

            $houses[] = $house;
        }

        return [
            'houses'      => $houses,
            'total_count' => $totalCount,
        ];
    }

    /** 取得房屋審核中的申請編號，沒有則為 null */
    private function pendingApplicationId(int $houseId): ?int
    {
        $statement = $this->pdo->prepare(
            'SELECT id FROM applications WHERE house_id = :house_id AND status IS NULL ORDER BY id DESC LIMIT 1'
        );
        $statement->execute([':house_id' => $houseId]);
        $id = $statement->fetchColumn();

        return $id === false ? null : (int) $id;
    }

    /**
     * API 5：查看房屋 [GET] /api/house/:house_id
     */
    public function show(Request $request, int $houseId): void
    {
        $house = $this->houses->findOrFail($houseId);

        Response::success([
            'id'           => (int) $house['id'],
            'title'        => $house['title'],
            'images'       => $this->houses->imageUrls($houseId),
            'description'  => $house['description'],
            'price'        => (int) $house['price'],
            'square'       => (int) $house['square'],
            'room'         => (int) $house['room'],
            'floor'        => (int) $house['floor'],
            'total_floor'  => (int) $house['total_floor'],
            'age'          => (int) $house['age'],
            'address'      => $house['address'],
            'published_at' => $house['published_at'],
            'is_ad'        => $this->isAd($houseId),
            'publisher'    => [
                'id'       => (int) $house['user_id'],
                'nickname' => $house['publisher_nickname'],
                'email'    => $house['publisher_email'],
            ],
        ]);
    }

    /** 房屋目前是否為有效期內的精選房屋 */
    private function isAd(int $houseId): bool
    {
        $statement = $this->pdo->prepare(
            'SELECT 1 FROM ads WHERE house_id = :house_id AND expired_at > NOW() LIMIT 1'
        );
        $statement->execute([':house_id' => $houseId]);

        return $statement->fetchColumn() !== false;
    }

    /**
     * API 7：刊登房屋 [POST] /api/house
     */
    public function store(Request $request): void
    {
        $user  = $this->auth->user($request->token());
        $input = $request->all();

        Validator::required($input, self::REQUIRED_FIELDS);
        Validator::strings($input, ['title', 'description', 'address']);
        Validator::integers($input, self::INTEGER_FIELDS);
        Validator::numerics($input, ['square']);

        // 先驗證封面索引再儲存檔案，避免驗證失敗時留下無用的圖片
        $imageFiles = $request->files('images');
        $coverIndex = $this->resolveCoverIndex($input, count($imageFiles));
        $uploaded   = $this->images->store($imageFiles);
        $finalPaths = $this->arrangeImages($input, [], $uploaded);

        $this->pdo->beginTransaction();
        try {
            $insert = $this->pdo->prepare(
                'INSERT INTO houses
                    (user_id, title, description, price, square, room, floor, total_floor, age, address, published_at)
                 VALUES
                    (:user_id, :title, :description, :price, :square, :room, :floor, :total_floor, :age, :address, NOW())'
            );
            $insert->execute([
                ':user_id'     => $user['id'],
                ':title'       => (string) $input['title'],
                ':description' => (string) $input['description'],
                ':price'       => (int) $input['price'],
                ':square'      => (float) $input['square'],
                ':room'        => (int) $input['room'],
                ':floor'       => (int) $input['floor'],
                ':total_floor' => (int) $input['total_floor'],
                ':age'         => (int) $input['age'],
                ':address'     => (string) $input['address'],
            ]);

            $houseId = (int) $this->pdo->lastInsertId();
            $this->saveImages($houseId, $finalPaths, $coverIndex);

            $this->pdo->commit();
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }

        Response::success(['id' => $houseId]);
    }

    /**
     * API 8：編輯房屋 [PUT] /api/house/:house_id
     * 圖片處理規則：keep_paths[] 為保留的既有圖片（依順序），images[] 為新上傳的圖片，
     * 兩者串接後即為最終順序，cover_index 指向串接後的索引
     */
    public function update(Request $request, int $houseId): void
    {
        $user  = $this->auth->user($request->token());
        $house = $this->houses->findOrFail($houseId);

        if ((int) $house['user_id'] !== (int) $user['id']) {
            throw new ApiException('MSG_PERMISSION_DENY', 403);
        }

        $input = $request->all();

        Validator::required($input, self::REQUIRED_FIELDS);
        Validator::strings($input, ['title', 'description', 'address']);
        Validator::integers($input, self::INTEGER_FIELDS);
        Validator::numerics($input, ['square']);

        // 保留的既有圖片：沒有指定時代表沿用目前全部圖片
        $currentPaths = $this->currentImagePaths($houseId);
        $keepPaths    = $this->normalizeKeepPaths($input, $currentPaths);
        $imageFiles   = $request->files('images');

        // 先驗證封面索引再儲存檔案，避免驗證失敗時留下無用的圖片
        $coverIndex = $this->resolveCoverIndex($input, count($keepPaths) + count($imageFiles));
        $uploaded   = $this->images->store($imageFiles);
        $finalPaths = $this->arrangeImages($input, $keepPaths, $uploaded);

        $this->pdo->beginTransaction();
        try {
            $update = $this->pdo->prepare(
                'UPDATE houses SET
                    title = :title, description = :description, price = :price, square = :square,
                    room = :room, floor = :floor, total_floor = :total_floor, age = :age, address = :address
                 WHERE id = :id'
            );
            $update->execute([
                ':title'       => (string) $input['title'],
                ':description' => (string) $input['description'],
                ':price'       => (int) $input['price'],
                ':square'      => (float) $input['square'],
                ':room'        => (int) $input['room'],
                ':floor'       => (int) $input['floor'],
                ':total_floor' => (int) $input['total_floor'],
                ':age'         => (int) $input['age'],
                ':address'     => (string) $input['address'],
                ':id'          => $houseId,
            ]);

            $this->saveImages($houseId, $finalPaths, $coverIndex);

            $this->pdo->commit();
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }

        // 被移除的圖片同時刪除實體檔案
        foreach (array_diff($currentPaths, $finalPaths) as $removed) {
            $this->images->remove($removed);
        }

        Response::success('');
    }

    /**
     * 決定圖片的最終顯示順序
     * 若前端有傳入 order[]（元素為 keep:<路徑> 或 new:<上傳索引>），依其順序排列；
     * 否則為「保留的既有圖片」接上「新上傳的圖片」
     *
     * @param string[] $keepPaths 保留的既有圖片相對路徑
     * @param string[] $uploaded  新上傳的圖片相對路徑
     * @return string[]
     */
    private function arrangeImages(array $input, array $keepPaths, array $uploaded): array
    {
        $order = $input['order'] ?? null;
        if (!is_array($order) || $order === []) {
            return array_merge($keepPaths, $uploaded);
        }

        $arranged = [];

        foreach ($order as $token) {
            $token = (string) $token;

            if (strpos($token, 'new:') === 0) {
                $index = (int) substr($token, 4);
                if (isset($uploaded[$index])) {
                    $arranged[] = $uploaded[$index];
                }
                continue;
            }

            if (strpos($token, 'keep:') === 0) {
                $value    = substr($token, 5);
                $relative = ltrim((string) parse_url($value, PHP_URL_PATH), '/');
                foreach ($keepPaths as $path) {
                    if ($path === $value || ($relative !== '' && substr($relative, -strlen($path)) === $path)) {
                        $arranged[] = $path;
                        break;
                    }
                }
            }
        }

        // 沒有被列入順序的圖片仍附加在後面，避免資料遺失
        foreach (array_merge($keepPaths, $uploaded) as $path) {
            if (!in_array($path, $arranged, true)) {
                $arranged[] = $path;
            }
        }

        return $arranged;
    }

    /**
     * 取得房屋目前的圖片相對路徑（依排序）
     *
     * @return string[]
     */
    private function currentImagePaths(int $houseId): array
    {
        $statement = $this->pdo->prepare('SELECT path FROM images WHERE house_id = :house_id ORDER BY sort_order ASC');
        $statement->execute([':house_id' => $houseId]);

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * 整理要保留的既有圖片路徑
     * 前端可傳入完整網址或相對路徑，這裡統一轉為資料庫中的相對路徑
     */
    private function normalizeKeepPaths(array $input, array $currentPaths): array
    {
        if (!array_key_exists('keep_paths', $input)) {
            return $currentPaths;
        }

        $requested = $input['keep_paths'];
        if (is_string($requested)) {
            $requested = $requested === '' ? [] : [$requested];
        }
        if (!is_array($requested)) {
            return $currentPaths;
        }

        $kept = [];
        foreach ($requested as $value) {
            $relative = ltrim((string) parse_url((string) $value, PHP_URL_PATH), '/');
            foreach ($currentPaths as $path) {
                // 以結尾比對，讓完整網址與相對路徑都能對應到同一筆資料
                if ($path === $value || ($relative !== '' && substr($relative, -strlen($path)) === $path)) {
                    $kept[] = $path;
                    break;
                }
            }
        }

        return $kept;
    }

    /**
     * 驗證並取得封面索引
     *
     * @throws ApiException MSG_INVALID_COVER_INDEX (400)
     */
    private function resolveCoverIndex(array $input, int $imageCount): int
    {
        if (!array_key_exists('cover_index', $input) || $input['cover_index'] === '' || $input['cover_index'] === null) {
            return 0;
        }

        $value = $input['cover_index'];
        if (!preg_match('/^\d+$/', (string) $value)) {
            throw new ApiException('MSG_INVALID_COVER_INDEX', 400);
        }

        $index = (int) $value;
        if ($imageCount === 0 || $index >= $imageCount) {
            throw new ApiException('MSG_INVALID_COVER_INDEX', 400);
        }

        return $index;
    }

    /**
     * 重新寫入房屋圖片（排序由 0 開始，封面以 is_cover 標記）
     *
     * @param string[] $paths 依顯示順序排列的圖片相對路徑
     */
    private function saveImages(int $houseId, array $paths, int $coverIndex): void
    {
        $delete = $this->pdo->prepare('DELETE FROM images WHERE house_id = :house_id');
        $delete->execute([':house_id' => $houseId]);

        if ($paths === []) {
            return;
        }

        $insert = $this->pdo->prepare(
            'INSERT INTO images (house_id, sort_order, path, is_cover) VALUES (:house_id, :sort_order, :path, :is_cover)'
        );

        foreach (array_values($paths) as $index => $path) {
            $insert->execute([
                ':house_id'   => $houseId,
                ':sort_order' => $index,
                ':path'       => $path,
                ':is_cover'   => $index === $coverIndex ? 1 : 0,
            ]);
        }
    }

    /**
     * API 9：刪除房屋 [DELETE] /api/house/:house_id
     */
    public function destroy(Request $request, int $houseId): void
    {
        $user  = $this->auth->user($request->token());
        $house = $this->houses->findOrFail($houseId);

        if ((int) $house['user_id'] !== (int) $user['id']) {
            throw new ApiException('MSG_PERMISSION_DENY', 403);
        }

        $this->pdo->beginTransaction();
        try {
            // 先刪除實體圖片檔，再清除關聯資料
            $statement = $this->pdo->prepare('SELECT path FROM images WHERE house_id = :house_id');
            $statement->execute([':house_id' => $houseId]);
            foreach ($statement->fetchAll(PDO::FETCH_COLUMN) as $path) {
                $this->images->remove((string) $path);
            }

            foreach (['DELETE FROM images WHERE house_id = :id',
                      'DELETE FROM ads WHERE house_id = :id',
                      'DELETE FROM applications WHERE house_id = :id',
                      'DELETE FROM houses WHERE id = :id'] as $sql) {
                $this->pdo->prepare($sql)->execute([':id' => $houseId]);
            }

            $this->pdo->commit();
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }

        Response::success('');
    }
}
