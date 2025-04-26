let input=document.getElementById("input")
let image=document.getElementById("image")

input.addEventListener("change",function(){
    image.src=URL.createObjectURL(input.files[0])
})

// 顯示顏色
image.addEventListener("mousemove",function(event){
    let canvas=document.createElement("canvas")
    let ctx=canvas.getContext("2d")
    canvas.width=image.width
    canvas.height=image.height
    ctx.drawImage(image,0,0,image.width,image.height)
    let rgbdata=ctx.getImageData(event.offsetX,event.offsetY,1,1).data
    let rgb="rgb("+rgbdata[0]+","+rgbdata[1]+","+rgbdata[2]+")"
    document.getElementById("colorrgbtext").innerHTML=`
        RGB: ${rgb}
    `
    document.getElementById("colorshow").style.backgroundColor=rgb

    // // 放大鏡功能
    let magnifier=document.getElementById("canva");
    let mmagnifierctx=magnifier.getContext("2d");
    magnifier.width=100;
    magnifier.height=100;
    mmagnifierctx.drawImage(canvas,event.offsetX-100/7/2,event.offsetY-100/7/2,100/7,100/7,0,0,100,100)
    mmagnifierctx.strokeStyle="black";
    mmagnifierctx.lineWidth=1;
    for(let i=0;i<100;i=i+10){ //列
        for(let j=0;j<100;j=j+10){ //行
            mmagnifierctx.beginPath()
            mmagnifierctx.moveTo(i,j)
            mmagnifierctx.lineTo(i+10,j)
            mmagnifierctx.moveTo(i,j)
            mmagnifierctx.lineTo(i,j+10)
            mmagnifierctx.stroke()
        }
    }
    magnifier.style.left=(event.pageX+20)+"px"
    magnifier.style.top=(event.pageY+20)+"px"
})

document.getElementById("reflashbutton").onclick=function(){
    location.reload()
}