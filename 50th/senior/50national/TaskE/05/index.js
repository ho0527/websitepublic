for(let i=0;i<5;i=i+1){
    let letters="0123456789ABCDEF"
    let color1="#"
    let color2="#"
    for(let i=0;i<6;i=i+1){
        color1=color1+letters[Math.floor(Math.random()*16)];
        color2=color2+letters[Math.floor(Math.random()*16)];
    }
    let div1=document.createElement("div")
    div1.classList.add("color")
    div1.id=color1
    div1.dataset.id="right"
    div1.style.backgroundColor=color1
    document.getElementById("colorright").appendChild(div1)
    let div2=document.createElement("div")
    div2.classList.add("color")
    div2.id=color2
    div2.dataset.id="left"
    div2.style.backgroundColor=color2
    document.getElementById("colorleft").appendChild(div2)
}

setTimeout(function(){
    document.querySelectorAll(".color").forEach(function(event){
        event.onclick=function(){
            if(event.dataset.id=="right"){
                document.getElementById("right").value=event.id
            }else{
                document.getElementById("left").value=event.id
            }
        }
    })
},10)

document.getElementById("submit").onclick=function(){
    let right=document.getElementById("right").value
    let left=document.getElementById("left").value

    // Validate input as hexadecimal colors
    if(/^\#([0-9A-F]{3}){1,2}$/.test(right)&&/^\#([0-9A-F]{3}){1,2}$/.test(left)){
        document.getElementById("main").style.background="linear-gradient(to right, "+right+", "+left+")"
    }else{ alert("[ERROR]") }
}