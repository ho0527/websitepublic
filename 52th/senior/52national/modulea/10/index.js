document.getElementById("file").addEventListener("change",function(event){
    let colorlist=document.getElementById("color")
    let sorted=[]
    sorted.splice(0,sorted.length)
    let reader=new FileReader()
    let image=document.getElementById("image")
    reader.onload=function(){
        image.src=reader.result
        let img=document.createElement("img")
        img.src=reader.result
        img.onload=function(){
            let canvas=document.createElement("canvas")
            canvas.width=img.width
            canvas.height=img.height
            let ctx=canvas.getContext("2d")
            ctx.drawImage(img,0,0)
            let colors=[]
            for(let i=0;i<canvas.width;i=i+5){
                for(let j=0;j<canvas.height;j=j+5){
                    let pixel=ctx.getImageData(i,j,1,1).data
                    let rgb="rgb("+pixel[0]+","+pixel[1]+","+pixel[2]+")"
                    let colorindex=colors.findIndex(function(indexevent){ return indexevent[0]==rgb })
                    if(colorindex==-1){
                        colors.push([rgb,1])
                    }else{
                        colors[colorindex][1]=colors[colorindex][1]+1
                    }
                }
            }
            colors.sort(function(a,b){ return b[1]-a[1] })
            colorlist.innerHTML=""
            let count
            if(colors.length<3){
                count=colors.length
            }else{
                count=3
            }
            for(let i=0;i<count;i=i+1){
                let color=colors[i][0]
                let p=document.createElement("p")
                p.textContent=color
                p.classList.add("colorname")
                let div=document.createElement("div")
                div.classList.add("colorbox")
                div.style.backgroundColor=color
                div.appendChild(p)
                colorlist.appendChild(div)
            }
        }
    }
    reader.readAsDataURL(event.target.files[0])
})

document.getElementById("reflashbutton").onclick=function(){
    location.reload()
}