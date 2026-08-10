<?php
/**
 * 產品資料存取類別
 *
 * 產品的多語系資訊（英文 / 法文的名稱與描述）存放在 product_translations 資料表，
 * 以 locale_code 區分，日後要加入新的語言只需新增資料列，不必改動資料表結構。
 */

declare(strict_types=1);

final class ProductRepository
{
    /** 支援的語系代碼，順序即為畫面上的顯示順序 */
    public const SUPPORTED_LOCALES = ['en', 'fr'];

    /** JSON API 每頁筆數 */
    public const API_PAGE_SIZE = 10;

    /**
     * 查詢產品清單。
     *
     * @param array{
     *     includeHidden?: bool,
     *     onlyHidden?: bool,
     *     companyId?: int|null,
     *     keyword?: string,
     *     limit?: int|null,
     *     offset?: int
     * } $options
     * @return array<int,array<string,mixed>>
     */
    public function findAll(array $options = []): array
    {
        [$whereSql, $parameters] = $this->buildWhere($options);

        $limitSql = '';
        if (isset($options['limit']) && $options['limit'] !== null) {
            // limit / offset 已在呼叫端轉為整數，直接內嵌不會有注入風險
            $limitSql = sprintf(' LIMIT %d OFFSET %d', (int) $options['limit'], (int) ($options['offset'] ?? 0));
        }

        $rows = Database::run(
            "SELECT p.*, c.name AS company_name, c.is_active AS company_is_active
               FROM products AS p
               INNER JOIN companies AS c ON c.id = p.company_id
              {$whereSql}
              ORDER BY p.id ASC{$limitSql}",
            $parameters
        )->fetchAll();

        return $this->attachTranslations($rows);
    }

    /**
     * 計算符合條件的產品筆數（給分頁使用）。
     *
     * @param array<string,mixed> $options 與 findAll() 相同
     */
    public function countAll(array $options = []): int
    {
        [$whereSql, $parameters] = $this->buildWhere($options);

        return (int) Database::run(
            "SELECT COUNT(*)
               FROM products AS p
               INNER JOIN companies AS c ON c.id = p.company_id
              {$whereSql}",
            $parameters
        )->fetchColumn();
    }

    /**
     * 依 GTIN 取得單一產品。
     *
     * @param bool $includeHidden 是否允許取得已隱藏的產品（後台為 true、公開頁面為 false）
     * @return array<string,mixed>|null
     */
    public function findByGtin(string $gtin, bool $includeHidden = true): ?array
    {
        $condition = $includeHidden ? '' : ' AND p.is_hidden = 0';

        $row = Database::run(
            "SELECT p.*, c.name AS company_name, c.is_active AS company_is_active
               FROM products AS p
               INNER JOIN companies AS c ON c.id = p.company_id
              WHERE p.gtin = ?{$condition}",
            [$gtin]
        )->fetch();

        if ($row === false) {
            return null;
        }

        $rows = $this->attachTranslations([$row]);

        return $rows[0];
    }

    /**
     * GTIN 是否已被其他產品使用。
     *
     * @param int|null $excludeProductId 編輯時要排除自己
     */
    public function isGtinTaken(string $gtin, ?int $excludeProductId = null): bool
    {
        if ($excludeProductId === null) {
            $count = Database::run('SELECT COUNT(*) FROM products WHERE gtin = ?', [$gtin])->fetchColumn();
        } else {
            $count = Database::run(
                'SELECT COUNT(*) FROM products WHERE gtin = ? AND id <> ?',
                [$gtin, $excludeProductId]
            )->fetchColumn();
        }

        return (int) $count > 0;
    }

    /**
     * 新增產品，回傳新產品 id。
     *
     * @param array<string,mixed> $input 已整理過的表單資料
     */
    public function create(array $input): int
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            Database::run(
                'INSERT INTO products
                        (company_id, gtin, brand, country_of_origin, gross_weight, net_weight, weight_unit, image_path, is_hidden)
                 VALUES (:company_id, :gtin, :brand, :country, :gross_weight, :net_weight, :weight_unit, :image_path, :is_hidden)',
                [
                    ':company_id'   => $input['company_id'],
                    ':gtin'         => $input['gtin'],
                    ':brand'        => $input['brand'],
                    ':country'      => $input['country_of_origin'],
                    ':gross_weight' => $input['gross_weight'],
                    ':net_weight'   => $input['net_weight'],
                    ':weight_unit'  => $input['weight_unit'],
                    ':image_path'   => $input['image_path'],
                    ':is_hidden'    => $input['is_hidden'],
                ]
            );

            $productId = (int) $pdo->lastInsertId();
            $this->saveTranslations($productId, $input['translations']);

