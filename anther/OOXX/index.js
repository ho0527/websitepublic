let td1=document.getElementById("td1")
let td2=document.getElementById("td2")
let td3=document.getElementById("td3")
let td4=document.getElementById("td4")
let td5=document.getElementById("td5")
let td6=document.getElementById("td6")
let td7=document.getElementById("td7")
let td8=document.getElementById("td8")
let td9=document.getElementById("td9")
let tds=document.querySelectorAll(".td")
let mask=document.getElementById("maskdiv")
let player="X"

tds.forEach(function(td){
    td.addEventListener("click", function() {
        tdclick(td.id)
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
                    <h1>結果:O獲勝</h1>
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
                    <h1>結果:X獲勝</h1>
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
    document.getElementById("submit").onclick=function(){
        location.reload()
    }
}

function check(id){
    if((td1.innerHTML==td2.innerHTML)&&(td2.innerHTML==td3.innerHTML)&&td1.innerHTML!=""&&td2.innerHTML!=""&&td3.innerHTML!=""){
        console.log("1");
        win(id)
    }else if((td4.innerHTML==td5.innerHTML)&&(td5.innerHTML==td6.innerHTML)&&td4.innerHTML!=""&&td5.innerHTML!=""&&td6.innerHTML!=""){
        console.log("2");
        win(id)
    }else if((td7.innerHTML==td8.innerHTML)&&(td8.innerHTML==td9.innerHTML)&&td7.innerHTML!=""&&td8.innerHTML!=""&&td9.innerHTML!=""){
        console.log("3");
        win(id)
    }else if((td1.innerHTML==td4.innerHTML)&&(td4.innerHTML==td7.innerHTML)&&td1.innerHTML!=""&&td4.innerHTML!=""&&td7.innerHTML!=""){
        console.log("4");
        win(id)
    }else if((td2.innerHTML==td5.innerHTML)&&(td5.innerHTML==td8.innerHTML)&&td2.innerHTML!=""&&td5.innerHTML!=""&&td8.innerHTML!=""){
        console.log("5");
        win(id)
    }else if((td3.innerHTML==td6.innerHTML)&&(td6.innerHTML==td9.innerHTML)&&td3.innerHTML!=""&&td6.innerHTML!=""&&td9.innerHTML!=""){
        console.log("6");
        win(id)
    }else if((td1.innerHTML==td5.innerHTML)&&(td5.innerHTML==td9.innerHTML)&&td1.innerHTML!=""&&td5.innerHTML!=""&&td9.innerHTML!=""){
        console.log("7");
        win(id)
    }else if((td3.innerHTML==td5.innerHTML)&&(td5.innerHTML==td7.innerHTML)&&td3.innerHTML!=""&&td5.innerHTML!=""&&td7.innerHTML!=""){
        console.log("8");
        win(id)
    }else if(td1.innerHTML!=""&&td2.innerHTML!=""&&td3.innerHTML!=""&&td4.innerHTML!=""&&td5.innerHTML!=""&&td6.innerHTML!=""&&td7.innerHTML!=""&&td8.innerHTML!=""&&td9.innerHTML!=""){
        win("same")
    }else{
        console.log("countu");
    }
}

function tdclick(id){
    console.log(id);
    if(player=="X"){
        if(document.getElementById(id).innerHTML==""){
            document.getElementById(id).innerHTML=`X`
            document.getElementById(id).classList.add("cover")
            player="O"
        }else{
            console.log("FUCK 幹嘛按這格");
        }
    }else{
        if(document.getElementById(id).innerHTML==""){
            document.getElementById(id).innerHTML=`O`
            document.getElementById(id).classList.add("cover")
            player="X"
        }else{
            console.log("FUCK 幹嘛按這格");
        }
    }
    check(player)
}