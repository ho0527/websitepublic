const AJAXURL="/backend/51nationalmoduled/api/"
const ERRORMESSAGE={
	"MSG_INVALID_LOGIN": "登入失敗(帳號或密碼有誤)",
	"MSG_USER_EXISTS": "使⽤者已存在",
	"MSG_INVALID_TOKEN": "無效的Token",
	"MSG_PERMISSION_DENY": "權限不⾜",
	"MSG_MISSING_FIELD": "缺少下列欄位",
	"MSG_WRONG_DATA_TYPE": "下列欄位資料格式錯誤",
	"MSG_IMAGE_CAN_NOT_PROCESS": "圖片格式錯誤(僅支援jpg,jpeg,png,gif)",
	"MSG_INVALID_COVER_INDEX": "封⾯索引錯誤(無該圖片)",
	"MSG_HOUSE_NOT_EXISTS": "房屋不存在",
	"MSG_HOUSE_APPLIED": "申請精選房屋中",
	"MSG_HOUSE_ADVERTISED": "此房屋已是精選房屋",
	"MSG_APPLICATION_NOT_EXISTS": "申請不存在",
	"MSG_ALREADY_ADVERTISED": "申請已審核",
	"MSG_AD_NOT_EXISTS": "精選房屋不存在",
}
let file=getfile().split(".")[0]

if(weblsget("51nationalmoduled-permission")){
	if(weblsget("51nationalmoduled-permission")=="USER"){
		innerhtml("#navigationbar",`
			<div class="navigationbarleft">
				<img src="/material/icon/mainicon.png" class="logo">
			</div>
			<div class="navigationbarright">
				<input type="button" class="navigationbarbutton" id="index" onclick="href('index.html')" value="首頁">
				<input type="button" class="navigationbarbutton" id="publish" onclick="href('publish.html')" value="刊登列表">
				<input type="button" class="navigationbarbutton" id="signout" value="登出">
			</div>
		`,false)
	}else{
		innerhtml("#navigationbar",`
			<div class="navigationbarleft">
				<img src="/material/icon/mainicon.png" class="logo">
			</div>
			<div class="navigationbarright">
				<input type="button" class="navigationbarbutton" id="index" onclick="href('index.html')" value="首頁">
				<input type="button" class="navigationbarbutton" id="publish" onclick="href('publish.html')" value="刊登列表">
				<input type="button" class="navigationbarbutton" id="application" onclick="href('application.html')" value="申請列表">
				<input type="button" class="navigationbarbutton" id="ads" onclick="href('ads.html')" value="精選房屋列表">
				<input type="button" class="navigationbarbutton" id="signout" value="登出">
			</div>
		`,false)
	}
}else{
	innerhtml("#navigationbar",`
		<div class="navigationbarleft">
			<img src="/material/icon/mainicon.png" class="logo">
		</div>
		<div class="navigationbarright">
			<input type="button" class="navigationbarbutton" id="index" onclick="href('index.html')" value="首頁">
			<input type="button" class="navigationbarbutton" id="signup" onclick="href('signup.html')" value="註冊">
			<input type="button" class="navigationbarbutton" id="signin" onclick="href('signin.html')" value="登入">
		</div>
	`,false)
}

addclass("#"+file,["navigationbarselect"])
if(file=="house"){
	addclass("#index",["navigationbarselect"])
}

if(file=="publish"){
	if(!weblsget("51nationalmoduled-token")){
		href("index.html")
	}
}else if(file=="signup"||file=="signin"){
	if(weblsget("51nationalmoduled-token")){
		href("index.html")
	}
}

startmacossection()