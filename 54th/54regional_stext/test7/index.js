let count=0

setInterval(function(){
    if(count+1<document.querySelectorAll(".image").length){
        count=count+1
    }else{
        count=0
    }
    document.querySelectorAll(".image").forEach(function(event){
        event.style.display="none"
    })
    document.querySelectorAll(".image")[count].style.display="block"
},1500)