let verifycode=""
let check=0

document.querySelectorAll(".dragimg").forEach(function(event){
    event.addEventListener("dragstart",function(addeventlistenerevent){
        addeventlistenerevent.dataTransfer.setData("imgid",addeventlistenerevent.target.id)
        addeventlistenerevent.dataTransfer.setData("src",addeventlistenerevent.target.src)
    })
})

document.getElementById("dropbox").addEventListener("dragover",function(addeventlistenerevent){
    addeventlistenerevent.preventDefault()
})

document.getElementById("dropbox").addEventListener("drop",function(addeventlistenerevent){
    if(check<3){
        let img=document.createElement("img")
        img.setAttribute("src",addeventlistenerevent.dataTransfer.getData("src"))
        verifycode=verifycode+addeventlistenerevent.dataTransfer.getData("imgid")
        document.querySelectorAll(".dropbox")[0].appendChild(img)
        check=check+1
    }else{
        alert("超過長度請登入或重新產生驗證碼")
    }
})

document.getElementById("login").onclick=function(){
    let ajax=new XMLHttpRequest()

    ajax.onload=function(){
        let data=JSON.parse(ajax.responseText)
        if(data["success"]){
            alert("登入成功")
            localStorage.removeItem("error")
            localStorage.setItem("49regionalid",data["data"]["id"])
            localStorage.setItem("49regionalpermission",data["data"]["permission"])
            localStorage.setItem("49regionaltimer",60)
            let ajax2=new XMLHttpRequest()
            ajax2.onload=function(){
                if(data["data"]["id"]=="a0000"){
                    location.href="verify.php"
                }else{
                    location.href="main.html"
                }
            }
            ajax2.open("GET","api.php?login=true&userid="+data["data"]["id"])
            ajax2.send()
        }else{
            alert(data["data"]["message"])
            if(localStorage.getItem("error")){
                localStorage.setItem("error",parseInt(localStorage.getItem("error"))+1)
                if(localStorage.getItem("error")>=3){
                    let ajax2=new XMLHttpRequest()
                    ajax2.onload=function(){
                        localStorage.removeItem("error")
                        location.href="usererror.html"
                    }
                    ajax2.open("GET","api.php?login=false&userid="+data["data"]["id"])
                    ajax2.send()
                }else{
                    document.getElementById("password").value=""
                }
            }else{
                localStorage.setItem("error",1)
                document.getElementById("password").value=""
            }
        }
    }

    let formdata=new FormData()
    formdata.append("submit","true")
    formdata.append("username",document.getElementById("username").value)
    formdata.append("password",document.getElementById("password").value)
    formdata.append("verifycode",verifycode)

    ajax.open("POST","api/login.php")
    ajax.send(formdata)
}

document.getElementById("clear").onclick=function(){
    location.reload()
}

document.getElementById("reflashpng").onclick=function(){
    location.reload()
}

document.onkeydown=function(event){
    if(event.key=="Enter"){
        event.preventDefault()
        document.getElementById("login").click()
    }
}