            $pdo->commit();
        } catch (Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }

        return $productId;
    }

    /**
     * 更新產品。
     *
     * @param array<string,mixed> $input 已整理過的表單資料
     */
    public function update(int $productId, array $input): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            Database::run(
                'UPDATE products
                    SET company_id = :company_id,
                        gtin = :gtin,
                        brand = :brand,
                        country_of_origin = :country,
                        gross_weight = :gross_weight,
                        net_weight = :net_weight,
                        weight_unit = :weight_unit,
                        image_path = :image_path,
                        is_hidden = :is_hidden
                  WHERE id = :id',
                [
                    ':company_id'   => $input['company_id'],
                    ':gtin'         => $input['gtin'],
                    ':brand'        => $input['brand'],
                    ':country'      => $input['country_of_origin'],
                    ':gross_weight' => $input['gross_weight'],
                    ':net_weight'   => $input['net_weight'],
                    ':weight_unit'  => $input['weight_unit'],
                    ':image_path'   => $input['image_path'],
                    ':is_hidden'    => $input['is_hidden'],
                    ':id'           => $productId,
                ]
            );

            $this->saveTranslations($productId, $input['translations']);

            $pdo->commit();
        } catch (Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }
    }

    /**
     * 設定產品的隱藏狀態。
     */
    public function setHidden(int $productId, bool $isHidden): void
    {
        Database::run('UPDATE products SET is_hidden = ? WHERE id = ?', [$isHidden ? 1 : 0, $productId]);
    }

    /**
     * 僅更新圖片欄位（用於上傳、更換、移除圖片）。
     */
    public function updateImagePath(int $productId, ?string $imagePath): void
    {
        Database::run('UPDATE products SET image_path = ? WHERE id = ?', [$imagePath, $productId]);
    }

    /**
     * 永久刪除產品。
     *
     * 只允許刪除已隱藏的產品，這個條件同時寫在 SQL 的 WHERE 中做最後把關。
     *
     * @return bool 真的有刪除到資料才回傳 true
     */
    public function deleteHiddenProduct(int $productId): bool
    {
        $statement = Database::run('DELETE FROM products WHERE id = ? AND is_hidden = 1', [$productId]);

        return $statement->rowCount() > 0;
    }

    /**
     * 把產品資料轉成試題指定的 JSON 結構。
     *
     * @param array<string,mixed> $product 由 findAll()／findByGtin() 取得的資料列
     * @param array<string,mixed> $companyApiArray 已轉換好的公司 JSON 結構
     * @return array<string,mixed>
     */
    public function toApiArray(array $product, array $companyApiArray): array
    {
        $names        = [];
        $descriptions = [];

        foreach (self::SUPPORTED_LOCALES as $localeCode) {
            $names[$localeCode]        = $product['translations'][$localeCode]['name'] ?? '';
            $descriptions[$localeCode] = $product['translations'][$localeCode]['description'] ?? '';
        }

        return [
            'name'            => $names,
            'description'     => $descriptions,
            'gtin'            => $product['gtin'],
            'brand'           => $product['brand'],
            'countryOfOrigin' => $product['country_of_origin'],
            'weight'          => [
                'gross' => $product['gross_weight'] === null ? null : (float) $product['gross_weight'],
                'net'   => $product['net_weight'] === null ? null : (float) $product['net_weight'],
                'unit'  => $product['weight_unit'],
            ],
            'company'         => $companyApiArray,
        ];
    }

    /**
     * 組出 WHERE 條件與參數。
     *
     * @param array<string,mixed> $options
     * @return array{0:string,1:array<string,mixed>}
     */
    private function buildWhere(array $options): array
    {
        $conditions = [];
        $parameters = [];

        if (($options['onlyHidden'] ?? false) === true) {
            $conditions[] = 'p.is_hidden = 1';
        } elseif (($options['includeHidden'] ?? true) === false) {
            $conditions[] = 'p.is_hidden = 0';
        }

        if (!empty($options['companyId'])) {
            $conditions[]             = 'p.company_id = :company_id';
            $parameters[':company_id'] = (int) $options['companyId'];
        }

        $keyword = trim((string) ($options['keyword'] ?? ''));
        if ($keyword !== '') {
            // 以 EXISTS 搜尋任一語系的名稱或描述，涵蓋 name / name(fr) / description / description(fr)
            // 關閉模擬預處理時同名參數不可重複使用，因此名稱與描述各用一個參數
            $conditions[] = 'EXISTS (
                SELECT 1 FROM product_translations AS t
                 WHERE t.product_id = p.id
                   AND (t.name LIKE :keyword_name OR t.description LIKE :keyword_description)
            )';
            // 轉義 LIKE 的萬用字元，避免使用者輸入 % 或 _ 影響搜尋結果
            $escaped                            = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $keyword);
            $parameters[':keyword_name']        = '%' . $escaped . '%';
            $parameters[':keyword_description'] = '%' . $escaped . '%';
        }

        $whereSql = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);

        return [$whereSql, $parameters];
    }

    /**
     * 為多筆產品補上多語系資料，結果放在 translations[localeCode]。
     *
     * @param array<int,array<string,mixed>> $products
     * @return array<int,array<string,mixed>>
     */
    private function attachTranslations(array $products): array
    {
        if ($products === []) {
            return [];
        }

        $productIds  = array_column($products, 'id');
        $placeholder = implode(',', array_fill(0, count($productIds), '?'));

        $translationRows = Database::run(
            "SELECT product_id, locale_code, name, description
               FROM product_translations
              WHERE product_id IN ({$placeholder})",
            $productIds
        )->fetchAll();

        $translationsByProduct = [];
        foreach ($translationRows as $translationRow) {
            $translationsByProduct[(int) $translationRow['product_id']][$translationRow['locale_code']] = [
                'name'        => $translationRow['name'],
                'description' => (string) $translationRow['description'],
            ];
        }

        foreach ($products as $index => $product) {
            $productId                       = (int) $product['id'];
            $products[$index]['translations'] = $translationsByProduct[$productId] ?? [];
        }

        return $products;
    }

    /**
     * 以 upsert 寫入各語系的名稱與描述。
     *
     * @param array<string,array{name:string,description:string}> $translations
     */
    private function saveTranslations(int $productId, array $translations): void
    {
        foreach (self::SUPPORTED_LOCALES as $localeCode) {
            $translation = $translations[$localeCode] ?? ['name' => '', 'description' => ''];

            Database::run(
                'INSERT INTO product_translations (product_id, locale_code, name, description)
                      VALUES (:product_id, :locale_code, :name, :description)
                 ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description)',
                [
                    ':product_id'  => $productId,
                    ':locale_code' => $localeCode,
                    ':name'        => $translation['name'],
                    ':description' => $translation['description'],
                ]
            );
        }
    }
}
