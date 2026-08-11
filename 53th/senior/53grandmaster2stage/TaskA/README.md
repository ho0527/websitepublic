# 模組 A － GraphQL 圖書管理系統

第 47 屆國際技能競賽第 2 階段國手選拔賽（53 屆全國賽國手選拔二階）模組 A。

## 端點

| 用途 | 網址 |
| --- | --- |
| GraphQL API（POST） | `http://127.0.0.1:83/53th/senior/53grandmaster2stage/TaskA/` |
| 同上（PATH_INFO 寫法） | `http://127.0.0.1:83/53th/senior/53grandmaster2stage/TaskA/index.php/graphql` |
| 內建查詢主控台（GET） | `http://127.0.0.1:83/53th/senior/53grandmaster2stage/TaskA/` |
| 建立／還原資料庫 | `http://127.0.0.1:83/53th/senior/53grandmaster2stage/TaskA/setup.php` |

請求格式支援 `application/json`（`{"query": "...", "variables": {}, "operationName": ""}`）、
`application/graphql`（body 直接放查詢字串），以及一般表單 `query=...`。

認證方式為 HTTP 標頭 `Authorization: Bearer <user_token>`，
`user_token` 依試題規定為 **Email 的 sha256**。

## 快速驗證

```bash
# 1. 建立資料庫
php setup.php

# 2. 登入取得 user_token
curl -s -X POST http://127.0.0.1:83/53th/senior/53grandmaster2stage/TaskA/ \
     -H "Content-Type: application/json" \
     -d '{"query":"mutation userLogin { login(email: \"admin@localhost\", password: \"adminpass\") { user_token } }"}'

# 3. 以權杖取得自身資料
curl -s -X POST http://127.0.0.1:83/53th/senior/53grandmaster2stage/TaskA/ \
     -H "Content-Type: application/json" \
     -H "Authorization: Bearer 7ba684f146c9445720e4b9a8d6ed775de4804bc2dbe01fb6490b8cce05db9f43" \
     -d '{"query":"query getUser { user { id email username role } }"}'
```

## 內建帳號

| # | Email | Password | Username | Role |
| --- | --- | --- | --- | --- |
| 1 | admin@localhost | adminpass | admin | ADMIN |
| 2 | user1@localhost | user1pass | user1 | USER |
| 3 | admin@web01.com | adminpass | admin2 | ADMIN |
| 4 | user1@web01.com | user1pass | user12 | USER |

> 試題中的帳號寫成 `admin@webXX.com`（XX 為崗位／國別代碼），但註冊範例又以 `@localhost` 出現，
> 因此兩種網域各建立一組。要改成其他網域時，修改 `config.php` 的 `account_domains` 後重新執行 `setup.php` 即可。

## 種子資料

| 資料表 | 內容 |
| --- | --- |
| books | id=1 The Pragmatic Programmer（978-0135957059，已被 admin 借出）、id=2 Clean Code（978-0132350884） |
| rents | id=1 → admin 借出 book id=1 |

## API 一覽

### Query

| 欄位 | 權限 | 回傳 | 錯誤 |
| --- | --- | --- | --- |
| `user` | 會員 | `User` | `unauthorized user` |
| `books` | 公開 | `[Book]` | － |
| `rents` | 會員 | `[Rent]` | `unauthorized user` |

### Mutation

| 欄位 | 權限 | 參數 | 回傳 | 錯誤 |
| --- | --- | --- | --- | --- |
| `login` | 訪客 | email, password | `{ user_token }` | `user not found` |
| `logout` | 會員 | － | `{ message: "user logout success" }` | `unauthorized user` |
| `register` | 訪客 | email, password, username | `{ message: "user register success" }` | `user already exists` |
| `insertBook` | 管理者 | name, isbn, author | `{ id }` | `unauthorized user` / `permission denied` / `invalid isbn` / `book already exists` |
| `removeBook` | 管理者 | id | `{ message: "book delete success" }` | `unauthorized user` / `permission denied` / `book not exists` / `book is rental` |
| `insertRent` | 會員 | bookId | `{ id }` | `unauthorized user` / `book not exists` / `book is rental` |
| `removeRent` | 會員 | id | `{ message: "rent delete success" }` | `unauthorized user` / `rent not exists` / `permission denied` |

