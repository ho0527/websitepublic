#!/usr/bin/env bash
# ==========================================================================
# 第51屆全國技能競賽 網頁技術 模組D - 房屋交易平台
# API 自動測試腳本（以 curl 實際發送請求，檢查 HTTP 狀態碼與 message）
#
# 使用方式：
#   bash "!TEST/api-test.sh"
#
# 注意：腳本會重新匯入 !SQL/schema.sql，測試過程中的資料異動不會保留
# ==========================================================================

BASE_URL="${BASE_URL:-http://127.0.0.1:83/51th/senior/51national/moduled/api/index.php}"
MYSQL_BIN="${MYSQL_BIN:-C:/xampp/mysql/bin/mysql}"
# 取得模組根目錄
# Windows 版的 curl 無法辨識 MSYS 形式的 /c/... 路徑，
# 因此優先使用 pwd -W 取得 C:/... 形式的路徑
MODULE_DIR="$(cd "$(dirname "$0")/.." && { pwd -W 2>/dev/null || pwd; })"

PASS_COUNT=0
FAIL_COUNT=0

# 還原測試資料
"$MYSQL_BIN" -uroot --default-character-set=utf8mb4 < "$MODULE_DIR/!SQL/schema.sql"

# 送出請求並檢查狀態碼與 message
# 用法：check "說明" 期望狀態碼 期望message curl參數...
check() {
	local description="$1"; shift
	local expected_status="$1"; shift
	local expected_message="$1"; shift

	local response
	response=$(curl -s -w $'\n%{http_code}' "$@")

	local status="${response##*$'\n'}"
	local body="${response%$'\n'*}"
	local message
	message=$(printf '%s' "$body" | sed -n 's/.*"message"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p')

	if [ "$status" = "$expected_status" ] && [ "$message" = "$expected_message" ]; then
		PASS_COUNT=$((PASS_COUNT + 1))
		printf 'PASS  %-52s %s %s\n' "$description" "$status" "${message:-<empty>}"
	else
		FAIL_COUNT=$((FAIL_COUNT + 1))
		printf 'FAIL  %-52s 期望 %s/%s 實際 %s/%s\n' "$description" "$expected_status" "${expected_message:-<empty>}" "$status" "${message:-<empty>}"
	fi
}

# 取出回應中的 token
login_token() {
	curl -s -X POST "$BASE_URL/user/login" -H 'Content-Type: application/json' \
		-d "{\"email\":\"$1\",\"password\":\"$2\"}" |
		sed -n 's/.*"token"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p'
}

ADMIN_TOKEN=$(login_token admin@localhost adminpass)
USER1_TOKEN=$(login_token user1@localhost user1pass)
USER2_TOKEN=$(login_token user2@localhost user2pass)

echo "=== API 1 會員登入 ==="
check "正確帳密"            200 ""                    -X POST "$BASE_URL/user/login" -H 'Content-Type: application/json' -d '{"email":"admin@localhost","password":"adminpass"}'
check "密碼錯誤"            403 MSG_INVALID_LOGIN     -X POST "$BASE_URL/user/login" -H 'Content-Type: application/json' -d '{"email":"admin@localhost","password":"wrong"}'
check "使用者不存在"        403 MSG_INVALID_LOGIN     -X POST "$BASE_URL/user/login" -H 'Content-Type: application/json' -d '{"email":"ghost@localhost","password":"x"}'
check "缺少必要欄位"        400 MSG_MISSING_FIELD     -X POST "$BASE_URL/user/login" -H 'Content-Type: application/json' -d '{"email":"admin@localhost"}'
check "資料格式錯誤"        400 MSG_WRONG_DATA_TYPE   -X POST "$BASE_URL/user/login" -H 'Content-Type: application/json' -d '{"email":"notmail","password":"x"}'

echo "=== API 3 會員註冊 ==="
check "註冊成功"            200 ""                    -X POST "$BASE_URL/user/register" -H 'Content-Type: application/json' -d '{"email":"eli@localhost","password":"elipass","nickname":"eli"}'
check "使用者已存在"        409 MSG_USER_EXISTS       -X POST "$BASE_URL/user/register" -H 'Content-Type: application/json' -d '{"email":"eli@localhost","password":"elipass","nickname":"eli"}'
check "缺少必要欄位"        400 MSG_MISSING_FIELD     -X POST "$BASE_URL/user/register" -H 'Content-Type: application/json' -d '{"email":"new@localhost"}'
check "資料格式錯誤"        400 MSG_WRONG_DATA_TYPE   -X POST "$BASE_URL/user/register" -H 'Content-Type: application/json' -d '{"email":"bad","password":"p","nickname":"n"}'

