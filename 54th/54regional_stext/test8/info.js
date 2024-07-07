let s=1

document.getElementById("m").onclick=function(){
    if(s-0.1>0){
        s=s-0.1
        document.getElementById("map").style.scale=s
    }
}

document.getElementById("p").onclick=function(){
    s=s+0.1
    document.getElementById("map").style.scale=s
}