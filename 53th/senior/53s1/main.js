function p1(){
    document.getElementById("1").style.display="block"
    document.getElementById("2").style.display="none"
}

function p2(){
    document.getElementById("1").style.display="none"
    document.getElementById("2").style.display="block"
}

function clearall(){
    document.getElementById("1").style.display="none"
    document.getElementById("2").style.display="none"
}

document.getElementById("1").style.display="block"
document.getElementById("2").style.display="none"

document.getElementById("search").onclick=function(){
    document.getElementById("1").style.display="block"
    document.getElementById("2").style.display="none"
}