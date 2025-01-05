# API架構表

## 目錄
- [API架構表](#api架構表)
	- [目錄](#目錄)
	- [管理員api](#管理員api)
		- [登入管理者](#登入管理者)
			- [request](#request)
			- [request body](#request-body)
			- [success response](#success-response)
			- [error response](#error-response)
		- [註冊管理者](#註冊管理者)
			- [request](#request-1)
			- [request body](#request-body-1)
			- [success response](#success-response-1)
			- [error response](#error-response-1)
		- [登出管理者](#登出管理者)
			- [request](#request-2)
			- [request body](#request-body-2)
			- [success response](#success-response-2)
			- [error response](#error-response-2)
		- [更新使用者](#更新使用者)
			- [request](#request-3)
			- [request body](#request-body-3)
			- [success response](#success-response-3)
			- [error response](#error-response-3)
		- [更新使用者密碼](#更新使用者密碼)
			- [request](#request-4)
			- [request body](#request-body-4)
			- [success response](#success-response-4)
			- [error response](#error-response-4)
		- [修改員工時薪](#修改員工時薪)
			- [request](#request-5)
			- [request body](#request-body-5)
			- [success response](#success-response-5)
			- [error response](#error-response-5)
		- [修改員工組別](#修改員工組別)
			- [request](#request-6)
			- [request body](#request-body-6)
			- [success response](#success-response-6)
			- [error response](#error-response-6)
	- [案件資料API](#案件資料api)
		- [新增案件](#新增案件)
			- [request](#request-7)
			- [request body](#request-body-7)
			- [success response](#success-response-7)
			- [error response](#error-response-7)
		- [修改案件](#修改案件)
			- [request](#request-8)
			- [request body](#request-body-8)
			- [success response](#success-response-8)
			- [error response](#error-response-8)
		- [刪除案件](#刪除案件)
			- [request](#request-9)
			- [request body](#request-body-9)
			- [success response](#success-response-9)
			- [error response](#error-response-9)
		- [查詢特定案件](#查詢特定案件)
			- [request](#request-10)
			- [request body](#request-body-10)
			- [success response](#success-response-10)
			- [error response](#error-response-10)
		- [查詢所有案件](#查詢所有案件)
			- [request](#request-11)
			- [request body](#request-body-11)
			- [success response](#success-response-11)
			- [error response](#error-response-11)
	- [註解及參見](#註解及參見)
		- [註解](#註解)
		- [參見](#參見)

<div style="page-break-after: always;"></div>

## 管理員api
### 登入管理者
#### request

**/admin/signin POST header: N/A**

#### request body
```json
{
	"username": string,
	"password": string
}
```

#### success response
200
```json
{
	"success": true,
	"data": {
		"userid": userid,
		"permissionid": permissionid,
		"token": token
	}
}
```

#### error response

400 / 必填資料不存在
```json
{
	"success": false,
	"data": "[ERROR]request data not found"
}
```

403 / 登入失敗-帳號錯誤

```json
{
	"success": false,
	"data": "[ERROR]username error"
}
```

403 / 登入失敗-密碼錯誤

```json
{
	"success": false,
	"data": "[ERROR]password error"
}
```

### 註冊管理者
#### request

**/admin/signup POST header: N/A**

#### request body
```json
{
	"permissionid": string,
	"email": string,
	"username": string,
	"name": string,
	"password": string
}
```

#### success response
200
```json
{
	"success": true,
	"data": user_temptoken
}
```

#### error response

400 / 必填資料不存在
```json
{
	"success": false,
	"data": "[ERROR]request data not found"
}
```

409 / 帳號重複

```json
{
	"success": false,
	"data": "[ERROR]username exist"
}
```

### 登出管理者
#### request

**/admin/signout POST header: Authorization Bearer \<token\>**

#### request body
N/A

#### success response
200
```json
{
	"success": true,
	"data": ""
}
```

#### error response
403 / token不存在

```json
{
	"success": false,
	"data": "[ERROR]token not found"
}
```

403 / token錯誤

```json
{
	"success": false,
	"data": "[ERROR]token error"
}
```

### 更新使用者

#### request

**/edituser/{useruuid} PUT header: N/A**

#### request body
```json
{
	?"username": string,
	?"nickname": string,
	?"permission": string('Admin','Supervisor','Employee')
}
```

#### success response
200
```json
{
	"success": true,
	"data": ""
}
```

#### error response

400 / 資料格式錯誤

```json
{
	"success": false,
	"data": "[ERROR]request data data-type error"
}
```

403 / token不存在

```json
{
	"success": false,
	"data": "[ERROR]token not exist"
}
```

403 / token錯誤

```json
{
	"success": false,
	"data": "[ERROR]token error"
}
```

403 / 權限不足

```json
{
	"success": false,
	"data": "[ERROR]no permission"
}
```

404 / 使用者不存在

```json
{
	"success": false,
	"data": "[ERROR]user not exist"
}
```

### 更新使用者密碼

#### request

**/edituserpassword/{useruuid} PUT header: N/A**

#### request body
```json
{
	"oldPassword": string,
	"password": string
}
```

#### success response
200
```json
{
	"success": true,
	"data": ""
}
```

#### error response

400 / 資料格式錯誤

```json
{
	"success": false,
	"data": "[ERROR]request data data-type error"
}
```

400 / 必填資料不存在

```json
{
	"success": false,
	"data": "[ERROR]request data not found"
}
```

403 / token不存在

```json
{
	"success": false,
	"data": "[ERROR]token not exist"
}
```

403 / token錯誤

```json
{
	"success": false,
	"data": "[ERROR]token error"
}
```

403 / 權限不足

```json
{
	"success": false,
	"data": "[ERROR]no permission"
}
```

403 / 使用者密碼不正確

```json
{
	"success": false,
	"data": "[ERROR]password error"
}
```

404 / 使用者不存在

```json
{
	"success": false,
	"data": "[ERROR]user not exist"
}
```

### 修改員工時薪

#### request

**/editemployeehourlyrate/{useruuid} POST header: Authorization Bearer \<token\>**

#### request body
```json
{
	"value": double
}
```

#### success response
200
```json
{
	"success": true,
	"data": ""
}
```

#### error response

400 / 資料格式錯誤

```json
{
	"success": false,
	"data": "[ERROR]request data data-type error"
}
```

400 / 必填資料不存在

```json
{
	"success": false,
	"data": "[ERROR]request data not found"
}
```

400 / userid不是員工

```json
{
	"success": false,
	"data": "[ERROR]user is not employee"
}
```

403 / token不存在

```json
{
	"success": false,
	"data": "[ERROR]token not exist"
}
```

403 / token錯誤

```json
{
	"success": false,
	"data": "[ERROR]token error"
}
```

403 / 權限不足

```json
{
	"success": false,
	"data": "[ERROR]no permission"
}
```

404 / 使用者不存在

```json
{
	"success": false,
	"data": "[ERROR]user not exist"
}
```

### 修改員工組別

#### request

**/editemployeegroupid/{useruuid} POST header: Authorization Bearer \<token\>**

#### request body
```json
{
	"value": double
}
```

#### success response
200
```json
{
	"success": true,
	"data": ""
}
```

#### error response

400 / 資料格式錯誤

```json
{
	"success": false,
	"data": "[ERROR]request data data-type error"
}
```

400 / 必填資料不存在

```json
{
	"success": false,
	"data": "[ERROR]request data not found"
}
```

400 / userid不是員工

```json
{
	"success": false,
	"data": "[ERROR]user is not employee"
}
```

403 / token不存在

```json
{
	"success": false,
	"data": "[ERROR]token not exist"
}
```

403 / token錯誤

```json
{
	"success": false,
	"data": "[ERROR]token error"
}
```

403 / 權限不足

```json
{
	"success": false,
	"data": "[ERROR]no permission"
}
```

404 / 使用者不存在

```json
{
	"success": false,
	"data": "[ERROR]user not exist"
}
```

## 案件資料API

### 新增案件

#### request

**/newproject POST header: Authorization Bearer \<token\>**

#### request body
```json
{
	"clientNumber": string,
	"matterName": string,
	"hourlyRate": double,
	"prepayments": double,
	?"info": object({
		"userId": string(user_uuid),
		"description": string,
		"startTime": date(Y-m-d H:i:s),
		"endTime": date(Y-m-d H:i:s),
	})
}
```

#### success response
200
```json
{
	"success": true,
	"data": {
		"uuid": project_uuid,
		"createUserId": token user id
	}
}
```

#### error response
400 / 資料格式錯誤

```json
{
	"success": false,
	"data": "[ERROR]request data data-type error"
}
```

400 / 必填資料不存在

```json
{
	"success": false,
	"data": "[ERROR]request data not found"
}
```

400 / 日期或時間格式錯誤

```json
{
	"success": false,
	"data": "[ERROR]date or time format error"
}
```

403 / token不存在

```json
{
	"success": false,
	"data": "[ERROR]token not exist"
}
```

403 / token錯誤

```json
{
	"success": false,
	"data": "[ERROR]token error"
}
```

### 修改案件

#### request

**/editproject/{project_uuid} PUT header: Authorization Bearer \<token\>**

#### request body
```json
{
	?"clientNumber": string,
	?"matterName": string,
	?"hourlyRate": double,
	?"prepayments": double,
	?"finishOrNot": boolean,
	?"info": object({
		"userId": string(user_uuid),
		"description": string,
		"startTime": date(Y-m-d H:i:s),
		"endTime": date(Y-m-d H:i:s),
	})
}
```

*如果要更新時間需將date及time皆上傳不然不會更新*

#### success response
200
```json
{
	"success": true,
	"data": ""
}
```

#### error response
400 / 資料格式錯誤

```json
{
	"success": false,
	"data": "[ERROR]request data data-type error"
}
```

400 / 日期或時間格式錯誤

```json
{
	"success": false,
	"data": "[ERROR]date or time format error"
}
```

403 / token不存在

```json
{
	"success": false,
	"data": "[ERROR]token not exist"
}
```

403 / token錯誤

```json
{
	"success": false,
	"data": "[ERROR]token error"
}
```

404 / 專案不存在

```json
{
	"success": false,
	"data": "[ERROR]project not exist"
}
```

404 / 使用者不存在

```json
{
	"success": false,
	"data": "[ERROR]user not exist"
}
```

*註: 目前的寫法任何人都可以改其他人的專案 之後看要不要只能管理員和本人才能改*

### 刪除案件

#### request

**/deleteproject/{project_uuid} DELETE header: Authorization Bearer \<token\>**

#### request body
N/A

*此刪除方式為軟刪除，也就是說你可以在資料庫回復(將deletetime欄位改為NULL)*

#### success response
200
```json
{
	"success": true,
	"data": ""
}
```

#### error response
403 / token不存在

```json
{
	"success": false,
	"data": "[ERROR]token not exist"
}
```

403 / token錯誤

```json
{
	"success": false,
	"data": "[ERROR]token error"
}
```

404 / 專案不存在

```json
{
	"success": false,
	"data": "[ERROR]project not exist"
}
```

### 查詢特定案件

#### request

**/getproject/{project_uuid} GET header: Authorization Bearer \<token\>**

#### request body
N/A

#### success response
200
```json
{
	"success": true,
	"data": {
		"id": project_uuid(string),
		"createUserId": create userid(int),
		"clientNumber": project_number(string),
		"matterName": project_matterName(string),
		"hourlyRate": project_hourlyRate(double),
		"prepayments": project_prepayment(double),
		"finishOrNot": project_finishOrNot(boolean),
		"projectInfo": object({
			"userId": string,
			"userNickname": string,
			"description": string,
			"startTime": date(Y-m-d H:i:s),
			"endTime": date(Y-m-d H:i:s)
		})
	}
}
```

#### error response
403 / token不存在

```json
{
	"success": false,
	"data": "[ERROR]token not exist"
}
```

403 / token錯誤

```json
{
	"success": false,
	"data": "[ERROR]token error"
}
```

404 / 專案不存在

```json
{
	"success": false,
	"data": "[ERROR]project not exist"
}
```

### 查詢所有案件

#### request

**/getprojectlist GET header: Authorization Bearer \<token\>**

#### request body
N/A

#### success response
200
```json
{
	"success": true,
	"data": [
		{
			"id": project_uuid(string),
			"createUserId": create userid(int),
			"clientNumber": project_number(string),
			"matterName": project_matterName(string),
			"hourlyRate": project_hourlyRate(double),
			"prepayments": project_prepayment(double),
			"finishOrNot": project_finishOrNot(boolean),
			"projectInfo": object({
				"userId": string,
				"userName": string,
				"description": string,
				"startTime": date(Y-m-d H:i:s),
				"endTime": date(Y-m-d H:i:s)
			})
		},
		// ...inf
	]
}
```

*已最新發布在上方排序*

#### error response
403 / token不存在

```json
{
	"success": false,
	"data": "[ERROR]token not exist"
}
```

403 / token錯誤

```json
{
	"success": false,
	"data": "[ERROR]token error"
}
```

<div style="page-break-after: always;"></div>

## 註解及參見
### 註解
1. request為請求之url method header等
   1. API request的排列方式為 APIurl method header
2. request body為請求正文(正常是json傳值)
3. response為回傳正文
   1. response 之排列方式為 回應碼 / 意義(成功將略過意義)
   2. "..."表示延伸
      1. inf表無限
4. date的表示法
   1. Y: 4位數的年份
   2. m: 2位數的月份
   3. d: 2位數的日期
   4. H: 2位數的小時(24小時制)
   5. i: 2位數的分鐘
   6. s: 2位數的秒鐘
### 參見

*b000000000 20240622*