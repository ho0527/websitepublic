let time=parseInt(document.getElementById("text").value)

setInterval(function(){
    time=time-1
    if(time<=0){
        document.getElementById("lightbox").style.display="block"
        setTimeout(function(){
            location.href="api/api.php?logout="
        },5000)
    }
    document.getElementById("show").value=time
},1000)