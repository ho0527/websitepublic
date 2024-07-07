let time=document.getElementById("time").value

let timer=setInterval(function(){
    time=time-1
    if(time<=0){
        clearInterval(timer)
        time=0
        document.getElementById("lightbox").style.display="block"
        setTimeout(function(){
            location.href="api.php?logout="
        },5000)
    }
    document.getElementById("timershow").innerHTML=`
        ${time}
    `
},1000)