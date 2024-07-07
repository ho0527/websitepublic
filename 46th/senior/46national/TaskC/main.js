let difficulty=weblsget("difficulty")
let timemin=0
let timesec=0
let width=innerWidth*0.6
let height=innerHeight*0.85
let nowblock=""
let nowarray=[] // 現在的狀態
let blocklist=[] // 方塊列表(a~e)
let blockarray=[] // 現在的方塊arr
let temparray=[] // 暫存arr
let blockzeroposition=[3,-3]
let blocklength=40 // 方怪長度
let rotatedeg=0 // 旋轉度數(0,90,180,270)
let speed=1 // 移動速度
let line=0
let start=true
let apptable=""
let nowblockpositionx=blocklength*5 // 定位到中間
let nowblockpositiony=20
let nowblocktimeinterval
let timer

function randomblock(){
    let random=Math.random()*7
    if(random<1){
        return "a"
    }else if(random<2){
        return "b"
    }else if(random<3){
        return "c"
    }else if(random<4){
        return "d"
    }else if(random<5){
        return "e"
    }else if(random<6){
        return "f"
    }else{
        return "g"
    }
}

function block(key){
    let table=doccreate("table")
    table.classList.add("type")
    table.style.width=(blocklength*4)+"px"
    table.style.height=(blocklength*2)+"px"
    if(key=="a"){
        table.innerHTML=`
            <tr>
                <td class="typea1"></td>
                <td class="typea2"></td>
                <td class="typea3"></td>
                <td class="typea4"></td>
            </tr>
            <tr>
                <td class="typea5"></td>
                <td class="typea6"></td>
                <td class="typea7"></td>
                <td class="typea8"></td>
            </tr>
        `
        blockarray=[
            [0,0,0,0],
            [0,0,0,0],
            [1,1,1,1],
            [0,0,0,0]
        ]
    }else if(key=="b"){
        table.innerHTML=`
            <tr>
                <td class="typeb1"></td>
                <td class="typeb2"></td>
                <td class="typeb3"></td>
                <td class="typeb4"></td>
            </tr>
            <tr>
                <td class="typeb5"></td>
                <td class="typeb6"></td>
                <td class="typeb7"></td>
                <td class="typeb8"></td>
            </tr>
        `
        blockarray=[
            [0,0,0,0],
            [0,1,0,0],
            [0,1,1,1],
            [0,0,0,0]
        ]
    }else if(key=="c"){
        table.innerHTML=`
            <tr>
                <td class="typec1"></td>
                <td class="typec2"></td>
                <td class="typec3"></td>
                <td class="typec4"></td>
            </tr>
            <tr>
                <td class="typec5"></td>
                <td class="typec6"></td>
                <td class="typec7"></td>
                <td class="typec8"></td>
            </tr>
        `
        blockarray=[
            [0,0,0,0],
            [0,0,0,1],
            [0,1,1,1],
            [0,0,0,0]
        ]
    }else if(key=="d"){
        table.innerHTML=`
            <tr>
                <td class="typed1"></td>
                <td class="typed2"></td>
                <td class="typed3"></td>
                <td class="typed4"></td>
            </tr>
            <tr>
                <td class="typed5"></td>
                <td class="typed6"></td>
                <td class="typed7"></td>
                <td class="typed8"></td>
            </tr>
        `
        blockarray=[
            [0,0,0,0],
            [0,1,1,0],
            [0,1,1,0],
            [0,0,0,0]
        ]
    }else if(key=="e"){
        table.innerHTML=`
            <tr>
                <td class="typee1"></td>
                <td class="typee2"></td>
                <td class="typee3"></td>
                <td class="typee4"></td>
            </tr>
            <tr>
                <td class="typee5"></td>
                <td class="typee6"></td>
                <td class="typee7"></td>
                <td class="typee8"></td>
            </tr>
        `
        blockarray=[
            [0,0,0,0],
            [0,0,1,1],
            [0,1,1,0],
            [0,0,0,0]
        ]
    }else if(key=="f"){
        table.innerHTML=`
            <tr>
                <td class="typef1"></td>
                <td class="typef2"></td>
                <td class="typef3"></td>
                <td class="typef4"></td>
            </tr>
            <tr>
                <td class="typef5"></td>
                <td class="typef6"></td>
                <td class="typef7"></td>
                <td class="typef8"></td>
            </tr>
        `
        blockarray=[
            [0,0,0,0],
            [0,0,1,0],
            [0,1,1,1],
            [0,0,0,0]
        ]
    }else if(key=="g"){
        table.innerHTML=`
            <tr>
                <td class="typeg1"></td>
                <td class="typeg2"></td>
                <td class="typeg3"></td>
                <td class="typeg4"></td>
            </tr>
            <tr>
                <td class="typeg5"></td>
                <td class="typeg6"></td>
                <td class="typeg7"></td>
                <td class="typeg8"></td>
            </tr>
        `
        blockarray=[
            [0,0,0,0],
            [0,1,1,0],
            [0,0,1,1],
            [0,0,0,0]
        ]
    }else{ conlog("[ERROR]error key","red","15") }
    docappendchild("main",table)
    return table
}

