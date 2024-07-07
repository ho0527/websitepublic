// let userid=weblsget("51regionaluserid")

// if(!userid){
//     // 登入介面
//     docgetid("main").innerHTML=`
//         <div class="navigationbar">
//             <div class="navigationbartitle center">網路問卷調查系統</div>
//         </div>
//         <div class="main indexmain">
//             <div>
//                 帳號: <input type="text" class="input" id="username"><br><br>
//                 密碼: <input type="password" class="input" id="password"><br><br>
//                 <input type="button" class="button" onclick="location.href='signup.php'" value="註冊">
//                 <input type="reset" class="button" value="清除">
//                 <input type="button" class="button" id="login" value="登入">
//             </div><br>
//             <div>
//                 填寫問卷網址:<br><br>
//                 <input type="text" class="input" id="link" placeholder="請輸入網址">
//                 <input type="button" class="button" id="responsesubmit" value="送出">
//             </div>
//         </div>
//     `
//     docgetid("login").onclick=function(){
//         oldajax("POST","",JSON.stringify({
//             "username": docgetid("username").value,
//             "password": docgetid("password").value
//         })).onload=function(){

//         }
//     }
// }else{
//     // 輸入網址
//     docgetid("main").innerHTML=`
//         <div class="navigationbar">
//             <div class="navigationbarleft"><div class="navigationbartitle">網路問卷管理系統</div></div>
//             <div class="navigationbarright">
//                 <input type="button" class="navigationbarbutton" onclick="location.href='verify.php'" value="返回">
//                 <input type="submit" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
//             </div>
//         </div>
//         <div class="main indexmain">
//             <div>
//                 填寫問卷網址:<br><br>
//                 <input type="text" class="input" id="link" placeholder="請輸入網址">
//                 <input type="button" class="button" id="responsesubmit" value="送出">
//             </div>
//         </div>
//     `
// }

// docgetid("responsesubmit").onclick=function(){
//     oldajax("POST","",JSON.stringify({
//         "username": docgetid("username").value,
//         "password": docgetid("password").value
//     })).onload=function(){

//     }
// }

// if(weblsget("showpassword")=="true"){
//     docgetid("showpassword").value=`隱藏密碼`
//     docgetid("password").type="text"
// }else{
//     docgetid("showpassword").value=`顯示密碼`
//     docgetid("password").type="password"
// }

// docgetid("showpassword").onclick=function(){
//     if(weblsget("showpassword")=="true"){
//         docgetid("showpassword").value=`顯示密碼`
//         docgetid("password").type="password"
//         weblsset("showpassword","false")
//     }else{
//         docgetid("showpassword").value=`隱藏密碼`
//         docgetid("password").type="text"
//         weblsset("showpassword","true")
//     }
// }

passwordshowhide()