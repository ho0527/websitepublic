let start=false
// timer1
let timer1
let timer1ms=0
let timer1s=0
let timer1m=0
let timer1h=0
let timer1text="未超時"
// timer2
let timer2
let timer2ms=0
let timer2s=0
let timer2m=0
let timer2h=0
// timer3
let timer3
let timer3ms=0
let timer3s=0
let timer3m=0
let timer3h=4
let timer3pm="m"

document.onkeydown=function(event){
    if(event.key==" "){
        event.preventDefault()

        if(!start){
            timer1=setInterval(function(){
                timer1ms=timer1ms+1
                if(timer1ms==100){
                    timer1ms=0
                    timer1s=timer1s+1
                }
                if(timer1s==60){
                    timer1s=0
                    timer1m=timer1m+1
                }
                if(timer1m==60){
                    timer1m=0
                    timer1h=timer1h+1
                }

                if(timer1h==4&&timer1ms==1){
                    document.getElementById("timer1").classList.add("ot")
                    timer1text="已超時"
                }

                document.getElementById("timer1").innerHTML=`
                    ${String(timer1h).padStart(2,"0")}:${String(timer1m).padStart(2,"0")}:${String(timer1s).padStart(2,"0")}:${String(timer1ms).padStart(2,"0")} ${timer1text}
                `
            },10)
            timer2=setInterval(function(){
                if(timer2h==4&&timer2ms==0){
                    clearInterval(timer2)
                }else{
                    timer2ms=timer2ms+1
                    if(timer2ms==100){
                        timer2ms=0
                        timer2s=timer2s+1
                    }
                    if(timer2s==60){
                        timer2s=0
                        timer2m=timer2m+1
                    }
                    if(timer2m==60){
                        timer2m=0
                        timer2h=timer2h+1
                    }
                    document.getElementById("timer2").innerHTML=`
                        ${String(timer2h).padStart(2,"0")}:${String(timer2m).padStart(2,"0")}:${String(timer2s).padStart(2,"0")}:${String(timer2ms).padStart(2,"0")}
                    `
                }

            },10)
            timer3=setInterval(function(){
                if(timer3pm=="m"){
                    if(timer3m==0){
                        timer3m=60
                        timer3h=timer3h-1
                    }
                    if(timer3s==0){
                        timer3s=60
                        timer3m=timer3m-1
                    }
                    if(timer3ms==0){
                        timer3ms=100
                        timer3s=timer3s-1
                    }
                    timer3ms=timer3ms-1
                    document.getElementById("timer3").innerHTML=`
                        超時時間: -${String(timer3h).padStart(2,"0")}:${String(timer3m).padStart(2,"0")}:${String(timer3s).padStart(2,"0")}:${String(timer3ms).padStart(2,"0")}
                    `
                }else{
                    timer3ms=timer3ms+1
                    if(timer3ms==100){
                        timer3ms=0
                        timer3s=timer3s+1
                    }
                    if(timer3s==60){
                        timer3s=0
                        timer3m=timer3m+1
                    }
                    if(timer3m==60){
                        timer3m=0
                        timer3h=timer3h+1
                    }
                    document.getElementById("timer3").innerHTML=`
                        超時時間: ${String(timer3h).padStart(2,"0")}:${String(timer3m).padStart(2,"0")}:${String(timer3s).padStart(2,"0")}:${String(timer3ms).padStart(2,"0")}
                    `
                }
            },10)
            start=true
        }else{
            clearInterval(timer1)
            clearInterval(timer2)
            clearInterval(timer3)
            start=false
        }
    }
}