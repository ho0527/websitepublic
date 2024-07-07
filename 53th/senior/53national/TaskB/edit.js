let width=localStorage.getItem("width")
let height=localStorage.getItem("height")
let canva=document.getElementById("canva1")
let ctx=canva.getContext("2d")
let isdrawing=false
let mod="select"
let undohistory=[]
let redohistory=[]
let color="black"
let thick=1
let x=0
let y=0
let x1=0
let y1=0
let x2=0
let y2=0
let canvacount=1
let sampleselect=""
let data=[]
let datacount=0
let nowlayer=1
let count

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

function undo(){
    if(undohistory.length==0){ return }

    redohistory.push(ctx.getImageData(0,0,canva.width,canva.height))
    ctx.putImageData(undohistory.pop(),0,0)
}

function redo(){
    if(redohistory.length==0){ return }

    undohistory.push(ctx.getImageData(0,0,canva.width,canva.height))
    ctx.putImageData(redohistory.pop(),0,0)
}

function save(){
    let alink=document.createElement("a")
    alink.href=canva.toDataURL("image/jpg")
    alink.download=date("","_","")+".jpg"
    alink.click()
}

function savesample(){
    data=canva.toDataURL("image/jpg")
    let count=parseInt(localStorage.getItem("count"))
    localStorage.setItem("image"+(count+1),data)
    localStorage.setItem("count",count+1)
    location.reload()
}

function upload(){
}

function selectdown(event){
    for(let i=0;i<data.length;i=i+1){
        for(let j=0;j<data[i][0].length;j=j+1){
            let thisx=data[i][0][j]
            let thisy=data[i][1][j]
            let nowx=event.offsetX
            let nowy=event.offsetY
            let x=Math.abs(thisx-nowx)
            let y=Math.abs(thisy-nowy)
            let z=Math.sqrt((x**2)+(y**2))
            let thisthick=data[i][2][4]
            let thislayer=data[i][2][5]

            if(thislayer==nowlayer){
                if(thisthick<=10){
                    if(z<=(thisthick*20)){
                        let tempdata=data[i]
                        let minx=Math.min(...tempdata[0])
                        let miny=Math.min(...tempdata[1])
                        let maxx=Math.max(...tempdata[0])
                        let maxy=Math.max(...tempdata[1])
                        ctx.strokeStyle="black"
                        ctx.lineWidth=1
                        ctx.rect(minx-5-thisthick,miny-5-thisthick,maxx-minx+5+thisthick*2,maxy-miny+5+thisthick*2)
                        ctx.stroke()
                        isdrawing=true
                        return ;
                    }
                }else{
                    if(z<=(thisthick*5)){
                        let tempdata=data[i]
                        let minx=Math.min(...tempdata[0])
                        let miny=Math.min(...tempdata[1])
                        let maxx=Math.max(...tempdata[0])
                        let maxy=Math.max(...tempdata[1])
                        ctx.strokeStyle="black"
                        ctx.lineWidth=1
                        ctx.rect(minx-5-thisthick,miny-5-thisthick,maxx-minx+5+thisthick*2,maxy-miny+5+thisthick*2)
                        ctx.stroke()
                        isdrawing=true
                        return ;
                    }
                }
            }
        }
    }
    if(!isdrawing){
        selectclear()
    }
}

function selectmove(event){
    if(isdrawing){
    }else{
    }
}

function selectclear(){
    // clear select
}

function paintdown(event){
    undohistory.push(ctx.getImageData(0,0,canva.width,canva.height))
    redohistory.length=0
    ctx.strokeStyle=color
    ctx.lineWidth=thick
    x1=event.offsetX
    y1=event.offsetY
    ctx.lineCap="round"
    ctx.lineJoin="round"
    ctx.beginPath()
    ctx.moveTo(x1,y1)
    ctx.lineTo(x1,y1)
    data.push([
        [x1],
        [y1]
    ])
    ctx.stroke()
    isdrawing=true
}

