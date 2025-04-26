let td1=document.getElementById("td1")
let td2=document.getElementById("td2")
let td3=document.getElementById("td3")
let td4=document.getElementById("td4")
let td5=document.getElementById("td5")
let td6=document.getElementById("td6")
let td7=document.getElementById("td7")
let td8=document.getElementById("td8")
let td9=document.getElementById("td9")
let td=document.querySelectorAll(".td")
let mask=document.getElementById("maskdiv")
let player="X"
let data={
    1:"",
    2:"",
    3:"",
    4:"",
    5:"",
    6:"",
    7:"",
    8:"",
    9:"",
}

let div=document.createElement("div")
document.getElementById("main").appendChild(div)

td.forEach(function(event){
    event.addEventListener("click",function(){
        let id=this.id
        if(document.getElementById(id).innerHTML==""){
            document.getElementById(id).innerHTML=`X`
            document.getElementById(id).classList.add("cover")
            player="O"
        }else{
            console.log("FUCK 幹嘛按這格")
        }
        data[id[id.length-1]]="X"
        fetch("apinowupdatex.php",{
            method:"POST",
            body:JSON.stringify({ id: id[id.length-1] }),
            headers:{ "Content-Type":"application/json" },
        }).then(function(response){ return response.text() })
        .catch(function(event){ console.error("Error:",event) })
        .then(function(){ console.log("sussess:)") })
        if(player=="O"){
            if(!check("X")){
                div.classList.add("mask")
                let random
                setTimeout(function(){
                    for(let i=0;i<9;i=i+1){
                        random=parseInt(Math.random()*9)
                        if(td[random].innerHTML==""){
                            document.getElementById("td"+(random+1)).innerHTML=`O`
                            document.getElementById("td"+(random+1)).classList.add("cover")
                            player="X"
                            break
                        }else{
                            console.log("pass")
                        }
                    }
                    div.classList.remove("mask")
                    data[(random+1)]="O"
                    fetch("apinowupdateo.php",{
                        method:"POST",
                        body:JSON.stringify({ id: (random+1) }),
                        headers:{ "Content-Type":"application/json" },
                    }).then(function(response){ return response.text() })
                    .catch(function(event){ console.error("Error:",event) })
                    .then(function(){ console.log("sussess:)") })
                    check("O")
                },300)
            }
        }
    })
})

function win(id){
    if(id=="X"){
        mask.innerHTML=`
            <div class="div">
                <div class="mask"></div>
                <div class="body">
                    <h2 class="title">遊戲結束</h2>
                    <hr>
                    <h1>結果:X獲勝</h1>
                    <div class="buttonlist">
                        <button id="submit" name="enter" class="submit button">重新開始</button>
                    </div>
                </div>
            </div>
        `
    }else if(id=="O"){
        mask.innerHTML=`
            <div class="div">
                <div class="mask"></div>
                <div class="body">
                    <h2 class="title">遊戲結束</h2>
                    <hr>
                    <h1>結果:O獲勝</h1>
                    <div class="buttonlist">
                        <button id="submit" name="enter" class="submit button">重新開始</button>
                    </div>
                </div>
            </div>
        `
    }else{
        mask.innerHTML=`
            <div class="div">
                <div class="mask"></div>
                <div class="body">
                    <h2 class="title">遊戲結束</h2>
                    <hr>
                    <h1>結果:平手</h1>
                    <div class="buttonlist">
                        <button id="submit" name="enter" class="submit button">重新開始</button>
                    </div>
                </div>
            </div>
        `
    }
    fetch("apiwin.php",{
        method:"POST",
        body:JSON.stringify({ id: id }),
        headers:{ "Content-Type":"application/json" },
    }).then(function(response){ return response.text() })
    .catch(function(event){ console.error("Error:",event) })
    .then(function(){ console.log(JSON.stringify({ id: id })) })
    document.getElementById("submit").onclick=function(){
        reload()
    }
}

function check(id){
    if((td1.innerHTML==td2.innerHTML)&&(td2.innerHTML==td3.innerHTML)&&td1.innerHTML!=""&&td2.innerHTML!=""&&td3.innerHTML!=""){
        win(id)
        return true
    }else if((td4.innerHTML==td5.innerHTML)&&(td5.innerHTML==td6.innerHTML)&&td4.innerHTML!=""&&td5.innerHTML!=""&&td6.innerHTML!=""){
        win(id)
        return true
    }else if((td7.innerHTML==td8.innerHTML)&&(td8.innerHTML==td9.innerHTML)&&td7.innerHTML!=""&&td8.innerHTML!=""&&td9.innerHTML!=""){
        win(id)
        return true
    }else if((td1.innerHTML==td4.innerHTML)&&(td4.innerHTML==td7.innerHTML)&&td1.innerHTML!=""&&td4.innerHTML!=""&&td7.innerHTML!=""){
        win(id)
        return true
    }else if((td2.innerHTML==td5.innerHTML)&&(td5.innerHTML==td8.innerHTML)&&td2.innerHTML!=""&&td5.innerHTML!=""&&td8.innerHTML!=""){
        win(id)
        return true
    }else if((td3.innerHTML==td6.innerHTML)&&(td6.innerHTML==td9.innerHTML)&&td3.innerHTML!=""&&td6.innerHTML!=""&&td9.innerHTML!=""){
        win(id)
        return true
    }else if((td1.innerHTML==td5.innerHTML)&&(td5.innerHTML==td9.innerHTML)&&td1.innerHTML!=""&&td5.innerHTML!=""&&td9.innerHTML!=""){
        win(id)
        return true
    }else if((td3.innerHTML==td5.innerHTML)&&(td5.innerHTML==td7.innerHTML)&&td3.innerHTML!=""&&td5.innerHTML!=""&&td7.innerHTML!=""){
        win(id)
        return true
    }else if(td1.innerHTML!=""&&td2.innerHTML!=""&&td3.innerHTML!=""&&td4.innerHTML!=""&&td5.innerHTML!=""&&td6.innerHTML!=""&&td7.innerHTML!=""&&td8.innerHTML!=""&&td9.innerHTML!=""){
        win("same")
        return true
    }else{
        return false
    }
}

function reload(){
    let ajax=new XMLHttpRequest()

    ajax.onreadystatechange=function(){
        if(ajax.readyState==4){
            if(ajax.status==200){
                let data=ajax.responseText
                console.log(data)
            }else{
                console.log("[ERROR] ajax error")
            }
        }
    }

    ajax.open("GET","apinowclear.php",true)
    ajax.send()

    fetch("apilog.php",{
        method:"POST",
        body:JSON.stringify({ data }),
        headers:{ "Content-Type":"application/json" },
    }).then(function(response){ return response.text() })
    .catch(function(event){ console.error("Error:",event) })
    .then(function(){ console.log("sussess:)") })

    setTimeout(function(){
        location.reload()
    },300)
}

let ajax=new XMLHttpRequest()

ajax.onreadystatechange=function(){
    if(ajax.readyState==4){
        if(ajax.status==200){
            let data=JSON.parse(ajax.responseText)[0]
            for(let i=1;i<=9;i=i+1){
                document.getElementById("td"+i).innerHTML=data[i]
            }
            console.log(data)
        }else{
            console.log("[ERROR] ajax error")
        }
    }
}

ajax.open("GET","apinow.php",true)
ajax.send()