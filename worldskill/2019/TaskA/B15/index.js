const timer=document.getElementById("timer")
const startButton=document.getElementById("start-button")
const stopButton=document.getElementById("stop-button")
const resetButton=document.getElementById("reset-button")
let timerInterval
let seconds3=0
let seconds2=0
let seconds1=0
let centiseconds2=0
let centiseconds1=0
let reset="rgba(206, 206, 206, 0.2)"
let show="red"
//訂定變數

startButton.disabled=false
stopButton.disabled=true


for(let i=1;i<=5;i=i+1){
    document.getElementById("digit"+i).innerHTML=`
        <div class="seg1" id="seg1${ i }"></div>
        <div class="seg2" id="seg2${ i }"></div>
        <div class="seg3" id="seg3${ i }"></div>
        <div class="seg4" id="seg4${ i }"></div>
        <div class="seg5" id="seg5${ i }"></div>
        <div class="seg6" id="seg6${ i }"></div>
        <div class="seg7" id="seg7${ i }"></div>
    `
}

function seg1(id){
    document.querySelectorAll(".seg1")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg2")[id].style.backgroundColor=show
    document.querySelectorAll(".seg3")[id].style.backgroundColor=show
    document.querySelectorAll(".seg4")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg5")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg6")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg7")[id].style.backgroundColor=reset
}

function seg2(id){
    document.querySelectorAll(".seg1")[id].style.backgroundColor=show
    document.querySelectorAll(".seg2")[id].style.backgroundColor=show
    document.querySelectorAll(".seg3")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg4")[id].style.backgroundColor=show
    document.querySelectorAll(".seg5")[id].style.backgroundColor=show
    document.querySelectorAll(".seg6")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg7")[id].style.backgroundColor=show
}

function seg3(id){
    document.querySelectorAll(".seg1")[id].style.backgroundColor=show
    document.querySelectorAll(".seg2")[id].style.backgroundColor=show
    document.querySelectorAll(".seg3")[id].style.backgroundColor=show
    document.querySelectorAll(".seg4")[id].style.backgroundColor=show
    document.querySelectorAll(".seg5")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg6")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg7")[id].style.backgroundColor=show
}

function seg4(id){
    document.querySelectorAll(".seg1")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg2")[id].style.backgroundColor=show
    document.querySelectorAll(".seg3")[id].style.backgroundColor=show
    document.querySelectorAll(".seg4")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg5")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg6")[id].style.backgroundColor=show
    document.querySelectorAll(".seg7")[id].style.backgroundColor=show
}

function seg5(id){
    document.querySelectorAll(".seg1")[id].style.backgroundColor=show
    document.querySelectorAll(".seg2")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg3")[id].style.backgroundColor=show
    document.querySelectorAll(".seg4")[id].style.backgroundColor=show
    document.querySelectorAll(".seg5")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg6")[id].style.backgroundColor=show
    document.querySelectorAll(".seg7")[id].style.backgroundColor=show
}

function seg6(id){
    document.querySelectorAll(".seg1")[id].style.backgroundColor=show
    document.querySelectorAll(".seg2")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg3")[id].style.backgroundColor=show
    document.querySelectorAll(".seg4")[id].style.backgroundColor=show
    document.querySelectorAll(".seg5")[id].style.backgroundColor=show
    document.querySelectorAll(".seg6")[id].style.backgroundColor=show
    document.querySelectorAll(".seg7")[id].style.backgroundColor=show
}

function seg7(id){
    document.querySelectorAll(".seg1")[id].style.backgroundColor=show
    document.querySelectorAll(".seg2")[id].style.backgroundColor=show
    document.querySelectorAll(".seg3")[id].style.backgroundColor=show
    document.querySelectorAll(".seg4")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg5")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg6")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg7")[id].style.backgroundColor=reset
}

function seg8(id){
    document.querySelectorAll(".seg1")[id].style.backgroundColor=show
    document.querySelectorAll(".seg2")[id].style.backgroundColor=show
    document.querySelectorAll(".seg3")[id].style.backgroundColor=show
    document.querySelectorAll(".seg4")[id].style.backgroundColor=show
    document.querySelectorAll(".seg5")[id].style.backgroundColor=show
    document.querySelectorAll(".seg6")[id].style.backgroundColor=show
    document.querySelectorAll(".seg7")[id].style.backgroundColor=show
}

function seg9(id){
    document.querySelectorAll(".seg1")[id].style.backgroundColor=show
    document.querySelectorAll(".seg2")[id].style.backgroundColor=show
    document.querySelectorAll(".seg3")[id].style.backgroundColor=show
    document.querySelectorAll(".seg4")[id].style.backgroundColor=show
    document.querySelectorAll(".seg5")[id].style.backgroundColor=reset
    document.querySelectorAll(".seg6")[id].style.backgroundColor=show
    document.querySelectorAll(".seg7")[id].style.backgroundColor=show
}

function seg0(id){
    document.querySelectorAll(".seg1")[id].style.backgroundColor=show
    document.querySelectorAll(".seg2")[id].style.backgroundColor=show
    document.querySelectorAll(".seg3")[id].style.backgroundColor=show
    document.querySelectorAll(".seg4")[id].style.backgroundColor=show
    document.querySelectorAll(".seg5")[id].style.backgroundColor=show
    document.querySelectorAll(".seg6")[id].style.backgroundColor=show
    document.querySelectorAll(".seg7")[id].style.backgroundColor=reset
}

