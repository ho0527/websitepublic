setInterval(function(){
    let date=new Date()
    let datehour=date.getHours()
    let dateminute=date.getMinutes()
    let datesecond=date.getSeconds()

    let second=((datesecond/60)*360)+90
    let minute=((dateminute/60)*360)+90+(datesecond/60)*5
    let hour=((datehour/12)*360)+90+(dateminute/60)*30

    document.getElementById("hour").style.transform="rotate("+hour+"deg)"
    document.getElementById("minute").style.transform="rotate("+minute+"deg)"
    document.getElementById("second").style.transform="rotate("+second+"deg)"

    if(second==90){
        document.getElementById("second").style.transitionDuration="0s"
    }else{
        document.getElementById("second").style.transitionDuration="0.1s"
    }
},100)

for(let i=1;i<=60;i=i+1){
    let span=document.createElement("span")
    span.classList.add("clocktick")
    if((i-1)%5==0){
        span.style.width="10px"
        span.style.height="25px"
        span.style.background="black"
    }
    span.style.transform="rotate("+(6*i-6)+"deg) translateX(-50%)"
    document.getElementById("tick").appendChild(span)
}