echo "=== API 4 取得房屋列表 ==="
check "預設列表"            200 ""                    "$BASE_URL/house"
check "搜尋與排序條件"      200 ""                    "$BASE_URL/house?title=apartment&min_price=1000000&max_price=20000000&room=3&min_age=0&max_age=20&sort_by=price&order=asc&page=1"
check "第二頁"              200 ""                    "$BASE_URL/house?page=2"

echo "=== API 5 查看房屋 ==="
check "存在的房屋"          200 ""                    "$BASE_URL/house/1"
check "不存在的房屋"        404 MSG_HOUSE_NOT_EXISTS  "$BASE_URL/house/9999"

echo "=== API 6 自己的刊登列表 ==="
check "會員取得自己的房屋"  200 ""                    "$BASE_URL/user/house" -H "X-User-Token: $USER1_TOKEN"
check "無效的 Token"        401 MSG_INVALID_TOKEN     "$BASE_URL/user/house" -H 'X-User-Token: invalid'

echo "=== API 7 刊登房屋 ==="
IMAGE_A="$MODULE_DIR/uploads/sample-1-1.png"
IMAGE_B="$MODULE_DIR/uploads/sample-1-2.png"
NOT_IMAGE="$MODULE_DIR/!TEST/not-an-image.txt"
printf 'this is not an image' > "$NOT_IMAGE"

NEW_HOUSE_ID=$(curl -s -X POST "$BASE_URL/house" -H "X-User-Token: $USER1_TOKEN" \
	-F 'title=Vanung apartment' -F 'description=The apartment near Vanung University' \
	-F "images[]=@$IMAGE_A" -F "images[]=@$IMAGE_B" -F 'cover_index=1' \
	-F 'price=6000000' -F 'square=15' -F 'room=1' -F 'floor=3' -F 'total_floor=3' -F 'age=40' -F 'address=No.16 Zhongyuan Rd' |
	sed -n 's/.*"id"[[:space:]]*:[[:space:]]*\([0-9]*\).*/\1/p')
if [ -n "$NEW_HOUSE_ID" ]; then
	PASS_COUNT=$((PASS_COUNT + 1)); printf 'PASS  %-52s 房屋編號 %s\n' "刊登房屋成功" "$NEW_HOUSE_ID"
else
	FAIL_COUNT=$((FAIL_COUNT + 1)); printf 'FAIL  %-52s 未取得房屋編號\n' "刊登房屋成功"
fi

check "無效的 Token"        401 MSG_INVALID_TOKEN            -X POST "$BASE_URL/house" -F 'title=x'
check "缺少必要欄位"        400 MSG_MISSING_FIELD            -X POST "$BASE_URL/house" -H "X-User-Token: $USER1_TOKEN" -F 'title=x'
check "資料格式錯誤"        400 MSG_WRONG_DATA_TYPE          -X POST "$BASE_URL/house" -H "X-User-Token: $USER1_TOKEN" -F 'title=x' -F 'description=d' -F 'price=abc' -F 'square=1' -F 'room=1' -F 'floor=1' -F 'total_floor=1' -F 'age=1' -F 'address=a'
check "圖片格式錯誤"        400 MSG_IMAGE_CAN_NOT_PROCESS    -X POST "$BASE_URL/house" -H "X-User-Token: $USER1_TOKEN" -F 'title=x' -F 'description=d' -F 'price=1' -F 'square=1' -F 'room=1' -F 'floor=1' -F 'total_floor=1' -F 'age=1' -F 'address=a' -F "images[]=@$NOT_IMAGE"
check "封面索引錯誤"        400 MSG_INVALID_COVER_INDEX      -X POST "$BASE_URL/house" -H "X-User-Token: $USER1_TOKEN" -F 'title=x' -F 'description=d' -F 'price=1' -F 'square=1' -F 'room=1' -F 'floor=1' -F 'total_floor=1' -F 'age=1' -F 'address=a' -F "images[]=@$IMAGE_A" -F 'cover_index=9'