function paintmove(event){
    if(isdrawing){
        x2=event.offsetX
        y2=event.offsetY
        ctx.beginPath()
        ctx.moveTo(x1,y1)
        ctx.lineTo(x2,y2)
        data[datacount][0].push(x2)
        data[datacount][1].push(y2)
        ctx.stroke()
        x1=x2
        y1=y2
    }
}

function bucket(event){
    undohistory.push(ctx.getImageData(0,0,canva.width,canva.height))
    redohistory.length=0
    x=event.offsetX
    y=event.offsetY
}

function sampledown(event){
    undohistory.push(ctx.getImageData(0,0,canva.width,canva.height))
    redohistory.length=0
    x=event.offsetX+20
    y=event.offsetY+20
    let image=document.getElementById("mainimage")
    ctx.drawImage(image,x,y,image.width,image.height)
    isdrawing=true
}

function samplemove(event){
    if(isdrawing){
        let image=document.getElementById("mainimage")

        x=event.offsetX+20
        y=event.offsetY+20
        document.getElementById("mainimage").style.display="none"
        setTimeout(function(){
            ctx.drawImage(image,x,y,image.width,image.height)
        },100)
    }else{
        if(document.getElementById("mainimage")){
            document.getElementById("mainimage").style.top=(event.offsetY+40)+"px"
            document.getElementById("mainimage").style.left=(event.offsetX+40)+"px"
        }
    }
}

function removealllistener(){
    if(mod !="sample" && document.getElementById("mainimage")){ document.getElementById("mainimage").remove()}
    canva.removeEventListener("pointerdown",selectdown)
    canva.removeEventListener("pointermove",selectmove)
    canva.removeEventListener("pointerdown",paintdown)
    canva.removeEventListener("pointermove",paintmove)
    canva.removeEventListener("pointerdown",bucket)
    canva.removeEventListener("pointerdown",sampledown)
    canva.removeEventListener("pointermove",samplemove)
    selectclear()
    document.querySelectorAll(".button").forEach(function(event){
        event.classList.remove("selectbutton")
    })
}

document.getElementById("new").onclick=function(){ if(confirm("是否裡開編輯頁面?")){ location.href="index.html" }}
document.getElementById("undo").onclick=function(){ undo() }
document.getElementById("redo").onclick=function(){ redo() }
document.getElementById("save").onclick=function(){ save() }
document.getElementById("uploadpicture").onclick=function(){ document.getElementById("file").click() }
document.getElementById("file").onchange=function(){ upload() }
document.getElementById("black").style.borderColor="yellow"
document.getElementById("layer1").style.background="yellow"
document.getElementById("layer1").style.color="black"
document.getElementById("layer1").style.borderBottom="1px yellow solid"
document.querySelectorAll(".savesample").forEach(function(event){ event.onclick=function(){ savesample()} })


if(localStorage.getItem("count")==null){ localStorage.setItem("count",0);}
count=parseInt(localStorage.getItem("count"))

canva.width=width
canva.height=height
canva.style.backgroundColor=localStorage.getItem("backgroundcolor")
canva.addEventListener("pointerdown",selectdown)
canva.addEventListener("pointermove",selectmove)

for(let i=1;i<=count;i=i+1){
    document.getElementById("choosesample").innerHTML=document.getElementById("choosesample").innerHTML+`
       <img src="${localStorage.getItem("image"+i)}" class="sampleimage" draggable="false">
    `
}

document.addEventListener("keydown",function(event){
    if(event.ctrlKey && event.key=="z"){ event.preventDefault();undo()}
    if(event.ctrlKey && event.shiftKey && event.key=="z"){ event.preventDefault();redo()}
    if(event.ctrlKey && event.key=="s"){ event.preventDefault();save()}
    if(event.key=="Escape"){ event.preventDefault();location.reload()}
})

