let maindiv=document.getElementById("main")
let postdiv=document.getElementById("post")
let newchat=document.getElementById("newchat")
let newchatdiv=document.getElementById("newchatdiv")
let allchat=document.getElementById("allchat")
let main=document.getElementById("main")
let back=document.getElementById("back")
// let postdiv=document.getElementById("post")

postdiv.style.display="none"
newchatdiv.style.display="none"

allchat.onclick=function(){
    main.style.display="none"
    postdiv.style.display="inline"
}

back.onclick=function(){
    main.style.display="inline"
    postdiv.style.display="none"
}

newchat.onclick=function(){
    newchatdiv.style.display="inline"
    postdiv.style.display="none"
    maindiv.style.display="none"
}

function filechoose(file){
    document.getElementById(file).click()
}

window.onbeforeunload="none"