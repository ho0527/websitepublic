document.getElementById("data").onchange=function(){
    if(document.getElementById("data").value>0){
        document.getElementById("data").value=parseInt(document.getElementById("data").value)
        document.getElementById("total").innerHTML=document.getElementById("data").value
    }else{
        document.getElementById("data").value="0"
        document.getElementById("total").innerHTML="0"
    }
}