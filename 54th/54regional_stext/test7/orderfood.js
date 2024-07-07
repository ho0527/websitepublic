document.getElementById("data").onchange=function(){
    if(0<document.getElementById("data").value){
        document.getElementById("data").value=parseInt(document.getElementById("data").value)
    }else{
        document.getElementById("data").value=0
    }
    document.getElementById("totalprice").innerHTML=document.getElementById("data").value
}