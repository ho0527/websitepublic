let canva=document.createElement("canvas")
canva.width=1920
canva.height=1080
document.body.appendChild(canva)
canva.style.display="none"
let ctx=canva.getContext("2d")

document.getElementById("input").addEventListener("change",function(){
    let reader=new FileReader()
    reader.onload=function(event){
        let image=new Image()
        image.onload=function(){
            let canvasRatio=canva.width/canva.height
            let imageRatio=image.width/image.height
            if((image.width>canva.width)||(image.height>canva.height)){
                if(imageRatio>canvasRatio){
                    canva.height=image.height*(canva.width/image.width)
                }else{
                    canva.width=image.width*(canva.height/image.height)
                }
            }else{
                canva.width=image.width
                canva.height=image.height
            }
            ctx.drawImage(image,0,0,canva.width,canva.height)
            let imagedata=ctx.getImageData(0,0,canva.width,canva.height)
            for(let i=0;i<imagedata.data.length;i=i+4){
                imagedata.data[i]=255-imagedata.data[i]
                imagedata.data[i+1]=255-imagedata.data[i+1]
                imagedata.data[i+2]=255-imagedata.data[i+2]
            }
        }
        image.src=event.target.result
    }
    reader.readAsDataURL(this.files[0])
})

// 當使用者點擊“產生圖片”按鈕時，生成線條重複的圖案背景圖
document.getElementById("downloadbutton").addEventListener("click",function(){
    let image=new Image()
    image.onload=function(){
        ctx.fillStyle=ctx.createPattern(image,"repeat")
        ctx.fillRect(0,0,canva.width,canva.height)
    }

    // 水平線條
    ctx.strokeStyle="rgb(255,255,255)"
    ctx.lineWidth=1
    for(let y=100;y<canva.height;y=y+100){
        ctx.beginPath()
        ctx.moveTo(0,y)
        ctx.lineTo(canva.width,y)
        ctx.stroke()
    }

    // 垂直線條
    for(let x=100;x<canva.width;x=x+100){
        ctx.beginPath()
        ctx.moveTo(x,0)
        ctx.lineTo(x,canva.height)
        ctx.stroke()
    }

    // 將畫布上的內容轉換為圖片下載
    let link=document.createElement("a")
    link.download="result.jpg"
    link.href=canva.toDataURL("image/jpeg",1.0)
    link.click()
})

document.getElementById("reflashbutton").onclick=function(){
    location.reload()
}