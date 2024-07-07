document.getElementById("image").onchange=function(){
    document.getElementById("leftimage").src=document.getElementById("image").value
    if(document.getElementById("filter").value!="default"){
        document.getElementById("rightimage").src=document.getElementById("image").value
        document.getElementById("rightimage").classList=document.getElementById("filter").value
    }
}

document.getElementById("filter").onchange=function(){
    document.getElementById("rightimage").src=document.getElementById("image").value
    document.getElementById("rightimage").classList=document.getElementById("filter").value
}