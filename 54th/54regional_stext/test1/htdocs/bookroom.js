let startdate=null
let enddate=null

document.querySelectorAll(".date").forEach(function(event){
    event.onclick=function(){
        if(event.dataset.id=="31"){ alert("無空房! 請重新選擇");return }

        if(!startdate){
            startdate=parseInt(event.dataset.id)
            event.classList.add("select")
            document.getElementById("room").innerHTML="Room"+(Math.floor(Math.random()*8)+1)
        }else if(!enddate){
            enddate=parseInt(event.dataset.id)
            if(startdate<enddate){
                for(let i=startdate;i<enddate;i=i+1){
                    document.querySelectorAll(".date")[i].classList.add("select")
                }

                document.getElementById("sd").innerHTML=enddate-startdate+1
    
                document.getElementById("room").innerHTML="Room"+(Math.floor(Math.random()*8)+1)
            }else{
                enddate=null
            }
        }
    }
})

document.getElementById("select").onclick=function(){
    if(startdate){
        document.getElementById('room').innerHTML='Room'+(Math.floor(Math.random()*8)+1)
    }
}