const checkDistance=(x1,y1,x2,y2) =>{
    return Math.sqrt(Math.pow(x2 - x1,2) + Math.pow(y2 - y1,2))
}

const decToHex=(dec) =>{
    dec=parseInt(dec) // ensure the dec parameter is integer
    return dec.toString(16) // use toString(16) to transform the dec to hex
}

let loopId,canvas,ctx,particles

const options={
    particlesColor: "#ffffff",
    lineColor: "#2ecce0",
    lineWidth: 0.8,
    particleAmount: 220,
    defaultRadius: 4,
    variantRadius: 3,
    defaultSpeed: 0.8,
    varaintSpeed: 1,
    linkRadius: 150,
    mouseColor: "#f66332",
    mouseRadius: 200,
    mousePushPower: 2
}

const getCanvas=function(){
    canvas=document.querySelector("#canvas")
    ctx=canvas.getContext("2d")
}

let canvasSize
let vw=document.documentElement.clientWidth
let vh=document.documentElement.clientHeight

const resize=function(){
    vw=document.documentElement.clientWidth
    vh=document.documentElement.clientHeight
    canvas.width=vw
    canvas.height=vh
}

const setResize=function(){
    resize()
    window.addEventListener("resize",resize)
}

const initialiseElements=function(){
    particles=[]
    for (let i=0;i<options.particleAmount;i=i+1){
        particles.push(new Particle())
    }
}

class Particle{
    constructor(){
        this.x=Math.random() * canvas.width
        this.y=Math.random() * canvas.height
        this.color=options.particlesColor
        this.radius=options.defaultRadius - Math.random() * options.variantRadius
        this.speed=options.defaultSpeed + Math.random() * options.varaintSpeed
        this.directionAngle=Math.floor(Math.random() * 360)
        this.vector={
            x: Math.cos(this.directionAngle) * this.speed,
            y: Math.sin(this.directionAngle) * this.speed
        }
    }

    draw(){
        ctx.beginPath()
        ctx.arc(this.x,this.y,this.radius,0,Math.PI * 2)
        ctx.closePath()
        ctx.fillStyle=this.color
        ctx.fill()
    }

    encounterBorder(){
        if (this.x >= vw || this.x <= 0){
            this.vector.x *= -1
        }
        if (this.y >= vh || this.y <= 0){
            this.vector.y *= -1
        }
        if (this.x > vw) this.x=vw
        if (this.y > vh) this.y=vh
        if (this.x<0) this.x=0
        if (this.y<0) this.y=0
    }

    encounterMouse(){
        const distance=checkDistance(this.x,this.y,mouse.x,mouse.y)

        if (distance <= options.mouseRadius){
            this.vector.x *= -1
            this.vector.y *= -1
        }
        if (distance<options.mouseRadius){
            const distanceX=this.x - mouse.x
            const distanceY=this.y - mouse.y
            this.x += (distanceX / distance) * options.mousePushPower
            this.y += (distanceY / distance) * options.mousePushPower
        }
    }

    update(){
        this.encounterBorder()
        this.encounterMouse()
        this.x += this.vector.x
        this.y += this.vector.y
    }
}

let mouse={
    x: 0,
    y: 0
}

const initMouse=function(){
    canvas.addEventListener("mousemove",(e) =>{
        mouse.x=e.x
        mouse.y=e.y
    })
}

const drawParticle=function(){
    for (let i=0;i<particles.length;i=i+1){
        particles[i].update()
        particles[i].draw()
    }
}

const drawLine=function(){
    for (let i=0;i<particles.length;i=i+1){
        for (let j=i+1;j<particles.length;j=j+1){
            const distance=checkDistance(
                particles[i].x,
                particles[i].y,
                particles[j].x,
                particles[j].y
            )
            const opacity=1 - distance / options.linkRadius
            if (opacity > 0){
                const lineOpacity=decToHex(opacity * 100)
                ctx.lineWidth=options.lineWidth
                ctx.strokeStyle=`${options.lineColor}${lineOpacity}`
                ctx.beginPath()
                ctx.moveTo(particles[i].x,particles[i].y)
                ctx.lineTo(particles[j].x,particles[j].y)
                ctx.closePath()
                ctx.stroke()
            }
        }
    }
}

const drawScene=function(){
    drawParticle()
    drawLine()
}

const startAnimation=function(){
    ctx.clearRect(0,0,vw,vh)
    drawScene()
    loopId=requestAnimationFrame(startAnimation)
}

const init=function(){
    getCanvas()
    setResize()
    initMouse()
    initialiseElements()
    startAnimation()
}

init()