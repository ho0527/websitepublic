let time

function main(){
    let lightbox=document.getElementById("lightbox")
    let timertime=parseInt(localStorage.getItem("49regionaltimer"))

    lightbox.style.display="none"
    document.getElementById("timer").innerHTML=timertime
    time=setInterval(function(){
        document.getElementById("timer").innerHTML=document.getElementById("timer").innerHTML-1
        if(document.getElementById("timer").innerHTML<=0){
            clearInterval(time)
            document.getElementById("timer").innerHTML=0
            lightbox.style.display="block"
            setTimeout(function(){
                location.href="link.php?logout="
            },5000)
        }
    },1000)
}

document.getElementById("inputtimer").value=parseInt(localStorage.getItem("49regionaltimer"))
clearInterval(time)
main()

document.getElementById("timerbutton").onclick=function(){
    if(0<document.getElementById("inputtimer").value&&document.getElementById("inputtimer").value<=999999999999999){
        localStorage.setItem("49regionaltimer",document.getElementById("inputtimer").value)
        clearInterval(time)
        main()
    }
}