document.getElementById("boxshadow").onchange=function(){
    let borderradius=document.getElementById("borderradiu").value
    let boxshadow=document.getElementById("boxshadow").value
    document.getElementById("main").style.borderRadius=borderradius+"px"
    document.getElementById("main").style.boxShadow=boxshadow
}

document.getElementById("borderradiu").onchange=function(){
    let borderradius=document.getElementById("borderradiu").value
    let boxshadow=document.getElementById("boxshadow").value
    document.getElementById("main").style.borderRadius=borderradius+"px"
    document.getElementById("main").style.boxShadow=boxshadow
}