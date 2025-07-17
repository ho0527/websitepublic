window.onload=function(){
    document.getElementById("cover").onpointerenter=function(event){
        document.getElementById("shadow").style.display="block"
    }

    document.getElementById("cover").onpointermove=function(event){
        let x=event.clientX
        let y=event.clientY

        document.getElementById("shadow").style.left=(x-150)+"px"
        document.getElementById("shadow").style.top=(y-150)+"px"
    }

    document.getElementById("cover").onpointerleave=function(event){
        document.getElementById("shadow").style.display="none"
    }
    
    document.querySelectorAll("img").forEach(function(element){
        element.onclick=function(){
            document.getElementById("lightbox").style.display="block"
            document.getElementById("bigimage").src=element.src
            document.getElementById("lightbox").onclick=function(){
                document.getElementById("lightbox").style.display="none"
            }
            window.onscroll=function(){
                document.getElementById("lightbox").style.display="none"
            }
        }
    })
}
