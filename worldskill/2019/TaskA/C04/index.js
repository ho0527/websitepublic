document.querySelectorAll(".range").forEach(function(event){
    event.onchange=function(){
        let r=document.getElementById("r").value
        let g=document.getElementById("g").value
        let b=document.getElementById("b").value
        let color="rgb("+r+","+g+","+b+")"
        document.getElementById("output").innerHTML=`
            ${color}
        `
        if(r>200||g>200||b>200){
            document.getElementById("output").style.color="black"
        }else{
            document.getElementById("output").style.color="white"
        }
        document.getElementById("output").style.backgroundColor=color
    }
})
