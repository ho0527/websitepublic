<?php
/**
 * 後台控制器
 *
 * 負責管理員登入、公司管理與產品管理的所有頁面與表單處理。
 * 除了登入頁之外，每個動作進入時都會先呼叫 Auth::requireAdmin()，
 * 未登入時直接回應 HTTP 401。
 */

declare(strict_types=1);

final class AdminController
{
    private CompanyRepository $companies;

    private ProductRepository $products;

    public function __construct()
    {
        $this->companies = new CompanyRepository();
        $this->products  = new ProductRepository();
    }

    // =================================================================
    // 首頁與登入
    // =================================================================

    /**
     * 模組首頁：已登入導向公司清單，未登入導向登入頁。
     */
    public function home(): void
    {
        redirectTo(Auth::isSignedIn() ? '/companies' : '/login');
    }

    /**
     * 顯示登入頁。
     */
    public function showLoginForm(string $errorMessage = ''): void
    {
        View::renderAdminPage('login', 'Admin Login', [
            'errorMessage' => $errorMessage,
        ]);
    }

    /**
     * 處理登入表單。
     */
    public function signIn(): void
    {
        $passphrase = (string) ($_POST['passphrase'] ?? '');

        if (Auth::attemptSignIn($passphrase)) {
            redirectTo('/companies');
        }

        $this->showLoginForm('The passphrase is incorrect.');
    }

    /**
     * 登出。
     */
    public function signOut(): void
    {
        Auth::signOut();
        redirectTo('/login');
    }

    // =================================================================
    // 公司管理
    // =================================================================

    /**
     * 公司清單（包含已停用的公司，以標籤區分）。
     */
    public function companyList(): void
    {
        Auth::requireAdmin();

        View::renderAdminPage('companies', 'Companies', [
            'companies'          => $this->companies->findAll('all'),
            'showDeactivatedOnly' => false,
        ]);
    }

    /**
     * 已停用公司的獨立清單。
     */
    public function deactivatedCompanyList(): void
    {
        Auth::requireAdmin();

        View::renderAdminPage('companies', 'Deactivated Companies', [
            'companies'           => $this->companies->findAll('deactivated'),
            'showDeactivatedOnly' => true,
        ]);
    }

    /**
     * 單一公司頁面：公司資料 + 旗下產品。
     */
    public function companyDetail(int $companyId): void
    {
        Auth::requireAdmin();

        $company = $this->companies->findById($companyId);

        if ($company === null) {
            respondWithErrorPage(404, 'Not Found', 'Company not found.');
        }

        View::renderAdminPage('companydetail', 'Company - ' . $company['name'], [
            'company'  => $company,
            'products' => $this->products->findAll(['companyId' => $companyId]),
        ]);
    }

    /**
     * 新增公司的表單。
     *
     * @param array<int,string>   $errors    驗證錯誤訊息
     * @param array<string,mixed> $oldInput  重新填回表單的輸入值
     */
    public function companyCreateForm(array $errors = [], array $oldInput = []): void
    {
        Auth::requireAdmin();

        View::renderAdminPage('companyform', 'New Company', [
            'pageHeading' => 'New company',
            'formAction'  => urlFor('/companies'),
            'company'     => $oldInput,
            'errors'      => $errors,
        ]);
    }

    /**
     * 寫入新公司。
     */
    public function companyCreate(): void
    {
        Auth::requireAdmin();

        $input  = $this->collectCompanyInput();
        $errors = $this->validateCompanyInput($input);

        if ($errors !== []) {
            $this->companyCreateForm($errors, $input);

            return;
        }

        $companyId = $this->companies->create($input);
        redirectTo('/companies/' . $companyId);
    }

    /**
     * 編輯公司的表單。
     *
     * @param array<int,string>        $errors
     * @param array<string,mixed>|null $oldInput
     */
    public function companyEditForm(int $companyId, array $errors = [], ?array $oldInput = null): void
    {
        Auth::requireAdmin();

        $company = $this->companies->findById($companyId);

        if ($company === null) {
            respondWithErrorPage(404, 'Not Found', 'Company not found.');
        }

        View::renderAdminPage('companyform', 'Edit Company', [
            'pageHeading' => 'Edit company',
            'formAction'  => urlFor('/companies/' . $companyId),
            'company'     => $oldInput ?? $this->companyRowToFormInput($company),
            'errors'      => $errors,
        ]);
    }

