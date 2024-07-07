let version=document.querySelectorAll(".product")
let val

version.forEach(function(event){
    event.style.backgroundColor="rgb(35, 35, 35)"
    if(event.id==valdata){
        event.style.background="yellow"
        document.getElementById("val").value=event.id
    }
    event.onclick=function(){
        version.forEach(function(event){
            event.style.backgroundColor="rgb(35, 35, 35)"
        })
        this.style.backgroundColor="yellow"
        document.getElementById("val").value=this.id
    }
})