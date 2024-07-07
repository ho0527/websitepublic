let open=document.getElementById("open")
let close=document.getElementById("close")
let lightbox=document.getElementById("lightbox")

lightbox.style.display="none"

open.onclick=function(){
    lightbox.style.display="block"
}

close.onclick=function(){
    lightbox.style.display="none"
}