function test(key){
    if(key){
        conlog("teststart","green","15")
        block("a")
        block("b")
        block("c")
        block("d")
        block("e")
        block("f")
        block("g")
    }
}

function left(){
    nowblock.style.left=(nowblockpositionx-blocklength)+"px"
    nowblockpositionx=nowblockpositionx-blocklength
    blockzeroposition=[blockzeroposition[0]-1,blockzeroposition[1]]
}

function right(){
    nowblock.style.left=(nowblockpositionx+blocklength)+"px"
    nowblockpositionx=nowblockpositionx+blocklength
    blockzeroposition=[blockzeroposition[0]+1,blockzeroposition[1]]
}

function rotate(){
    nowblock.style.rotate=(rotatedeg+90)+"deg"
    rotatedeg=rotatedeg+90
}

function down(){
    speed=100
    nowblock.style.top=(nowblockpositiony+blocklength)+"px"
    nowblockpositiony=nowblockpositiony+blocklength
    blockzeroposition=[blockzeroposition[0],blockzeroposition[1]+1]
}

function downtobottom(){
}

// 分享
function share(){
}

// 暫停/繼續
function stopstart(){
    if(start){
        document.onkeydown=function(event){
            if(event.key=="Escape"){ event.preventDefault(); }
            if(event.key=="ArrowLeft"){ event.preventDefault(); }
            if(event.key=="ArrowRight"){ event.preventDefault(); }
            if(event.key=="ArrowUp"){ event.preventDefault(); }
            if(event.key=="ArrowDown"){ event.preventDefault(); }
            if(event.key==" "){ event.preventDefault(); }
            if(event.key=="s"||event.key=="S"){ event.preventDefault(); }
            if(event.key=="p"||event.key=="P"){ event.preventDefault();stopstart() }
        }
        docgetid("app").innerHTML=docgetid("app").innerHTML+`
            <div class="mask" id="mask">暫停</div>
        `
        clearInterval(nowblocktimeinterval)
        clearInterval(timer)
        docgetid("stop").value="繼續遊戲(p)"
        start=false
    }else{
        document.onkeydown=function(event){
            if(event.key=="Escape"){ event.preventDefault();cancel() }
            if(event.key=="ArrowLeft"){ event.preventDefault();left() }
            if(event.key=="ArrowRight"){ event.preventDefault();right() }
            if(event.key=="ArrowUp"){ event.preventDefault();rotate() }
            if(event.key=="ArrowDown"){ event.preventDefault();down() }
            if(event.key==" "){ event.preventDefault();downtobottom() }
            if(event.key=="s"||event.key=="S"){ event.preventDefault();share() }
            if(event.key=="p"||event.key=="P"){ event.preventDefault();stopstart() }
        }
        docgetid("mask").remove()
        nowblocktimeinterval=setInterval(function(){
            nowblock.style.top=(nowblockpositiony+blocklength)+"px"
            nowblockpositiony=nowblockpositiony+blocklength
        },speed)

        timer=setInterval(function(){
            timesec=timesec+1
            if(timesec==60){
                timemin=timemin+1
                timesec=0
            }
            let min=timemin.toString()
            let sec=timesec.toString()
            if(timesec<10){
                sec="0"+sec
            }
            if(timemin<10){
                min="0"+min
            }
            docgetid("time").innerHTML=`
                時間: ${min}:${sec}
            `
        },1000)
        docgetid("stop").value="暫停遊戲(p)"
        start=true
    }
}

// 放棄遊戲
function cancel(){
    start=true
    stopstart()
    if(confirm("是否要放棄遊戲?")){
        location.href="index.html"
    }else{
        start=false
        stopstart()
    }
}

for(let i=0;i<17;i=i+1){
    nowarray.push([])
    temparray.push([])
    apptable=apptable+"<tr>"
    for(let j=0;j<10;j=j+1){
        nowarray[i].push(0)
        temparray[i].push(0)
        apptable=apptable+"<td class=\"apptd\"></td>"
    }
    apptable=apptable+"</tr>"
}

