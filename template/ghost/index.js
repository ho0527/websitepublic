document.addEventListener("pointermove",function(event){
    document.getElementById("ghost").style.transform="translate("+(event.pageX+2+10)+"px,"+(event.pageY+2+10)+"px)"
    document.getElementById("dot").style.transform="translate("+(event.pageX+2)+"px,"+(event.pageY+2)+"px)"
})

docgetall("body")[0].innerHTML=`
    ${docgetall("body")[0].innerHTML}
    <div class="dot" id="dot"></div>
    <div class="ghost" id="ghost">
        <div class="body"></div>
        <div class="eye1"></div>
        <div class="eye2"></div>
        <div class="mouth"></div>
        <div class="foot1"></div>
        <div class="foot2"></div>
        <div class="foot3"></div>
    </div>
`