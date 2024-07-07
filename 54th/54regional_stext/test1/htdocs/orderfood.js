document.querySelectorAll(".number").forEach(function(event){
    event.onchange=function(){
        document.getElementById("price").innerHTML=0
        document.querySelectorAll(".number").forEach(function(event){
            document.getElementById("price").innerHTML=BigInt(document.getElementById("price").innerHTML)+BigInt(event.dataset.price*event.value)
        })
    }
})