docgetid("app").innerHTML=`
    <table class="apptable" id="apptable">
        ${apptable}
    </table>
`

docgetid("apptable").style.maxWidth=((blocklength*10)+20)+"px"
docgetall(".apptd").forEach(function(event){
    event.style.width=blocklength+"px"
    event.style.height=blocklength+"px"
})

docgetid("main").style.width=width+"px"
docgetid("main").style.height=height+"px"
docgetid("difficulty").innerHTML=difficulty
docgetid("line").innerHTML=`行數: ${line}`

document.onkeydown=function(event){
    if(event.key=="Escape"){ event.preventDefault();cancel() }
    if(event.key=="ArrowLeft"){ event.preventDefault();left() }
    if(event.key=="ArrowRight"){ event.preventDefault();right() }
    if(event.key=="ArrowUp"){ event.preventDefault();rotate() }
    if(event.key=="ArrowDown"){ event.preventDefault();down() }
    if(event.key==" "){ event.preventDefault();downtobottom() }
    if(event.key=="s"||event.key=="S"){ event.preventDefault();share() }
    if(event.key=="p"||event.key=="P"){ event.preventDefault();stopstart() }
}

// 修改speed
if(difficulty=="normal"){
    speed=1000
}else{
    speed=250
}

docgetid("share").onclick=function(){ share() } // 分享

docgetid("stop").onclick=function(){ stopstart() } // 暫停/開始遊戲

docgetid("cancel").onclick=function(){ cancel() } // 放棄遊戲

test(0)
setInterval(function(){
    if(blocklist.length<=10){
        blocklist.push(randomblock())
    }
},500)

blocklist.push(randomblock())

timer=setInterval(function(){
    timesec=timesec+1
    if(timesec==60){
        timemin=timemin+1
        timesec=0
    }
    docgetid("time").innerHTML=`
        時間: ${String(timemin).padStart(2,"0")}:${String(timesec).padStart(2,"0")}
    `
},1000)

// main START
nowblock=block(blocklist.shift())
nowblock.style.position="absolute"
nowblock.style.top=nowblockpositiony+"px"
nowblock.style.left=nowblockpositionx+"px"

nowblocktimeinterval=setInterval(function(){
    nowblockpositiony=nowblockpositiony+blocklength
    nowblock.style.top=nowblockpositiony+"px"
    console.log("nowblockpositiony="+nowblockpositiony)
    blockzeroposition=[blockzeroposition[0],blockzeroposition[1]+1]

    if(blockzeroposition[1]==15){
        clearInterval(nowblocktimeinterval)
        console.log(temparray)
        console.log(blockarray)
        console.log(blockzeroposition)

        temparray[blockzeroposition[1]+0][blockzeroposition[0]+0]=blockarray[0][0]
        temparray[blockzeroposition[1]+1][blockzeroposition[0]+0]=blockarray[0][1]
        temparray[blockzeroposition[1]+2][blockzeroposition[0]+0]=blockarray[0][2]
        temparray[blockzeroposition[1]+3][blockzeroposition[0]+0]=blockarray[0][3]
        temparray[blockzeroposition[1]+0][blockzeroposition[0]+1]=blockarray[1][0]
        temparray[blockzeroposition[1]+1][blockzeroposition[0]+1]=blockarray[1][1]
        temparray[blockzeroposition[1]+2][blockzeroposition[0]+1]=blockarray[1][2]
        temparray[blockzeroposition[1]+3][blockzeroposition[0]+1]=blockarray[1][3]
        temparray[blockzeroposition[1]+0][blockzeroposition[0]+2]=blockarray[2][0]
        temparray[blockzeroposition[1]+1][blockzeroposition[0]+2]=blockarray[2][1]
        temparray[blockzeroposition[1]+2][blockzeroposition[0]+2]=blockarray[2][2]
        temparray[blockzeroposition[1]+3][blockzeroposition[0]+2]=blockarray[2][3]
        temparray[blockzeroposition[1]+0][blockzeroposition[0]+3]=blockarray[3][0]
        temparray[blockzeroposition[1]+1][blockzeroposition[0]+3]=blockarray[3][1]
        temparray[blockzeroposition[1]+2][blockzeroposition[0]+3]=blockarray[3][2]
        temparray[blockzeroposition[1]+3][blockzeroposition[0]+3]=blockarray[3][3]

        console.log(temparray)
        console.log(blockarray)
    }

    console.log(temparray)
    console.log(blockzeroposition)
},speed)

// plugin function
startmacossection()