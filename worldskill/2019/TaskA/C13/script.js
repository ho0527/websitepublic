function draggable(dom){
    let isdrag=false
    let x
    let y
    let offsetx=0
    let offsety=0

    dom.addEventListener("pointerdown",function(event){
        isdrag=true
        x=event.clientX-offsetx
        y=event.clientY-offsety
    })

    dom.addEventListener("pointermove",function(event){
        if(isdrag){
            event.preventDefault()
            offsetx=event.clientX-x
            offsety=event.clientY-y
            dom.style.transform="translate("+offsetx+"px,"+offsety+"px)"
        }
    })

    document.addEventListener("pointerup",function(){
        if(isdrag){
            isdrag=false
        }
    })
}

draggable(document.getElementById('draggable'));