echo "=== API 8 編輯房屋 ==="
check "編輯自己的房屋"      200 ""                     -X PUT "$BASE_URL/house/$NEW_HOUSE_ID" -H "X-User-Token: $USER1_TOKEN" -F 'title=Vanung apartment v2' -F 'description=updated' -F 'price=7000000' -F 'square=16' -F 'room=2' -F 'floor=4' -F 'total_floor=5' -F 'age=41' -F 'address=New address'
check "無效的 Token"        401 MSG_INVALID_TOKEN      -X PUT "$BASE_URL/house/$NEW_HOUSE_ID" -F 'title=x'
check "權限不足"            403 MSG_PERMISSION_DENY    -X PUT "$BASE_URL/house/$NEW_HOUSE_ID" -H "X-User-Token: $USER2_TOKEN" -F 'title=x' -F 'description=d' -F 'price=1' -F 'square=1' -F 'room=1' -F 'floor=1' -F 'total_floor=1' -F 'age=1' -F 'address=a'
check "缺少必要欄位"        400 MSG_MISSING_FIELD      -X PUT "$BASE_URL/house/$NEW_HOUSE_ID" -H "X-User-Token: $USER1_TOKEN" -F 'title=x'
check "不存在的房屋"        404 MSG_HOUSE_NOT_EXISTS   -X PUT "$BASE_URL/house/9999" -H "X-User-Token: $USER1_TOKEN" -F 'title=x' -F 'description=d' -F 'price=1' -F 'square=1' -F 'room=1' -F 'floor=1' -F 'total_floor=1' -F 'age=1' -F 'address=a'

echo "=== API 10 申請精選房屋 ==="
check "申請成功"            200 ""                     -X POST "$BASE_URL/application" -H "X-User-Token: $USER1_TOKEN" -H 'Content-Type: application/json' -d "{\"house_id\":$NEW_HOUSE_ID}"
check "精選房屋申請中"      409 MSG_HOUSE_APPLIED      -X POST "$BASE_URL/application" -H "X-User-Token: $USER1_TOKEN" -H 'Content-Type: application/json' -d "{\"house_id\":$NEW_HOUSE_ID}"
check "房屋已是精選房屋"    409 MSG_HOUSE_ADVERTISED   -X POST "$BASE_URL/application" -H "X-User-Token: $USER1_TOKEN" -H 'Content-Type: application/json' -d '{"house_id":2}'
check "權限不足（非自己）"  403 MSG_PERMISSION_DENY    -X POST "$BASE_URL/application" -H "X-User-Token: $USER2_TOKEN" -H 'Content-Type: application/json' -d "{\"house_id\":$NEW_HOUSE_ID}"
check "不存在的房屋"        404 MSG_HOUSE_NOT_EXISTS   -X POST "$BASE_URL/application" -H "X-User-Token: $USER1_TOKEN" -H 'Content-Type: application/json' -d '{"house_id":9999}'
check "缺少必要欄位"        400 MSG_MISSING_FIELD      -X POST "$BASE_URL/application" -H "X-User-Token: $USER1_TOKEN" -H 'Content-Type: application/json' -d '{}'
check "無效的 Token"        401 MSG_INVALID_TOKEN      -X POST "$BASE_URL/application" -H 'Content-Type: application/json' -d '{"house_id":1}'

echo "=== API 12 取得申請列表 ==="
check "管理員取得列表"      200 ""                     "$BASE_URL/application" -H "X-User-Token: $ADMIN_TOKEN"
check "含搜尋條件"          200 ""                     "$BASE_URL/application?title=apartment&status=applied&order=asc&page=1" -H "X-User-Token: $ADMIN_TOKEN"
check "權限不足"            403 MSG_PERMISSION_DENY    "$BASE_URL/application" -H "X-User-Token: $USER1_TOKEN"
check "無效的 Token"        401 MSG_INVALID_TOKEN      "$BASE_URL/application" -H 'X-User-Token: invalid'

echo "=== API 11 取消申請 ==="
# 房屋 3 的申請編號為 3（審核中），由 user2 刊登
check "權限不足"            403 MSG_PERMISSION_DENY          -X DELETE "$BASE_URL/application/3" -H "X-User-Token: $USER1_TOKEN"
check "申請已審核"          409 MSG_ALREADY_ADVERTISED       -X DELETE "$BASE_URL/application/1" -H "X-User-Token: $USER1_TOKEN"
check "取消成功"            200 ""                            -X DELETE "$BASE_URL/application/3" -H "X-User-Token: $USER2_TOKEN"
check "不存在的申請"        404 MSG_APPLICATION_NOT_EXISTS    -X DELETE "$BASE_URL/application/3" -H "X-User-Token: $USER2_TOKEN"
check "無效的 Token"        401 MSG_INVALID_TOKEN             -X DELETE "$BASE_URL/application/4"