    /**
     * 更新公司資料。
     */
    public function companyUpdate(int $companyId): void
    {
        Auth::requireAdmin();

        if ($this->companies->findById($companyId) === null) {
            respondWithErrorPage(404, 'Not Found', 'Company not found.');
        }

        $input  = $this->collectCompanyInput();
        $errors = $this->validateCompanyInput($input);

        if ($errors !== []) {
            $this->companyEditForm($companyId, $errors, $input);

            return;
        }

        $this->companies->update($companyId, $input);
        redirectTo('/companies/' . $companyId);
    }

    /**
     * 停用公司（同時把旗下產品標記為隱藏）。
     */
    public function companyDeactivate(int $companyId): void
    {
        Auth::requireAdmin();

        if ($this->companies->findById($companyId) === null) {
            respondWithErrorPage(404, 'Not Found', 'Company not found.');
        }

        $this->companies->deactivate($companyId);
        redirectTo('/companies/' . $companyId);
    }

    /**
     * 重新啟用公司。
     */
    public function companyActivate(int $companyId): void
    {
        Auth::requireAdmin();

        if ($this->companies->findById($companyId) === null) {
            respondWithErrorPage(404, 'Not Found', 'Company not found.');
        }

        $this->companies->activate($companyId);
        redirectTo('/companies/' . $companyId);
    }

    // =================================================================
    // 產品管理
    // =================================================================

    /**
     * 所有產品的清單。
     */
    public function productList(): void
    {
        Auth::requireAdmin();

        View::renderAdminPage('products', 'Products', [
            'products'       => $this->products->findAll(),
            'pageHeading'    => 'All products',
            'showHiddenOnly' => false,
        ]);
    }

    /**
     * 已隱藏產品的清單（可從這裡永久刪除）。
     */
    public function hiddenProductList(): void
    {
        Auth::requireAdmin();

        View::renderAdminPage('products', 'Hidden Products', [
            'products'       => $this->products->findAll(['onlyHidden' => true]),
            'pageHeading'    => 'Hidden products',
            'showHiddenOnly' => true,
        ]);
    }

    /**
     * 單一產品的管理頁面（網址為 /products/[GTIN]）。
     */
    public function productDetail(string $gtin): void
    {
        Auth::requireAdmin();

        $product = $this->products->findByGtin($gtin);

        if ($product === null) {
            respondWithErrorPage(404, 'Not Found', 'Product not found.');
        }

        View::renderAdminPage('productdetail', 'Product - ' . $gtin, [
            'product' => $product,
        ]);
    }

    /**
     * 新增產品的表單。
     *
     * @param array<int,string>   $errors
     * @param array<string,mixed> $oldInput
     */
    public function productCreateForm(array $errors = [], array $oldInput = []): void
    {
        Auth::requireAdmin();

        // 允許用 ?company_id= 預選公司，方便從公司頁面直接新增產品
        if ($oldInput === [] && isset($_GET['company_id'])) {
            $oldInput['company_id'] = (int) $_GET['company_id'];
        }

        View::renderAdminPage('productform', 'New Product', [
            'pageHeading'   => 'New product',
            'formAction'    => urlFor('/products'),
            'product'       => $oldInput,
            'companies'     => $this->companies->findAll('all'),
            'errors'        => $errors,
            'currentImage'  => null,
            'isEditing'     => false,
        ]);
    }

    /**
     * 寫入新產品。
     */
    public function productCreate(): void
    {
        Auth::requireAdmin();

        $input  = $this->collectProductInput();
        $errors = $this->validateProductInput($input, null);

        $uploadedFileName = ImageUploader::store($_FILES['image'] ?? null, $errors);

        if ($errors !== []) {
            // 驗證失敗時把剛存下來的檔案刪掉，避免留下孤兒檔案
            ImageUploader::removeStoredFile($uploadedFileName);
            $this->productCreateForm($errors, $input);

            return;
        }

        $input['image_path'] = $uploadedFileName;
        $this->products->create($input);

        redirectTo('/products/' . $input['gtin']);
    }

    /**
     * 編輯產品的表單。
     *
     * @param array<int,string>        $errors
     * @param array<string,mixed>|null $oldInput
     */
    public function productEditForm(string $gtin, array $errors = [], ?array $oldInput = null): void
    {
        Auth::requireAdmin();

        $product = $this->products->findByGtin($gtin);

        if ($product === null) {
            respondWithErrorPage(404, 'Not Found', 'Product not found.');
        }

        View::renderAdminPage('productform', 'Edit Product', [
            'pageHeading'  => 'Edit product',
            'formAction'   => urlFor('/products/' . $gtin),
            'product'      => $oldInput ?? $this->productRowToFormInput($product),
            'companies'    => $this->companies->findAll('all'),
            'errors'       => $errors,
            'currentImage' => $product['image_path'],
            'isEditing'    => true,
        ]);
    }

