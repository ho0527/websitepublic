let imagecontainer=document.getElementById("imagecontainer")
let canva=document.getElementById("canva")
let image=null
let scale=1
let scaleX=1 // -1 or 1
let scaleY=1 // -1 or 1
let rotate=0
let ctx

function draw(){
    ctx.clearRect(0,0,canva.width,canva.height);
    ctx.save();
    // Calculate image width and height
    let imageWidth=scaleX*canva.width*scale;
    let imageHeight=scaleY*canva.height*scale;
    // Move the canvas origin to the center
    ctx.translate(canva.width/2,canva.height/2);
    // Perform scale and rotate operations
    ctx.scale(scaleX,scaleY);
    ctx.rotate((Math.PI/180)*rotate);
    // Draw the image at the center of the canvas
    ctx.drawImage(image,-imageWidth/2,-imageHeight/2,imageWidth,imageHeight);
    // Restore the canvas origin
    ctx.restore();
}

imagecontainer.addEventListener("dragover",function(event){
    event.preventDefault()
})

imagecontainer.addEventListener("drop",function(event){
    event.preventDefault()
    let file=event.dataTransfer.files[0]
    if(file.type=="image/png"||file.type=="image/jpeg"){
        let reader=new FileReader()
        reader.onload=function(loadevent){
            image=new Image()
            image.onload=function(){
                imagecontainer.classList.add("is-drop")
                canva.width=image.width
                canva.height=image.height
                ctx=canva.getContext("2d")
                ctx.drawImage(image,0,0,canva.width,canva.height)
            }
            image.src=loadevent.target.result
        }
        reader.readAsDataURL(file)
    }else{
        alert("只能上傳jpg或png圖檔")
    }
})

document.getElementById("plus").onclick=function(){
    if(image){
        scale=scale*1.5
        draw()
    }else{
        alert("請先上傳圖片!")
    }
}

document.getElementById("minus").onclick=function(){
    if(image){
        scale=scale*0.5
        draw()
    }else{
        alert("請先上傳圖片!")
    }
}

document.getElementById("undo").onclick=function(){
    if(image){
        rotate=rotate-90
        draw()
    }else{
        alert("請先上傳圖片!")
    }
}

document.getElementById("redo").onclick=function(){
    if(image){
        rotate=rotate+90
        draw()
    }else{
        alert("請先上傳圖片!")
    }
}

document.getElementById("alth").onclick=function(){
    if(image){
        scaleX=scaleX*-1
        draw()
    }else{
        alert("請先上傳圖片!")
    }
}

document.getElementById("altv").onclick=function(){
    if(image){
        scaleY=scaleY*-1
        draw()
    }else{
        alert("請先上傳圖片!")
    }
}

document.getElementById("trash").onclick=function(){
    location.reload()
}

document.getElementById("download").onclick=function(){
    if(image){
        let alink=document.createElement("a")
        alink.href=canva.toDataURL("image/png")
        alink.download="image.png"
        alink.click()
    }else{
        alert("請先上傳圖片!")
    }
}