錯誤一律以 `{"errors":[{"message":"..."}]}` 回應（不含 `data`），成功則為 `{"data":{...}}`。

### 型別

```graphql
type User { id: Int, email: String, username: String, role: String }
type Book { id: Int, name: String, isbn: String, author: String, created_at: Int, reader: User }
type Rent { id: Int, created_at: Int, book: Book, user: User }
```

## ISBN 驗證

`insertBook` 會驗證 ISBN-13：13 個數字（可用「-」分隔），前 12 位依序乘以權重 1、3、1、3…，
校驗碼為加權和除以 10 的負餘數，與第 13 位比對。不合法時回應 `invalid isbn`。

## 為什麼沒有使用 Laravel + rebing/graphql-laravel

試題指定使用 **Laravel 的 rebing/graphql-laravel**。本機為離線環境：

* `npm`／`composer` 都無法連外，`composer install` 會停在
  `Network disabled, request canceled: https://repo.packagist.org/...`（已實際嘗試並確認失敗）。
* composer 本機快取雖有 `rebing/graphql-laravel`、`webonyx/graphql-php` 等 zip 檔，
  但缺少 packagist 的相依性中介資料，無法完成相依性解析與安裝。

因此本模組**自行實作了等效的 GraphQL 伺服器**，位於 `src/GraphQL/`：

| 檔案 | 功能 |
| --- | --- |
| `src/GraphQL/Parser.php` | 詞法＋語法分析：query／mutation、操作名稱、變數宣告與代換、欄位別名、參數（字串／數字／布林／null／列舉／陣列／物件）、巢狀選取集合、`__typename`、`#` 註解、區塊字串 |
| `src/GraphQL/Executor.php` | 依 Schema 逐層解析欄位，只輸出被查詢到的欄位，並處理串列與純量型別轉換 |
| `src/Schema.php` | 型別與 resolver 定義（相當於 rebing/graphql-laravel 的 Type／Query／Mutation 類別） |

行為上與 rebing/graphql-laravel 相同：同一個端點、同樣的 `{"query": ...}` 請求格式、
同樣的 `data` / `errors` 回應結構。未支援的語法（fragment、subscription）會回傳明確的錯誤訊息。

## 檔案結構

```
TaskA/
├── index.php                 HTTP 進入點（POST 走 GraphQL、GET 顯示主控台）
├── console.php               內建查詢主控台（無任何外部 CDN）
├── setup.php                 建立／還原資料庫
├── bootstrap.php             載入設定與類別
├── config.php                資料庫與內建帳號設定
├── README.md
└── src/
    ├── App.php               查詢字串 → 回應陣列（與 HTTP 解耦，供模組 B 測試呼叫）
    ├── Database.php          PDO 封裝，全部使用 prepared statement
    ├── Installer.php         建表與種子資料
    ├── Isbn.php              ISBN-13 驗證
    ├── Schema.php            GraphQL 型別與 resolver
    ├── GraphQL/Parser.php
    ├── GraphQL/Executor.php
    └── Services/             AuthService、BookService、RentService（商業邏輯）
```

## 安全性

* 所有 SQL 皆使用 PDO prepared statement（`PDO::ATTR_EMULATE_PREPARES = false`），可防 SQL Injection。
* 密碼以 `password_hash()` 儲存、`password_verify()` 驗證，資料庫中不存明碼。
* 主控台與 `setup.php` 的輸出全部經過 `htmlspecialchars()` 或 `textContent` 處理，避免 XSS。
* API 回應為 JSON，登入失敗一律回 `user not found`，不透露帳號是否存在。
