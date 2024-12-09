let width=weblsget("width")
let height=weblsget("height")
let canva=docgetid("canva1")
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

docgetid("width").value=width
docgetid("height").value=height
canva.width=width
canva.height=height
canva.style.backgroundColor=weblsget("backgroundcolor")
if(!isset(weblsget("count"))){ weblsset("count",0); }

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
    let count=parseInt(weblsget("count"))
    weblsset("image"+(count+1),data)
    weblsset("count",count+1)
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
    let image=docgetid("mainimage")
    ctx.drawImage(image,x,y,image.width,image.height)
    isdrawing=true
}

function samplemove(event){
    if(isdrawing){
        let image=docgetid("mainimage")

        x=event.offsetX+20
        y=event.offsetY+20
        docgetid("mainimage").style.display="none"
        setTimeout(function(){
            ctx.drawImage(image,x,y,image.width,image.height)
        },100)
    }else{
        if(docgetid("mainimage")){
            docgetid("mainimage").style.top=(event.offsetY+40)+"px"
            docgetid("mainimage").style.left=(event.offsetX+40)+"px"
        }
    }
}

function removealllistener(){
    if(mod!="sample"&&docgetid("mainimage")){ docgetid("mainimage").remove() }
    canva.removeEventListener("pointerdown",selectdown)
    canva.removeEventListener("pointermove",selectmove)
    canva.removeEventListener("pointerdown",paintdown)
    canva.removeEventListener("pointermove",paintmove)
    canva.removeEventListener("pointerdown",bucket)
    canva.removeEventListener("pointerdown",sampledown)
    canva.removeEventListener("pointermove",samplemove)
}

docgetid("new").onclick=function(){ if(confirm("是否裡開編輯頁面?")){ location.href="index.html" } }
docgetid("undo").onclick=function(){ undo() }
docgetid("redo").onclick=function(){ redo() }
docgetid("save").onclick=function(){ save() }
docgetall(".savesample").forEach(function(event){ event.onclick=function(){ savesample() } })
docgetid("uploadpicture").onclick=function(){ docgetid("file").click() }
docgetid("file").onchange=function(){ upload() }
docgetid("black").style.borderColor="yellow"
docgetid("layer1").style.background="yellow"
docgetid("layer1").style.color="black"
docgetid("layer1").style.borderBottom="1px yellow solid"
canva.addEventListener("pointerdown",selectdown)
canva.addEventListener("pointermove",selectmove)

let count=parseInt(weblsget("count"))
for(let i=1;i<=count;i=i+1){
    docgetid("choosesample").innerHTML=docgetid("choosesample").innerHTML+`
        <img src="${weblsget("image"+i)}" class="sampleimage" draggable="false">
    `
}

document.addEventListener("keydown",function(event){
    if(event.ctrlKey&&event.key=="z"){ event.preventDefault();undo() }
    if(event.ctrlKey&&event.shiftKey&&event.key=="z"){ event.preventDefault();redo() }
    if(event.ctrlKey&&event.key=="s"){ event.preventDefault();save() }
    if(event.key=="Escape"){ event.preventDefault();location.reload() }
})

docgetall(".button").forEach(function(event){
    event.onclick=function(){
        mod=event.id
        docgetall(".button").forEach(function(event){
            if(event.id==mod){ event.classList.add("selectbutton") }
            else{ event.classList.remove("selectbutton") }
        })
        if(mod=="select"){
            removealllistener()
            canva.addEventListener("pointerdown",selectdown)
            canva.addEventListener("pointermove",selectmove)
        }else if(mod=="paint"){
            removealllistener()
            canva.addEventListener("pointerdown",paintdown)
            canva.addEventListener("pointermove",paintmove)
        }else if(mod=="bucket"){
            removealllistener()
            canva.addEventListener("pointerdown",bucket)
        }else if(mod=="sample"){
            removealllistener()
            docgetid("samplelightbox").style.display="block"
            docgetall(".sampleimage").forEach(function(event){
                if(sampleselect!=""){
                    if(event.src==sampleselect){
                        event.style.border="1px yellow solid"
                    }
                }
                event.onclick=function(){
                    docgetall(".sampleimage").forEach(function(event){
                        event.style.border="1px black solid"
                    })
                    event.style.border="1px yellow solid"
                }
            })
            canva.addEventListener("pointerdown",sampledown)
            canva.addEventListener("pointermove",samplemove)
        }else if(mod=="setcanva"){
            removealllistener()
        }else{
            removealllistener()
        }
    }
    if(event.id==mod){ event.classList.add("selectbutton") }
    else{ event.classList.remove("selectbutton") }
})

