# 模組 B － 系統開發與 PHPUnit 單元測試

針對模組 A（`../TaskA`）完成的 GraphQL 功能撰寫的單元測試。

## 執行方式

```bash
cd C:\nginx\skill\53th\senior\53grandmaster2stage\TaskB
php vendor\phpunit\phpunit\phpunit --testdox
```

或直接執行 `run-tests.bat`。

只跑單一測試項目：

```bash
php vendor\phpunit\phpunit\phpunit --testsuite Login
php vendor\phpunit\phpunit\phpunit --testsuite Logout
php vendor\phpunit\phpunit\phpunit --testsuite Register
php vendor\phpunit\phpunit\phpunit --testsuite GetUser
```

## 目前執行結果

```
PHPUnit 10.1.2 by Sebastian Bergmann and contributors.
Runtime:       PHP 8.3.7
Configuration: TaskB\phpunit.xml

.............................................                     45 / 45 (100%)

OK (45 tests, 117 assertions)
```

## 測試項目對應

| 試題項目 | 測試檔 | 案例數 |
| --- | --- | --- |
| 1. 訪客登入（成功／Email 有誤／密碼有誤） | `tests/LoginTest.php` | 14 |
| 2. 訪客登出（成功／使用者未認證） | `tests/LogoutTest.php` | 10 |
| 3. 訪客註冊（成功／重複的使用者） | `tests/RegisterTest.php` | 9 |
| 4. 取得會員本身資料（成功／使用者未認證） | `tests/GetUserTest.php` | 12 |

除了試題要求的基本情境，另外涵蓋：權杖是否為 `sha256(Email)`、登入後權杖是否寫入資料庫、
登出後舊權杖是否失效、登出是否影響其他使用者、密碼是否以雜湊儲存、註冊失敗是否真的沒有寫入、
GraphQL 是否只回傳被查詢的欄位、密碼欄位是否無法被查詢、SQL Injection 字串的處理、缺少必填參數的錯誤等。

## 資料庫還原

試題說明「在執行測試前可以自動或手動還原資料庫」。
`tests/GraphQLTestCase.php` 的 `setUp()` 會在**每一個**測試案例前呼叫 `Installer::install()`，
重建 `users`、`books`、`rents` 三張表並寫回種子資料，因此每個測試都彼此獨立、可重複執行。

## 測試方式說明

`bootstrap.php` 會載入 `../TaskA/bootstrap.php`，測試直接呼叫 `App::handle()`。
`App::handle()` 是模組 A 真正的處理流程（GraphQL 解析 → 執行 → 認證 → 資料庫），
只是與 HTTP 層解耦，所以不需要啟動網頁伺服器，也能測到完整的商業邏輯。
模組 A 的 HTTP 端點另以 curl 驗證（見 `../TaskA/README.md`）。

## 關於 vendor 目錄（離線環境的處理）

本機無法連外，`composer install` 會失敗於
`Network disabled, request canceled: https://repo.packagist.org/...`。

因此 `vendor/` 內的 **PHPUnit 10.1.2 與其相依套件是離線佈署**的：
從本機既有專案取得套件原始碼後，掃描所有 PHP 檔產生類別對應表，
再以 `vendor/autoload.php`（自寫的 classmap 自動載入器）取代 composer 產生的載入器。

* 使用的仍是**官方原版 PHPUnit 10.1.2**（試題列出的建議版本為 v10.2），
  執行檔為 `vendor/phpunit/phpunit/phpunit`，測試語法、註記與設定檔皆為標準 PHPUnit 用法。
* 相依套件：`phpunit/php-*`、`sebastian/*`、`myclabs/deep-copy`、`phar-io/*`、`theseer/tokenizer`、`nikic/php-parser`。

### 涵蓋率（Coverage）

`phpunit.xml` 已設定涵蓋率統計範圍為 `../TaskA/src`，但**本機 PHP 未安裝 Xdebug 或 PCOV**，
沒有涵蓋率驅動程式，因此 `--coverage-text` 無法輸出數據。
在有安裝 Xdebug／PCOV 的環境中，直接執行下列指令即可產生報表：

```bash
php vendor\phpunit\phpunit\phpunit --coverage-text
php vendor\phpunit\phpunit\phpunit --coverage-html coverage
```

## 檔案結構

```
TaskB/
├── phpunit.xml               PHPUnit 設定（4 個 testsuite、涵蓋率範圍）
├── bootstrap.php             載入 vendor 與模組 A
├── run-tests.bat             一鍵執行
├── README.md
├── tests/
│   ├── GraphQLTestCase.php   測試基底類別（自動還原資料庫、共用輔助方法）
│   ├── LoginTest.php
│   ├── LogoutTest.php
│   ├── RegisterTest.php
│   └── GetUserTest.php
└── vendor/                   離線佈署的 PHPUnit 10.1.2
```
