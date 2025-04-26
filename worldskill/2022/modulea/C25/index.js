document.onpointermove=function(event){
    document.getElementById("cursor").style.left=event.clientX+"px"
    document.getElementById("cursor").style.top=event.clientY+"px"
}

document.onpointerdown=function(event){
    document.getElementById("main").style.display="block"
    document.getElementById("main").style.top=(event.offsetY-50)+"px"
    document.getElementById("main").style.left=(event.offsetX-50)+"px"
}

document.onpointerup=function(){
    setTimeout(function(){
        document.getElementById("main").style.display="none"
    },100)
}