docgetall(".color").forEach(function(event){
    event.style.width=getComputedStyle(event).getPropertyValue("height")
    event.style.backgroundColor=event.id
    event.onclick=function(){
        docgetall(".color").forEach(function(event2){
            event2.style.borderColor="black"
        })
        docgetid("rainbow").style.borderColor="black"
        this.style.borderColor="yellow"
        color=this.id
        if(mod=="setcanva"){
            canva.style.backgroundColor=color
            weblsset("backgroundcolor",color)
        }
    }
})

docgetid("rainbow").onchange=function(){
    color=this.value
    docgetall(".color").forEach(function(event2){
        event2.style.borderColor="black"
    })
    this.style.borderColor="yellow"
    if(mod=="setcanva"){
        canva.style.backgroundColor=color
        weblsset("backgroundcolor",color)
    }
}

docgetid("thick").onchange=function(){ thick=parseInt(this.value) }

docgetid("newlayer").onclick=function(){
    canvacount=canvacount+1
    docgetid("layer").innerHTML=docgetid("layer").innerHTML+`
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
    docgetid("main").appendChild(canvas)
    canva=docgetid("canva"+canvacount)
    ctx=canva.getContext("2d")
    sort("layergrid","#layer")
    docgetall(".layergrid").forEach(function(event){
        event.style.background="none"
        event.style.color="white"
        event.style.borderBottom="1px white solid"
        event.querySelectorAll(".layername")[0].onclick=function(){
            docgetall(".layergrid").forEach(function(foreachevent){
                foreachevent.style.background="none"
                foreachevent.style.color="white"
                foreachevent.style.borderBottom="1px white solid"
            })
            event.style.background="yellow"
            event.style.color="black"
            event.style.borderBottom="1px yellow solid"
            canva=docgetid("canva"+event.dataset.id)
            ctx=canva.getContext("2d")
        }
    })
    docgetid("layer"+canvacount).style.background="yellow"
    docgetid("layer"+canvacount).style.color="black"
    docgetid("layer"+canvacount).style.borderBottom="1px yellow solid"
    docgetall(".layerdel").forEach(function(event){
        event.onclick=function(){
            let id=this.getAttribute("data-id")
            docgetid("layer"+id).remove()
            docgetid("canva"+id).remove()
        }
    })
    removealllistener()
    if(mod=="select"){
        canva.addEventListener("pointerdown",selectdown)
        canva.addEventListener("pointermove",selectmove)
    }else if(mod=="paint"){
        canva.addEventListener("pointerdown",paintdown)
        canva.addEventListener("pointermove",paintmove)
    }else if(mod=="bucket"){
        canva.addEventListener("pointerdown",bucket)
    }else if(mod=="sample"){
        canva.addEventListener("pointerdown",sampledown)
        canva.addEventListener("pointermove",samplemove)
    }else{ }
}

docgetall(".layerdel").forEach(function(event){
    event.onclick=function(){
        let id=this.getAttribute("data-id")
        docgetid("layertr"+id).remove()
        docgetid("canva"+id).remove()
        if(id==canvacount){ canvacount=canvacount-1 }
        canva=docgetid("canva"+canvacount)
        ctx=canva.getContext("2d")
        docgetid("canva"+canvacount).style.background="yellow"
        docgetid("canva"+canvacount).style.color="white"
        docgetid("canva"+canvacount).style.borderBottom="1px yellow solid"
    }
})

docgetid("uplaodsample").onclick=function(){ docment.getElementById("samplefile").click() }

docgetid("samplefile").onchange=function(event){
    let file=event.target.files[0]
    let reader=new FileReader()
    reader.onload=function(){
        let count=parseInt(weblsget("count"))
        weblsset("image"+(count+1),reader.result)
        weblsset("count",count+1)
    }
    reader.readAsDataURL(file)
    location.reload()
}

docgetid("samplesubmit").onclick=function(){
    docgetall(".sampleimage").forEach(function(event){
        if(event.style.border=="1px solid yellow"){
            if(docgetid("mainimage")){ docgetid("mainimage").remove() }
            sampleselect=event.src
            img=document.createElement("img")
            img.src=sampleselect
            img.id="mainimage"
            img.style.position="absolute"
            img.style.opacity=0.5
            img.style.zIndex=999
            docgetid("main").appendChild(img)
        }
        docgetid("samplelightbox").style.display="none"
    })
}

docgetid("close").onclick=function(){ docgetid("samplelightbox").style.display="none" }

document.addEventListener("pointerup",function(){
    if(isdrawing){
        if(docgetid("mainimage")){ docgetid("mainimage").style.display="block" }
        if(mod=="paint"){
            let tempdata=data[datacount]
            let minx=Math.min(...tempdata[0])
            let miny=Math.min(...tempdata[1])
            let maxx=Math.max(...tempdata[0])
            let maxy=Math.max(...tempdata[1])
            data[datacount].push([minx,miny,maxx,maxy,nowlayer,thick])
            datacount=datacount+1
        }
        // if(mod=="paint"){
        //     let tempdata=data[datacount]

        //     let datasortx=tempdata[0].sort(function(a,b){ return a-b })
        //     let datasorty=tempdata[1].sort(function(a,b){ return a-b })
        //     let minx=datasortx[0]
        //     let miny=datasorty[0]
        //     let maxx=datasortx[datasortx.length-1]
        //     let maxy=datasorty[datasorty.length-1]

        //     // let minx=Math.min(...tempdata[0])
        //     // let miny=Math.min(...tempdata[1])
        //     // let maxx=Math.max(...tempdata[0])
        //     // let maxy=Math.max(...tempdata[1])

        //     // let minx=tempdata[0].reduce(function(a,b){ return Math.min(a,b) })
        //     // let miny=tempdata[1].reduce(function(a,b){ return Math.min(a,b) })
        //     // let maxx=tempdata[0].reduce(function(a,b){ return Math.max(a,b) })
        //     // let maxy=tempdata[1].reduce(function(a,b){ return Math.max(a,b) })

        //     data[datacount].push([minx,miny,maxx,maxy])
        //     ctx.strokeStyle="blue"
        //     ctx.lineWidth=1
        //     ctx.rect(minx-5-thick,miny-5-thick,maxx-minx+5+thick*2,maxy-miny+5+thick*2)
        //     ctx.stroke()
        //     console.log(data)
        //     datacount=datacount+1
        //     console.log(minx)
        //     console.log(maxx)
        //     console.log(miny)
        //     console.log(maxy)
        // }
        isdrawing=false
    }
})

docgetid("submit").onclick=function(){
    let width=docgetid("width").value
    let height=docgetid("height").value
    if(/^[0-9]+$/.test(width)&&/^[0-9]+$/.test(height)){
        location.href="edit.html"
        weblsset("width",width)
        weblsset("height",height)
    }else{ alert("長寬要是整數") }
}

setInterval(function(){
    let layer=docgetall(".layergrid")
    for(let i=0;i<layer.length;i=i+1){
        let id=layer[i].dataset.id
        docgetid("canva"+id).style.zIndex=i+1
    }
},100)