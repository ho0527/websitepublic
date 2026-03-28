let file=location.href.split("/")[location.href.split("/").length-1].split(".")[0]
let ajaxurl="/flask/54regional"

if(!weblsget("54regionallogincheck")){ weblsget("54regionallogincheck",false) }

if(file==""){ location.href="index.html" }

if(docgetid("navigationbar")){
    docgetid("navigationbar").innerHTML=`
        <div class="navigationbar">
            <div class="navigationbarright">
                <div class="navigationbarrightphonebar" id="phonebar">
                    <div class="navigationbarrightphonebarline"></div>
                    <div class="navigationbarrightphonebarline"></div>
                    <div class="navigationbarrightphonebarline"></div>
                </div>
                <div class="navigationbarrightbuttonlist" id="navbarbuttonlist">
                    <input type="button" class="navigationbarbutton linkbutton" id="index" value="首頁">
                    <input type="button" class="navigationbarbutton linkbutton" id="comment" value="訪客留言">
                    <input type="button" class="navigationbarbutton linkbutton" id="bookroom" value="訪客訂房">
                    <input type="button" class="navigationbarbutton linkbutton" id="orderfood" value="訪客訂餐">
                    <input type="button" class="navigationbarbutton linkbutton" id="signin" value="網站管理">
                </div>
            </div>
        </div>
    `

    docgetid(file).classList.add("navigationbarselect")

    onclick(".linkbutton",function(element,event){
        location.href=element.id+".html"
    })
}

if(docgetid("adminnavigationbar")){
    docgetid("adminnavigationbar").innerHTML=`
        <div class="navigationbar">
            <div class="navigationbarright">
                <div class="navigationbarrightphonebar" id="phonebar">
                    <div class="navigationbarrightphonebarline"></div>
                    <div class="navigationbarrightphonebarline"></div>
                    <div class="navigationbarrightphonebarline"></div>
                </div>
                <div class="navigationbarrightbuttonlist" id="navbarbuttonlist">
                    <input type="button" class="navigationbarbutton linkbutton" id="index" value="首頁">
                    <input type="button" class="navigationbarbutton linkbutton" id="comment" value="訪客留言">
                    <input type="button" class="navigationbarbutton linkbutton" id="bookroom" value="訪客訂房">
                    <input type="button" class="navigationbarbutton linkbutton" id="orderfood" value="訪客訂餐">
                    <input type="button" class="navigationbarbutton" id="signout" value="登出">
                </div>
            </div>
        </div>
    `

    onclick(".linkbutton",function(element,event){
        location.href=element.id+".html"
    })

    onclick("#signout",function(element,event){
        weblsset("54regionallogincheck","false")
        location.href="signin.html"
    })
}

docgetid("phonebar").onclick=function(){
    if(docgetid("navbarbuttonlist").style.display=="block"){
        docgetid("navbarbuttonlist").style.display="none"
        docgetid("phonebar").style.rotate="0deg"
    }else{
        docgetid("navbarbuttonlist").style.display="block"
        docgetid("phonebar").style.rotate="90deg"
    }
}

startmacossection()