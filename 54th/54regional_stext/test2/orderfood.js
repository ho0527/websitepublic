document.querySelectorAll(".number").forEach(function(event){
    event.onchange=function(){
        document.getElementById("total").innerHTML=``
        document.querySelectorAll(".number").forEach(function(event){
            document.getElementById("total").innerHTML=BigInt(document.getElementById("total").innerHTML)+(BigInt(event.dataset.price)*BigInt(event.value))
        })
    }
})