function resettimer(){
    seconds3=0
    seconds2=0
    seconds1=0
    centiseconds2=0
    centiseconds1=0
    seg0(0)
    seg0(1)
    seg0(2)
    seg0(3)
    seg0(4)
    clearInterval(timerInterval)//暫停執行
}

resettimer()

//設定disabled
startButton.addEventListener("click",function(){//做start的監聽器
    timerInterval=setInterval(updateTimer,10)//執行updateTimer 等10秒
    startButton.disabled=true
    stopButton.disabled=false
    //設定disabled
})

stopButton.addEventListener("click",function(){//做stop的監聽器
    clearInterval(timerInterval)//暫停執行
    startButton.disabled=false
    stopButton.disabled=true
    //設定disabled
})

resetButton.addEventListener("click",function(){
    resettimer()
    startButton.disabled=false
    stopButton.disabled=false
})

function updateTimer(){
    //寫入innerHTML
    centiseconds1=centiseconds1+1
    //將微秒數+1
    if(seconds3==9&seconds2==9&&seconds1==9&&centiseconds2==9&&centiseconds1==9){//當時間=9:59.99時..
        console.log("stop")
        clearInterval(timerInterval)//暫停執行
        setTimeout(function(){//等一下在更新內容
            seg9(0)
            seg9(1)
            seg9(2)
            seg9(3)
            seg9(4)
        },100)
        startButton.disabled=true
        stopButton.disabled=true
        //設定disabled
    }
    if(centiseconds1==1){
        seg0(4)
    }else if(centiseconds1==1){
        seg1(4)
    }else if(centiseconds1==2){
        seg2(4)
    }else if(centiseconds1==3){
        seg3(4)
    }else if(centiseconds1==4){
        seg4(4)
    }else if(centiseconds1==5){
        seg5(4)
    }else if(centiseconds1==6){
        seg6(4)
    }else if(centiseconds1==7){
        seg7(4)
    }else if(centiseconds1==8){
        seg8(4)
    }else if(centiseconds1==9){
        seg9(4)
    }else if(centiseconds1==10){//當第1位微秒數=10時..
        centiseconds1=0//第1為微秒數=0
        centiseconds2=centiseconds2+1//第2位+1
        seg0(4)
    }

    if(centiseconds2==0){
        seg0(3)
    }else if(centiseconds2==1){
        seg1(3)
    }else if(centiseconds2==2){
        seg2(3)
    }else if(centiseconds2==3){
        seg3(3)
    }else if(centiseconds2==4){
        seg4(3)
    }else if(centiseconds2==5){
        seg5(3)
    }else if(centiseconds2==6){
        seg6(3)
    }else if(centiseconds2==7){
        seg7(3)
    }else if(centiseconds2==8){
        seg8(3)
    }else if(centiseconds2==9){
        seg9(3)
    }else if(centiseconds2==10){//當微秒=99時..
        centiseconds1=0//微秒=00
        centiseconds2=0
        seconds1=seconds1+1//秒數+1
        seg0(3)
        seg0(4)
    }

    if(seconds1==0){
        seg0(2)
    }else if(seconds1==1){
        seg1(2)
    }else if(seconds1==2){
        seg2(2)
    }else if(seconds1==3){
        seg3(2)
    }else if(seconds1==4){
        seg4(2)
    }else if(seconds1==5){
        seg5(2)
    }else if(seconds1==6){
        seg6(2)
    }else if(seconds1==7){
        seg7(2)
    }else if(seconds1==8){
        seg8(2)
    }else if(seconds1==9){
        seg9(2)
    }else if(seconds1==10){//當第1位秒數=10時..
        seconds1=0//第1位秒數=0
        seconds2=seconds2+1//第2位+1
    }

    if(seconds2==0){
        seg0(1)
    }else if(seconds2==1){
        seg1(1)
    }else if(seconds2==2){
        seg2(1)
    }else if(seconds2==3){
        seg3(1)
    }else if(seconds2==4){
        seg4(1)
    }else if(seconds2==5){
        seg5(1)
    }else if(seconds2==6){
        seg6(1)
    }else if(seconds2==7){
        seg7(1)
    }else if(seconds2==8){
        seg8(1)
    }else if(seconds2==9){
        seg9(1)
    }else if(seconds2==10){//當第1位秒數=10時..
        seconds2=0//第1位秒數=0
        seconds3=seconds3+1//第2位+1
        seg0(1)
    }

    if(seconds3==0){
        seg0(0)
    }else if(seconds3==1){
        seg1(0)
    }else if(seconds3==2){
        seg2(0)
    }else if(seconds3==3){
        seg3(0)
    }else if(seconds3==4){
        seg4(0)
    }else if(seconds3==5){
        seg5(0)
    }else if(seconds3==6){
        seg6(0)
    }else if(seconds3==7){
        seg7(0)
    }else if(seconds3==8){
        seg8(0)
    }else if(seconds3==9){
        seg9(0)
    }
}