    /**
     * 更新產品資料（含更換圖片）。
     */
    public function productUpdate(string $gtin): void
    {
        Auth::requireAdmin();

        $product = $this->products->findByGtin($gtin);

        if ($product === null) {
            respondWithErrorPage(404, 'Not Found', 'Product not found.');
        }

        $productId = (int) $product['id'];
        $input     = $this->collectProductInput();
        $errors    = $this->validateProductInput($input, $productId);

        $uploadedFileName = ImageUploader::store($_FILES['image'] ?? null, $errors);

        if ($errors !== []) {
            ImageUploader::removeStoredFile($uploadedFileName);
            $this->productEditForm($gtin, $errors, $input);

            return;
        }

        // 圖片處理：新上傳 > 勾選移除 > 保持原狀
        if ($uploadedFileName !== null) {
            ImageUploader::removeStoredFile($product['image_path']);
            $input['image_path'] = $uploadedFileName;
        } elseif (isset($_POST['remove_image'])) {
            ImageUploader::removeStoredFile($product['image_path']);
            $input['image_path'] = null;
        } else {
            $input['image_path'] = $product['image_path'];
        }

        $this->products->update($productId, $input);

        redirectTo('/products/' . $input['gtin']);
    }

    /**
     * 切換產品的隱藏狀態。
     */
    public function productSetHidden(string $gtin, bool $isHidden): void
    {
        Auth::requireAdmin();

        $product = $this->products->findByGtin($gtin);

        if ($product === null) {
            respondWithErrorPage(404, 'Not Found', 'Product not found.');
        }

        $this->products->setHidden((int) $product['id'], $isHidden);
        redirectTo('/products/' . $gtin);
    }

    /**
     * 永久刪除產品，只有已隱藏的產品才允許刪除。
     */
    public function productDelete(string $gtin): void
    {
        Auth::requireAdmin();

        $product = $this->products->findByGtin($gtin);

        if ($product === null) {
            respondWithErrorPage(404, 'Not Found', 'Product not found.');
        }

        if ((int) $product['is_hidden'] !== 1) {
            respondWithErrorPage(
                403,
                'Forbidden',
                'Only hidden products can be permanently deleted. Please mark it as hidden first.'
            );
        }

        if ($this->products->deleteHiddenProduct((int) $product['id'])) {
            ImageUploader::removeStoredFile($product['image_path']);
        }

        redirectTo('/products/hidden');
    }

    /**
     * 移除產品圖片（改用預設佔位圖）。
     */
    public function productRemoveImage(string $gtin): void
    {
        Auth::requireAdmin();

        $product = $this->products->findByGtin($gtin);

        if ($product === null) {
            respondWithErrorPage(404, 'Not Found', 'Product not found.');
        }

        ImageUploader::removeStoredFile($product['image_path']);
        $this->products->updateImagePath((int) $product['id'], null);

        redirectTo('/products/' . $gtin);
    }

    // =================================================================
    // 表單資料整理與驗證
    // =================================================================

    /**
     * 從 $_POST 取出公司欄位並去除前後空白。
     *
     * @return array<string,string>
     */
    private function collectCompanyInput(): array
    {
        $fieldNames = [
            'name', 'address', 'telephone', 'email',
            'owner_name', 'owner_mobile', 'owner_email',
            'contact_name', 'contact_mobile', 'contact_email',
        ];

        $input = [];
        foreach ($fieldNames as $fieldName) {
            $input[$fieldName] = trim((string) ($_POST[$fieldName] ?? ''));
        }

        return $input;
    }

    /**
     * 驗證公司欄位。
     *
     * @param array<string,string> $input
     * @return array<int,string>
     */
    private function validateCompanyInput(array $input): array
    {
        $errors = [];

        if ($input['name'] === '') {
            $errors[] = 'Company name is required.';
        }

        foreach (['email' => 'Company email', 'owner_email' => 'Owner email', 'contact_email' => 'Contact email'] as $field => $label) {
            if ($input[$field] !== '' && filter_var($input[$field], FILTER_VALIDATE_EMAIL) === false) {
                $errors[] = $label . ' is not a valid email address.';
            }
        }

        return $errors;
    }

