document.getElementById("open").onclick=function(){
    document.getElementById("modal").style.display="block"
    document.body.style.overflow="hidden"
}

document.getElementById("close").onclick=function(){
    document.getElementById("modal").style.display="none"
    document.body.style.overflow="auto"
}