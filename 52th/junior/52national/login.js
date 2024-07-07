document.querySelectorAll(".editbutton").forEach(function(event){
    event.onclick=function(){
        document.getElementById("main").style.display="none"
        document.getElementById("head").style.display="none"
    }
})