if(weblsget("46nationalmoduleduserid")){
    location.href="admintype.html"
}

docgetid("submit").onclick=function(){
    oldajax("POST","/backend/46nationalmoduled/login",JSON.stringify({
        "username": docgetid("username").value,
        "password": docgetid("password").value
    }),[
        ["Content-Type","application/json"]
    ]).onload=function(){
        let data=JSON.parse(this.responseText)
        if(data["success"]){
            alert("登入成功")
            localStorage.setItem("46nationalmoduleduserid",data["data"])
            location.href="admintype.html"
        }else{
            alert(data["data"])
        }
    }
}

docgetid("reset").onclick=function(){
    location.reload()
}

document.onkeydown=function(event){
    if(event.key=="Enter"){
        docgetid("submit").click()
    }
}