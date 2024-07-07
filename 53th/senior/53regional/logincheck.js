let file=location.href.split("53regional/")[1]
let id=localStorage.getItem("53regionaluserid")
let permission=localStorage.getItem("53regionalpermission")

function logout(){
    oldajax("GET","/backend/53regional/logout/"+weblsget("53regionaluserid")).onload=function(){
        let data=JSON.parse(this.responseText)
        if(data["success"]){
            alert("登出成功")
            weblsset("53regionaluserid",null)
            weblsset("53regionalpermission",null)
            weblsset("53regionaltime",null)
            location.href="index.html"
        }
    }
}

if(file==""||file=="index.html"||file=="usererror.html"){
    if(id!=null){
        location.href="verify.html"
    }
}else if(file=="verify.html"){
    if(id==null){''
        location.href="index.html"
    }
}else if(file=="main.html"){
    if(id==null){
        location.href="index.html"
    }
}else{
    if(id==null||permission!="管理者"){
        location.href="index.html"
    }
}

document.getElementById("logout").onclick=function(){
    logout()
}

startmacossection()