echo "=== API 13 審核申請 ==="
check "同意申請"            200 ""                            -X PUT "$BASE_URL/application/4" -H "X-User-Token: $ADMIN_TOKEN" -H 'Content-Type: application/json' -d '{"approve":true}'
check "申請已審核"          409 MSG_ALREADY_ADVERTISED        -X PUT "$BASE_URL/application/4" -H "X-User-Token: $ADMIN_TOKEN" -H 'Content-Type: application/json' -d '{"approve":true}'
check "缺少必要欄位"        400 MSG_MISSING_FIELD             -X PUT "$BASE_URL/application/5" -H "X-User-Token: $ADMIN_TOKEN" -H 'Content-Type: application/json' -d '{}'
check "資料格式錯誤"        400 MSG_WRONG_DATA_TYPE           -X PUT "$BASE_URL/application/5" -H "X-User-Token: $ADMIN_TOKEN" -H 'Content-Type: application/json' -d '{"approve":"maybe"}'
check "權限不足"            403 MSG_PERMISSION_DENY           -X PUT "$BASE_URL/application/5" -H "X-User-Token: $USER1_TOKEN" -H 'Content-Type: application/json' -d '{"approve":true}'
check "不存在的申請"        404 MSG_APPLICATION_NOT_EXISTS    -X PUT "$BASE_URL/application/999" -H "X-User-Token: $ADMIN_TOKEN" -H 'Content-Type: application/json' -d '{"approve":true}'
check "無效的 Token"        401 MSG_INVALID_TOKEN             -X PUT "$BASE_URL/application/5" -H 'Content-Type: application/json' -d '{"approve":true}'

echo "=== API 14 取得精選房屋列表 ==="
check "管理員取得列表"      200 ""                     "$BASE_URL/ads" -H "X-User-Token: $ADMIN_TOKEN"
check "含搜尋與排序條件"    200 ""                     "$BASE_URL/ads?min_price=1000000&sort_by=price&order=asc&page=1" -H "X-User-Token: $ADMIN_TOKEN"
check "權限不足"            403 MSG_PERMISSION_DENY    "$BASE_URL/ads" -H "X-User-Token: $USER1_TOKEN"
check "無效的 Token"        401 MSG_INVALID_TOKEN      "$BASE_URL/ads" -H 'X-User-Token: invalid'

echo "=== API 15 取消精選房屋 ==="
check "取消成功"            200 ""                     -X DELETE "$BASE_URL/ads/1" -H "X-User-Token: $ADMIN_TOKEN"
check "不存在的精選"        404 MSG_AD_NOT_EXISTS      -X DELETE "$BASE_URL/ads/1" -H "X-User-Token: $ADMIN_TOKEN"
check "權限不足"            403 MSG_PERMISSION_DENY    -X DELETE "$BASE_URL/ads/2" -H "X-User-Token: $USER1_TOKEN"
check "無效的 Token"        401 MSG_INVALID_TOKEN      -X DELETE "$BASE_URL/ads/2"

echo "=== API 9 刪除房屋 ==="
check "無效的 Token"        401 MSG_INVALID_TOKEN      -X DELETE "$BASE_URL/house/$NEW_HOUSE_ID"
check "權限不足"            403 MSG_PERMISSION_DENY    -X DELETE "$BASE_URL/house/$NEW_HOUSE_ID" -H "X-User-Token: $USER2_TOKEN"
check "刪除成功"            200 ""                     -X DELETE "$BASE_URL/house/$NEW_HOUSE_ID" -H "X-User-Token: $USER1_TOKEN"
check "不存在的房屋"        404 MSG_HOUSE_NOT_EXISTS   -X DELETE "$BASE_URL/house/$NEW_HOUSE_ID" -H "X-User-Token: $USER1_TOKEN"

echo "=== API 2 會員登出 ==="
check "登出成功"            200 ""                     -X POST "$BASE_URL/user/logout" -H "X-User-Token: $USER1_TOKEN"
check "無效的 Token"        401 MSG_INVALID_TOKEN      -X POST "$BASE_URL/user/logout" -H "X-User-Token: $USER1_TOKEN"

rm -f "$NOT_IMAGE"

echo
echo "=================================================="
echo "  通過：$PASS_COUNT 項    失敗：$FAIL_COUNT 項"
echo "=================================================="

[ "$FAIL_COUNT" -eq 0 ]
