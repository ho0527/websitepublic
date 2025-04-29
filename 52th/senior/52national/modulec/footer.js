let link=document.getElementById("link")
let onoffbutton=document.getElementById("onoff")
let fb=document.getElementById("fb")
let twitter=document.getElementById("twitter")
let youtube=document.getElementById("youtube")

link.style.display="block"

onoffbutton.onclick=function(){
    if(link.style.display=="block"){
        link.style.display="none"
        onoffbutton.value=`開啟footer`
    }else{
        link.style.display="block"
        onoffbutton.value=`關閉footer`
    }
}

fb.onclick=function(){
    location.href="https://www.facebook.com/"
}

twitter.onclick=function(){
    location.href="https://www.twitter.com/"
}

youtube.onclick=function(){
    location.href="https://www.youtube.com/"
}
