<?php
/**
 * 公司資料存取類別
 *
 * 公司的擁有者（owner）與聯絡人（contact）被正規化到 company_contacts 資料表，
 * 以 role_code 區分，因此新增／更新公司時會同時維護這兩筆關聯資料。
 */

declare(strict_types=1);

final class CompanyRepository
{
    /** 擁有者角色代碼 */
    public const ROLE_OWNER = 'owner';

    /** 聯絡人角色代碼 */
    public const ROLE_CONTACT = 'contact';

    /**
     * 取得公司清單。
     *
     * @param string $statusFilter all=全部、active=僅啟用、deactivated=僅停用
     * @return array<int,array<string,mixed>>
     */
    public function findAll(string $statusFilter = 'all'): array
    {
        $condition = '';

        if ($statusFilter === 'active') {
            $condition = 'WHERE c.is_active = 1';
        } elseif ($statusFilter === 'deactivated') {
            $condition = 'WHERE c.is_active = 0';
        }

        $rows = Database::run(
            "SELECT c.* FROM companies AS c {$condition} ORDER BY c.name ASC"
        )->fetchAll();

        return $this->attachContacts($rows);
    }

    /**
     * 依 id 取得單一公司（含 owner / contact）。
     *
     * @return array<string,mixed>|null 找不到時回傳 null
     */
    public function findById(int $companyId): ?array
    {
        $row = Database::run('SELECT * FROM companies WHERE id = ?', [$companyId])->fetch();

        if ($row === false) {
            return null;
        }

        $rows = $this->attachContacts([$row]);

        return $rows[0];
    }

    /**
     * 新增公司，回傳新公司的 id。
     *
     * @param array<string,string> $input 已整理過的表單資料
     */
    public function create(array $input): int
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            Database::run(
                'INSERT INTO companies (name, address, telephone, email, is_active)
                 VALUES (:name, :address, :telephone, :email, 1)',
                [
                    ':name'      => $input['name'],
                    ':address'   => $input['address'],
                    ':telephone' => $input['telephone'],
                    ':email'     => $input['email'],
                ]
            );

            $companyId = (int) $pdo->lastInsertId();
            $this->saveContacts($companyId, $input);

            $pdo->commit();
        } catch (Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }

        return $companyId;
    }

    /**
     * 更新公司基本資料與聯絡資訊。
     *
     * @param array<string,string> $input 已整理過的表單資料
     */
    public function update(int $companyId, array $input): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            Database::run(
                'UPDATE companies
                    SET name = :name, address = :address, telephone = :telephone, email = :email
                  WHERE id = :id',
                [
                    ':name'      => $input['name'],
                    ':address'   => $input['address'],
                    ':telephone' => $input['telephone'],
                    ':email'     => $input['email'],
                    ':id'        => $companyId,
                ]
            );

            $this->saveContacts($companyId, $input);

            $pdo->commit();
        } catch (Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }
    }

    /**
     * 將公司標記為停用，並同時把旗下所有產品標記為隱藏。
     */
    public function deactivate(int $companyId): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            Database::run('UPDATE companies SET is_active = 0 WHERE id = ?', [$companyId]);
            Database::run('UPDATE products SET is_hidden = 1 WHERE company_id = ?', [$companyId]);

            $pdo->commit();
        } catch (Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }
    }

    /**
     * 重新啟用公司。
     *
     * 產品不會自動取消隱藏，必須由管理員個別決定，避免誤把原本就手動隱藏的產品公開。
     */
    public function activate(int $companyId): void
    {
        Database::run('UPDATE companies SET is_active = 1 WHERE id = ?', [$companyId]);
    }

    /**
     * 把公司資料轉成試題指定的 JSON 結構。
     *
     * @param array<string,mixed> $company
     * @return array<string,mixed>
     */
    public function toApiArray(array $company): array
    {
        return [
            'companyName'      => $company['name'],
            'companyAddress'   => $company['address'],
            'companyTelephone' => $company['telephone'],
            'companyEmail'     => $company['email'],
            'owner'            => [
                'name'         => $company['owner_name'],
                'mobileNumber' => $company['owner_mobile'],
                'email'        => $company['owner_email'],
            ],
            'contact'          => [
                'name'         => $company['contact_name'],
                'mobileNumber' => $company['contact_mobile'],
                'email'        => $company['contact_email'],
            ],
        ];
    }

    /**
     * 為多筆公司資料補上 owner / contact 欄位。
     *
     * @param array<int,array<string,mixed>> $companies
     * @return array<int,array<string,mixed>>
     */
    private function attachContacts(array $companies): array
    {
        if ($companies === []) {
            return [];
        }

        $companyIds  = array_column($companies, 'id');
        $placeholder = implode(',', array_fill(0, count($companyIds), '?'));

        $contactRows = Database::run(
            "SELECT company_id, role_code, name, mobile_number, email
               FROM company_contacts
              WHERE company_id IN ({$placeholder})",
            $companyIds
        )->fetchAll();

        // 先把聯絡資料依 公司 id + 角色 整理成查表用的陣列
        $contactsByCompany = [];
        foreach ($contactRows as $contactRow) {
            $contactsByCompany[(int) $contactRow['company_id']][$contactRow['role_code']] = $contactRow;
        }

        foreach ($companies as $index => $company) {
            $companyId = (int) $company['id'];
            $owner     = $contactsByCompany[$companyId][self::ROLE_OWNER] ?? [];
            $contact   = $contactsByCompany[$companyId][self::ROLE_CONTACT] ?? [];

            $companies[$index]['owner_name']     = $owner['name'] ?? '';
            $companies[$index]['owner_mobile']   = $owner['mobile_number'] ?? '';
            $companies[$index]['owner_email']    = $owner['email'] ?? '';
            $companies[$index]['contact_name']   = $contact['name'] ?? '';
            $companies[$index]['contact_mobile'] = $contact['mobile_number'] ?? '';
            $companies[$index]['contact_email']  = $contact['email'] ?? '';
        }

        return $companies;
    }

    /**
     * 以 upsert 的方式寫入 owner 與 contact 兩筆聯絡資料。
     *
     * @param array<string,string> $input
     */
    private function saveContacts(int $companyId, array $input): void
    {
        $contactRows = [
            self::ROLE_OWNER => [
                'name'   => $input['owner_name'],
                'mobile' => $input['owner_mobile'],
                'email'  => $input['owner_email'],
            ],
            self::ROLE_CONTACT => [
                'name'   => $input['contact_name'],
                'mobile' => $input['contact_mobile'],
                'email'  => $input['contact_email'],
            ],
        ];

        foreach ($contactRows as $roleCode => $contact) {
            Database::run(
                'INSERT INTO company_contacts (company_id, role_code, name, mobile_number, email)
                      VALUES (:company_id, :role_code, :name, :mobile, :email)
                 ON DUPLICATE KEY UPDATE name = VALUES(name),
                                         mobile_number = VALUES(mobile_number),
                                         email = VALUES(email)',
                [
                    ':company_id' => $companyId,
                    ':role_code'  => $roleCode,
                    ':name'       => $contact['name'],
                    ':mobile'     => $contact['mobile'],
                    ':email'      => $contact['email'],
                ]
            );
        }
    }
}
