let circlesize=50

document.getElementById("image").onmouseover=function(event){
    document.getElementById("circle").style.display="block"
    document.getElementById("circle").style.top=(event.offsetY-(circlesize/2))+"px"
    document.getElementById("circle").style.left=(event.offsetX-(circlesize/2))+"px"
    document.getElementById("circle").style.width=(circlesize)+"px"
    document.getElementById("circle").style.height=(circlesize)+"px"
}

document.onwheel=function(event){
    event.preventDefault()
    circlesize=circlesize+(event.deltaY*-0.5)
    console.log("circlesize="+circlesize)

    if(circlesize<=5){
        circlesize=5
    }

    if(circlesize>=500){
        circlesize=500
    }
    document.getElementById("circle").style.width=(circlesize)+"px"
    document.getElementById("circle").style.height=(circlesize)+"px"
}

document.onmouseover=function(event){
    if(event.target!=document.getElementById("circle")&&event.target!=document.getElementById("image")){
        document.getElementById("circle").style.display="none"
    }
}