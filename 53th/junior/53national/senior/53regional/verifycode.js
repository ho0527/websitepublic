let verifycode=""

document.querySelectorAll(".dragimg").forEach(function(event){
    event.addEventListener("dragstart",function(addeventlistenerevent){
        addeventlistenerevent.dataTransfer.setData("imgid",addeventlistenerevent.target.id)
    })
})

document.querySelectorAll(".block").forEach(function(event){
    event.addEventListener("dragover",function(addeventlistenerevent){
        addeventlistenerevent.preventDefault()
    })
    event.addEventListener("drop",function(addeventlistenerevent){
        let id=document.getElementById(addeventlistenerevent.dataTransfer.getData("imgid"))
        verifycode=verifycode+id.getAttribute("data-id")
        document.querySelectorAll(".dropbox")[0].appendChild(id)
    })
})

function login(){
    let username=document.getElementById("username").value
    let password=document.getElementById("password").value
    location.href="login.php?username="+username+"&password="+password+"&verifycode="+verifycode
}