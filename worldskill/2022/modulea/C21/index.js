for(let i=0;i<10;i=i+1){
    let div=document.createElement("div")
    div.classList="bubble"
    div.style.width="100px"
    div.style.height="100px"
    div.style.borderRadius="50%"
    div.style.backgroundColor="rgb("+(parseInt(Math.random()*255)+1)+","+(parseInt(Math.random()*255)+1)+","+(parseInt(Math.random()*255)+1)+")"
    div.style.position="absolute"
    div.style.top=(parseInt(Math.random()*(window.innerHeight-99)+1))+"px"
    div.style.left=(parseInt(Math.random()*(window.innerWidth-99)+1))+"px"
    document.getElementById("body").appendChild(div)
}