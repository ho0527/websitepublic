document.querySelectorAll(".card").forEach(function(event){
    let isdrag=false
    let x
    let y
    let offsetx=0
    let offsety=0

    event.addEventListener("pointerdown",function(){
        isdrag=true
        x=event.clientX-offsetx
        y=event.clientY-offsety
        event.style.transform="rotate(15deg)"
    })

    event.addEventListener("pointermove",function(addeventlisterevent){
        if(isdrag){
            addeventlisterevent.preventDefault()
            offsetx=addeventlisterevent.clientX-x
            offsety=addeventlisterevent.clientY-y
            event.style.transform="translate("+offsetx+"px,"+offsety+"px)"
        }
    })

    document.addEventListener("pointerup",function(){
        if(isdrag){
            isdrag=false
            event.style.transform="rotate(0deg)"
        }
    })
})

sort(".card",".group-sortable")