    /**
     * 把資料庫中的公司資料列轉成表單用的欄位。
     *
     * @param array<string,mixed> $company
     * @return array<string,string>
     */
    private function companyRowToFormInput(array $company): array
    {
        return [
            'name'           => (string) $company['name'],
            'address'        => (string) $company['address'],
            'telephone'      => (string) $company['telephone'],
            'email'          => (string) $company['email'],
            'owner_name'     => (string) $company['owner_name'],
            'owner_mobile'   => (string) $company['owner_mobile'],
            'owner_email'    => (string) $company['owner_email'],
            'contact_name'   => (string) $company['contact_name'],
            'contact_mobile' => (string) $company['contact_mobile'],
            'contact_email'  => (string) $company['contact_email'],
        ];
    }

    /**
     * 從 $_POST 取出產品欄位。
     *
     * @return array<string,mixed>
     */
    private function collectProductInput(): array
    {
        $translations = [];
        foreach (ProductRepository::SUPPORTED_LOCALES as $localeCode) {
            $translations[$localeCode] = [
                'name'        => trim((string) ($_POST['name_' . $localeCode] ?? '')),
                'description' => trim((string) ($_POST['description_' . $localeCode] ?? '')),
            ];
        }

        return [
            'company_id'        => (int) ($_POST['company_id'] ?? 0),
            'gtin'              => trim((string) ($_POST['gtin'] ?? '')),
            'brand'             => trim((string) ($_POST['brand'] ?? '')),
            'country_of_origin' => trim((string) ($_POST['country_of_origin'] ?? '')),
            'gross_weight'      => $this->normalizeDecimal($_POST['gross_weight'] ?? ''),
            'net_weight'        => $this->normalizeDecimal($_POST['net_weight'] ?? ''),
            'weight_unit'       => trim((string) ($_POST['weight_unit'] ?? '')),
            'is_hidden'         => isset($_POST['is_hidden']) ? 1 : 0,
            'image_path'        => null,
            'translations'      => $translations,
        ];
    }

    /**
     * 空字串轉成 null，其餘轉成 float，方便寫入 DECIMAL 欄位。
     *
     * @param mixed $value
     */
    private function normalizeDecimal($value): ?float
    {
        $value = trim((string) $value);

        return $value === '' ? null : (float) $value;
    }

    /**
     * 驗證產品欄位（GTIN 格式檢查在此以伺服器端完成）。
     *
     * @param array<string,mixed> $input
     * @param int|null            $excludeProductId 編輯時排除自己
     * @return array<int,string>
     */
    private function validateProductInput(array $input, ?int $excludeProductId): array
    {
        $errors = [];

        if (!isValidGtinFormat((string) $input['gtin'])) {
            $errors[] = 'GTIN must be a number with 13 or 14 digits.';
        } elseif ($this->products->isGtinTaken((string) $input['gtin'], $excludeProductId)) {
            $errors[] = 'This GTIN is already used by another product.';
        }

        if ($input['company_id'] <= 0 || $this->companies->findById((int) $input['company_id']) === null) {
            $errors[] = 'Please choose an existing company.';
        }

        if ($input['translations']['en']['name'] === '') {
            $errors[] = 'Product name (English) is required.';
        }

        if ($input['translations']['fr']['name'] === '') {
            $errors[] = 'Product name (French) is required.';
        }

        if ($input['weight_unit'] === '') {
            $errors[] = 'Weight unit is required.';
        }

        foreach (['gross_weight' => 'Gross weight', 'net_weight' => 'Net content weight'] as $field => $label) {
            if ($input[$field] !== null && $input[$field] < 0) {
                $errors[] = $label . ' cannot be negative.';
            }
        }

        return $errors;
    }

    /**
     * 把資料庫中的產品資料列轉成表單用的欄位。
     *
     * @param array<string,mixed> $product
     * @return array<string,mixed>
     */
    private function productRowToFormInput(array $product): array
    {
        $translations = [];
        foreach (ProductRepository::SUPPORTED_LOCALES as $localeCode) {
            $translations[$localeCode] = [
                'name'        => (string) ($product['translations'][$localeCode]['name'] ?? ''),
                'description' => (string) ($product['translations'][$localeCode]['description'] ?? ''),
            ];
        }

        return [
            'company_id'        => (int) $product['company_id'],
            'gtin'              => (string) $product['gtin'],
            'brand'             => (string) $product['brand'],
            'country_of_origin' => (string) $product['country_of_origin'],
            'gross_weight'      => $product['gross_weight'],
            'net_weight'        => $product['net_weight'],
            'weight_unit'       => (string) $product['weight_unit'],
            'is_hidden'         => (int) $product['is_hidden'],
            'translations'      => $translations,
        ];
    }
}
