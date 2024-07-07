let timer

setTimeout(function(){
    document.querySelectorAll(".macossectiondiv").forEach(function(event){
        event.addEventListener("scroll",function(){
            clearTimeout(timer)
            event.setAttribute("scroll","true")
            timer=setTimeout(function(){
                event.removeAttribute("scroll")
            },500)
        })
    })
},200);