if(!isset(weblsget("53regionalproductcost"))){ weblsset("53regionalproductcost","") }
if(!isset(weblsget("53regionalproductdescription"))){ weblsset("53regionalproductdescription","") }
if(!isset(weblsget("53regionalproductlink"))){ weblsset("53regionalproductlink","") }
if(!isset(weblsget("53regionalproductname"))){ weblsset("53regionalproductname","") }
if(!isset(weblsget("53regionalproductfile"))){ weblsset("53regionalproductfile","") }
if(!isset(weblsget("53regionalproductedit"))){ weblsset("53regionalproductedit","false") }
if(!isset(weblsget("53regionalproductid"))){ weblsset("53regionalproductid",1) }

docgetid("navigationbar").innerHTML=`
    <div class="navigationbar">
        <div class="navigationbarleft">
            <div class="navigationbartitle">咖啡商品展示系統-選擇版型</div>
        </div>
        <div class="navigationbarright">
            <input type="button" class="navigationbarbutton" onclick="location.href='main.html'" value="首頁">
            <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='productindex.html'" value="上架商品">
            <input type="button" class="navigationbarbutton" onclick="location.href='admin.html'" value="會員管理">
            <input type="button" class="navigationbarbutton" id="logout" value="登出">
        </div>
    </div>
    <div class="navigationbar2">
        <div class="productbardiv center">
            <input type="button" class="navigationbarbutton" id="index" onclick="location.href='productindex.html'" value="選擇版型">
            <input type="button" class="navigationbarbutton" id="input" onclick="location.href='productinput.html'" value="填寫資料">
            <input type="button" class="navigationbarbutton" id="preview" onclick="location.href='productpreview.html'" value="預覽">
            <input type="button" class="navigationbarbutton" id="submit" onclick="location.href='productsubmit.html'" value="確定送出">
            <input type="button" class="navigationbarbutton" id="new" onclick="location.href='newproduct.html'" value="新增版型">
        </div>
    </div>
`