document.querySelectorAll(".button").forEach(function(event){
    event.onclick=function(){
        mod=event.id
        document.querySelectorAll(".button").forEach(function(event){
            if(event.id==mod){ event.classList.add("selectbutton")}
            else{} event.classList.remove("selectbutton")
        })
        removealllistener()
        if(mod=="select"){
            canva.addEventListener("pointerdown",selectdown)
            canva.addEventListener("pointermove",selectmove)
            document.getElementById("select").classList.add("selectbutton")
        }else if(mod=="paint"){
            canva.addEventListener("pointerdown",paintdown)
            canva.addEventListener("pointermove",paintmove)
            document.getElementById("paint").classList.add("selectbutton")
        }else if(mod=="bucket"){
            canva.addEventListener("pointerdown",bucket)
            document.getElementById("bucket").classList.add("selectbutton")
        }else if(mod=="sample"){
            document.getElementById("samplelightbox").style.display="block"
            document.querySelectorAll(".sampleimage").forEach(function(loopevent){
                if(sampleselect !=""){
                    if(loopevent.src==sampleselect){
                        loopevent.style.border="1px yellow solid"
                    }
                }
                loopevent.onclick=function(){
                    document.querySelectorAll(".sampleimage").forEach(function(loopevent){
                        loopevent.style.border="1px black solid"
                    })
                    loopevent.style.border="1px yellow solid"
                }
            })
            canva.addEventListener("pointerdown",sampledown)
            canva.addEventListener("pointermove",samplemove)
            document.getElementById("sample").classList.add("selectbutton")
        }else if(mod=="setcanva"){
            document.getElementById("setcanva").classList.add("selectbutton")
        }else{ }
    }
    if(event.id==mod){ event.classList.add("selectbutton")}
    else{} event.classList.remove("selectbutton")
})

document.querySelectorAll(".color").forEach(function(event){
    event.style.width=getComputedStyle(event).getPropertyValue("height")
    event.style.backgroundColor=event.id
    event.onclick=function(){
        document.querySelectorAll(".color").forEach(function(loopevent){
            loopevent.style.borderColor="black"
        })
        document.getElementById("rainbow").style.borderColor="black"
        this.style.borderColor="yellow"
        color=this.id
        if(mod=="setcanva"){
            canva.style.backgroundColor=color
            localStorage.setItem("backgroundcolor",color)
        }
    }
})

document.getElementById("rainbow").onchange=function(){
    color=this.value
    document.querySelectorAll(".color").forEach(function(event){
        event.style.borderColor="black"
    })
    this.style.borderColor="yellow"
    if(mod=="setcanva"){
        canva.style.backgroundColor=color
        localStorage.setItem("backgroundcolor",color)
    }
}

document.getElementById("thick").onchange=function(){ thick=parseInt(this.value)}

document.getElementById("newlayer").onclick=function(){
    canvacount=canvacount+1
    document.getElementById("layer").innerHTML=document.getElementById("layer").innerHTML+`
       <div class="layergrid" id="layer${canvacount}" data-id="${canvacount}">
           <div class="layername">
                圖層${canvacount}
           </div>
           <div class="layerdef">
               <input type="button" class="layerdel" data-id="${canvacount}" value="刪除">
           </div>
       </div>
    `
    let canvas=document.createElement("canvas")
    canvas.classList.add("canva")
    canvas.id="canva"+canvacount
    canvas.width=width
    canvas.height=height
    canvas.style.zIndex=canvacount
    document.getElementById("main").appendChild(canvas)
    canva=document.getElementById("canva"+canvacount)
    ctx=canva.getContext("2d")
    nowlayer=canvacount
    sort("layergrid","#layer")
    document.querySelectorAll(".layergrid").forEach(function(event){
        event.style.background="none"
        event.style.color="white"
        event.style.borderBottom="1px white solid"
        event.querySelectorAll(".layername")[0].onclick=function(){
            document.querySelectorAll(".layergrid").forEach(function(loopevent){
                loopevent.style.background="none"
                loopevent.style.color="white"
                loopevent.style.borderBottom="1px white solid"
            })
            event.style.background="yellow"
            event.style.color="black"
            event.style.borderBottom="1px yellow solid"
            canva=document.getElementById("canva"+event.dataset.id)
            ctx=canva.getContext("2d")
            nowlayer=event.dataset.id
        }
    })
    document.getElementById("layer"+canvacount).style.background="yellow"
    document.getElementById("layer"+canvacount).style.color="black"
    document.getElementById("layer"+canvacount).style.borderBottom="1px yellow solid"
    document.querySelectorAll(".layerdel").forEach(function(event){
        event.onclick=function(){
            let id=this.getAttribute("data-id")
            document.getElementById("layer"+id).remove()
            document.getElementById("canva"+id).remove()
        }
    })
    removealllistener()
    if(mod=="select"){
        canva.addEventListener("pointerdown",selectdown)
        canva.addEventListener("pointermove",selectmove)
        document.getElementById("select").classList.add("selectbutton")
    }else if(mod=="paint"){
        canva.addEventListener("pointerdown",paintdown)
        canva.addEventListener("pointermove",paintmove)
        document.getElementById("paint").classList.add("selectbutton")
    }else if(mod=="bucket"){
        canva.addEventListener("pointerdown",bucket)
        document.getElementById("bucket").classList.add("selectbutton")
    }else if(mod=="sample"){
        canva.addEventListener("pointerdown",sampledown)
        canva.addEventListener("pointermove",samplemove)
        document.getElementById("sample").classList.add("selectbutton")
    }else if(mod=="setcanva"){
        document.getElementById("setcanva").classList.add("selectbutton")
    }else{ }
}

