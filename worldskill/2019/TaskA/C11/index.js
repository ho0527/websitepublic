let canva=document.getElementById("canva")
let ctx=canva.getContext("2d")
let x=50
let y=canva.height/2
let radius=50
let speed=2

function drawCircle(){
    // Clear canva
    ctx.clearRect(0,0,canva.width,canva.height)

    // Draw circle
    ctx.beginPath()
    ctx.arc(x,y,radius,0,6)
    ctx.fillStyle="red"
    ctx.fill()
    ctx.closePath()

    x=x+speed
    if(x>canva.width+radius){
        x=-radius
    }

    // Reverse direction if circle reaches the bottom of the canva
    if(y+radius>=canva.height||y-radius<=0){
        speed=speed*-1
    }

    requestAnimationFrame(drawCircle)
}

drawCircle()