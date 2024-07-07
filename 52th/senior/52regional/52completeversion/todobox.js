let user=document.getElementById("user-button")
let setting=document.getElementById("setting-button")
let loggout=document.getElementById("loggout-button")

let workbox=document.getElementsByClassName("work-box")
let button=document.getElementsByClassName("todobut")
let updownbut=document.getElementById("updownbut")
let down=false
let move=false
//訂定變數

user.style.display="inline"
setting.style.display="none"
loggout.style.display="none"

user.onclick=function(){
    user.style.display="none"
    setting.style.display="inline"
    loggout.style.display="inline"
}

for(let i=0;i<button.length;i=i+1){
    button[i].disabled=true//讓每個button都是disabled
}

for(let i=0;i<workbox.length;i=i+1){//做總workbox數
    workbox[i].addEventListener('click',function(){
        down=false
        move=false
        let buttons=this.querySelectorAll(".todobut")//選擇該todobut
        for(let i=0;i<button.length;i=i+1){
            button[i].disabled=true//將其他todobut disabled
        }
        for(let i=0;i<buttons.length;i=i+1){
            buttons[i].disabled=false//將該todobut disabled false
            setTimeout(function(){
                for(let j=0;j<button.length;j=j+1){
                    button[j].disabled=true //等待5秒設為true
                }
            },5000)
        }
        console.log(workbox[i]);
    })
}

document.querySelectorAll(".todo").forEach(function(element){
    element.addEventListener("mousedown",function(){
        down=true
    })
})

document.querySelectorAll(".todo").forEach(function(element){
    element.addEventListener("mousemove",function(event){
        if(down==true){
            move=true
        }
    })
})

document.querySelectorAll(".todo").forEach(function(element){
    element.addEventListener("mouseup",function(){
        if(move==true){
            location.href="useradd.php"
        }
    })
})

let upusertablediv=document.querySelectorAll(".upusertablediv")
let downusertablediv=document.querySelectorAll(".downusertablediv")
let boxid
var boxs
document.querySelectorAll('.work-box').forEach(function(element){
    element.addEventListener("mousedown",function(){
        boxid=this.id//取得id
        down=false
        move=false
        boxs=document.querySelectorAll("#"+boxid)
        boxs.forEach(function(box){
            box.addEventListener("dragstart",dragstart)
        })
    })
})

upusertablediv.forEach(function(up){
    up.addEventListener("dragenter",dragenter)
    up.addEventListener("dragover",dragover)
    up.addEventListener("dragleave",dragleave)
    up.addEventListener("drop",updrop)
})

downusertablediv.forEach(function(down){
    down.addEventListener("dragenter",dragenter)
    down.addEventListener("dragover",dragover)
    down.addEventListener("dragleave",dragleave)
    down.addEventListener("drop",downdrop)
})

function dragstart(e){
    e.dataTransfer.setData("text",boxid)
}

function dragenter(e){
    e.preventDefault()
    e.target.classList.add("drag-over")
}

function dragover(e){
    e.preventDefault()
    e.target.classList.add("drag-over")
}

function dragleave(e){
    e.preventDefault()
    e.target.classList.remove("drag-over")
}

function updrop(e){
    e.target.classList.remove("drag-over")
    let id=e.dataTransfer.getData("text")
    let box=document.getElementById(id)
    e.target.appendChild(box)
    box.style.top="0px"
    box.style.left="10px"
    let boxheight=box.style.height
    let time=parseInt(boxheight)/30
    let divtarget=parseFloat(e.target.id)
    let starthr=Math.floor(divtarget)
    let startmin=((divtarget-starthr)*60).toFixed(0)
    if(starthr<10){
        starthr="0"+starthr
    }
    if(startmin<10){
        startmin="0"+startmin
    }
    let starttime=starthr+":"+startmin
    let endhr=parseInt(starthr)+parseInt(time)
    let decimalonly=time%1*10
    let endmin=parseInt(startmin)+((decimalonly/5)*30)
    if(endmin<10){
        endmin="0"+endmin
    }
    if(endmin==60){
        endmin="00"
        endhr=endhr+1
    }
    if(endhr<10){
        endhr="0"+endhr
    }
    let endtime=endhr+":"+endmin
    document.getElementById(boxid+"starttime").innerHTML=`開始時間: ${starttime}`
    document.getElementById(boxid+"endtime").innerHTML=`結束時間: ${endtime}`
}


function downdrop(e){
    e.preventDefault()
    e.target.classList.remove("drag-over")
    const id=e.dataTransfer.getData("text")
    const box=document.getElementById(id)
    e.target.appendChild(box)
    box.style.top="0px"
    box.style.left="10px"
    let height=box.style.height
    let time=parseInt(height)/30
    let divtarget=parseFloat(e.target.id)
    let starthr=Math.floor(divtarget)
    let startmin=((divtarget-starthr)*60).toFixed(0)
    if(starthr<10){
        starthr="0"+starthr
    }
    if(startmin<10){
        startmin="0"+startmin
    }
    let starttime=starthr+":"+startmin
    let endhr=parseInt(starthr)-parseInt(time)
    let decimalonly=time%1*10
    let endmin=parseInt(startmin)+((decimalonly/5)*30)
    if(endmin<10){
        endmin="0"+endmin
    }
    if(endmin==60){
        endmin="00"
    }
    if(endhr<10){
        endhr="0"+endhr
    }
    let endtime=endhr+":"+endmin
    document.getElementById(boxid+"starttime").innerHTML=`開始時間: ${endtime}`
    document.getElementById(boxid+"endtime").innerHTML=`結束時間: ${starttime}`
}