document.querySelectorAll(".layerdel").forEach(function(event){
    event.onclick=function(){
        let id=this.getAttribute("data-id")
        document.getElementById("layertr"+id).remove()
        document.getElementById("canva"+id).remove()
        if(id==canvacount){ canvacount=canvacount-1 }
        canva=document.getElementById("canva"+canvacount)
        ctx=canva.getContext("2d")
        document.getElementById("canva"+canvacount).style.background="yellow"
        document.getElementById("canva"+canvacount).style.color="white"
        document.getElementById("canva"+canvacount).style.borderBottom="1px yellow solid"
    }
})

document.getElementById("uplaodsample").onclick=function(){ docment.getElementById("samplefile").click() }

document.getElementById("samplefile").onchange=function(event){
    let file=event.target.files[0]
    let reader=new FileReader()
    reader.onload=function(){
        let count=parseInt(localStorage.getItem("count"))
        localStorage.setItem("image"+(count+1),reader.result)
        localStorage.setItem("count",count+1)
    }
    reader.readAsDataURL(file)
    location.reload()
}

document.getElementById("samplesubmit").onclick=function(){
    document.querySelectorAll(".sampleimage").forEach(function(event){
        if(event.style.border=="1px solid yellow"){
            if(document.getElementById("mainimage")){ document.getElementById("mainimage").remove()}
            sampleselect=event.src
            img=document.createElement("img")
            img.src=sampleselect
            img.id="mainimage"
            img.style.position="absolute"
            img.style.opacity=0.5
            img.style.zIndex=999
            document.getElementById("main").appendChild(img)
        }
        document.getElementById("samplelightbox").style.display="none"
    })
}

document.getElementById("close").onclick=function(){ document.getElementById("samplelightbox").style.display="none" }

document.addEventListener("pointerup",function(){
    if(isdrawing){
        if(document.getElementById("mainimage")){ document.getElementById("mainimage").style.display="block" }
        if(mod=="paint"){
            let tempdata=data[datacount]
            let minx=Math.min(...tempdata[0])
            let miny=Math.min(...tempdata[1])
            let maxx=Math.max(...tempdata[0])
            let maxy=Math.max(...tempdata[1])
            data[datacount].push([minx,miny,maxx,maxy,nowlayer,thick])
            datacount=datacount+1
        }
        isdrawing=false
    }
})

setInterval(function(){
    let layer=document.querySelectorAll(".layergrid")
    for(let i=0;i<layer.length;i=i+1){
        let id=layer[i].dataset.id
        document.getElementById("canva"+id).style.zIndex=i+1
    }
},100)

document.getElementById("select").classList.add("selectbutton")

document.getElementById("savecanedit").onclick=function(){
}

document.getElementById("open").onclick=function(){
}