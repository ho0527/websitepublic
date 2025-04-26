let ctx=canva.getContext("2d")
let isdrawing=false
let ismovesize=false
let color="black"
let thick=1
let paintstin=1
let x1=0
let y1=0
let x2=0
let y2=0

document.getElementById("canva").width=300
document.getElementById("canva").height=300
canva.style.backgroundColor="white"

function date(ymdlink,midlink,hmslink){
    let date=new Date()
    let year=date.getFullYear().toString().padStart(4,"0")
    let month=(date.getMonth()+1).toString().padStart(2,"0")
    let day=date.getDate().toString().padStart(2,"0")
    let hour=date.getHours().toString().padStart(2,"0")
    let minute=date.getMinutes().toString().padStart(2,"0")
    let second=date.getSeconds().toString().padStart(2,"0")

    return year+ymdlink+month+ymdlink+day+midlink+hour+hmslink+minute+hmslink+second
}

function paintdown(event){
    console.log(event)
    ctx.strokeStyle=color
    ctx.lineWidth=thick
    x1=event.offsetX
    y1=event.offsetY
    ctx.lineCap="round"
    ctx.lineJoin="round"
    isdrawing=true
}

function paintmove(event){
    if(isdrawing){
        x2=event.offsetX
        y2=event.offsetY
        ctx.beginPath()
        ctx.moveTo(x1, y1)
        ctx.lineTo(x2, y2)
        ctx.stroke()
        x1=x2
        y1=y2
    }
}

function paintup(){
    if(isdrawing){
        isdrawing=false
    }
}

document.getElementById("savejpg").onclick=function(){
    let alink=document.createElement("a")
    alink.href=canva.toDataURL("image/jpg")
    alink.download=date("","_","")+".jpg"
    alink.click()
}

document.getElementById("savepng").onclick=function(){
    let alink=document.createElement("a")
    alink.href=canva.toDataURL("image/png")
    alink.download=date("","_","")+".png"
    alink.click()
}

canva.addEventListener("pointerdown",paintdown)
canva.addEventListener("pointermove",paintmove)
canva.addEventListener("pointerup",paintup)

document.querySelectorAll(".color").forEach(function(event){
    event.style.backgroundColor=event.id
    event.onclick=function(){
        document.querySelectorAll(".color").forEach(function(event2){
            event2.style.borderColor="black"
        })
        this.style.borderColor="yellow"
        color=this.id
    }
})

document.onpointerup=function(){
    if(isdrawing){ isdrawing=false}
    if(ismovesize){ ismovesize=false}
}

document.getElementById("movesize").onmousedown=function(){
    ismovesize=true
    document.getElementById("movesize").style.cursor="grabbing"
}

document.onmousemove=function(event){
    if(ismovesize){
        document.getElementById("canva").width=event.pageX
        document.getElementById("canva").height=event.pageY-130
        document.getElementById("movesize").style.left=event.pageX+"px"
        document.getElementById("movesize").style.top=event.pageY-130+"px"
    }
}