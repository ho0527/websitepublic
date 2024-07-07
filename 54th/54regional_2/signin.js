let verifycode=String(parseInt(Math.random()*9999)).padStart(4,"0")

if(weblsget("54regionallogincheck")=="true"){ location.href="admincomment.html" }

docgetid("verifycode").innerHTML=`
    驗證碼: ${verifycode}
`

innerhtml(domgetid("titletext"),`網站管理`,false)

onclick("#reflashverifycode",function(element,event){
    verifycode=String(parseInt(Math.random()*9999)).padStart(4,"0")
    docgetid("verifycode").innerHTML=`
        驗證碼: ${verifycode}
    `
})

onclick("#clear",function(element,event){
    docgetid("username").value=""
    docgetid("password").value=""
    docgetid("verifycodeans").value=""
})

onclick("#submit",function(element,event){
    if(docgetid("username").value!="admin"){
        alert("帳號錯誤")
    }else if(docgetid("password").value!="1234"){
        alert("密碼錯誤")
    }else if(docgetid("verifycodeans").value!=verifycode){
        alert("驗證碼錯誤")
    }else{
        alert("登入成功")
        weblsset("54regionallogincheck","true")
        location.href="admincomment.html"
    }
})

document.onkeydown=function(event){
    if(event.key=="Enter"){
        docgetid("submit").click()
    }
}

passwordshowhide()