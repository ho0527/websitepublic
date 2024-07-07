function main(){
    let sin=parseFloat(docgetid("sin").value)
    let cos=parseFloat(docgetid("cos").value)
    let canva=docgetid("canva")
    let ctx=canva.getContext("2d")
    
    ctx.clearRect(0,0,canva.width,canva.height) // 清除畫布

    // 繪製x軸和y軸
    ctx.strokeStyle="black"
    ctx.lineWidth=1
    ctx.beginPath()
    ctx.moveTo(0,canva.height/2)
    ctx.lineTo(canva.width,canva.height/2)
    ctx.moveTo(canva.width/2,0)
    ctx.lineTo(canva.width/2,canva.height)
    ctx.stroke()

    // 繪製x軸刻度
    for(let i=0;i<=canva.width;i=i+canva.width/8){
        ctx.moveTo(i,canva.height/2-5)
        ctx.lineTo(i,canva.height/2+5)
        ctx.stroke()
    }

    // 繪製y軸刻度
    for(let i=0;i<=canva.height;i=i+canva.height/4){
        ctx.moveTo(canva.width/2-5,i)
        ctx.lineTo(canva.width/2+5,i)
        ctx.stroke()
    }

    // 設定畫筆顏色和粗度
    ctx.strokeStyle="red"
    ctx.lineWidth=5

    // 繪製sin波
    ctx.beginPath()
    for(let i=0;i<=canva.width;i=i+1){
        let radians=(i/canva.width)*(Math.PI*2)
        ctx.lineTo(i,Math.sin(radians*sin)*(canva.height/4)+(canva.height/2))
    }
    ctx.stroke()

    // 設定畫筆顏色
    ctx.strokeStyle="blue"
    ctx.lineWidth=5

    // 繪製cos波
    ctx.beginPath()
    for(let i=0;i<=canva.width;i=i+1){
        let radians=(i/canva.width)*(Math.PI*2)
        ctx.lineTo(i,Math.cos(radians*cos)*(canva.height/4)+(canva.height/2))
    }
    ctx.stroke()

    // 設定畫筆顏色和粗度
    ctx.strokeStyle="purple"
    ctx.lineWidth=5

    // 繪製sin+cos波
    ctx.beginPath()
    for(let i=0;i<=canva.width;i=i+1){
        let radians=(i/canva.width)*(Math.PI*2)
        let sinvalue=Math.sin(radians*sin)*(canva.height/4)
        let cosvalue=Math.cos(radians*cos)*(canva.height/4)
        let y=sinvalue+cosvalue+(canva.height/2)
        ctx.lineTo(i,y)
    }
    ctx.stroke()
}



docgetall(".maininput").forEach(function(event){
    event.onchange=function(){
        main()
    }
})

main()