let shift = false

document.querySelectorAll(".key").forEach(function(event){
    event.onclick=function(){
        if(event.innerHTML=="shift"){
            if(shift==true){ shift=false }
            else{ shift=true }
            event.classList.toggle("active",shift)
        }else if(event.innerHTML=="space"){
            document.getElementById("testarea").value=document.getElementById("testarea").value+" "
        }else{
            let text
            if(shift){ text=event.innerHTML.toUpperCase() }
            else{ text=event.innerHTML.toLowerCase() }
            document.getElementById("testarea").value=document.getElementById("testarea").value+text
        }
    }
})