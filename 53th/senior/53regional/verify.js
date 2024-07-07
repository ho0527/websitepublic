let data=["水平線","垂直線","左上右下斜線","右上左下斜線"]
let count=Math.floor(Math.random()*4)
let clickcount=0

docgetid("context").innerHTML=data[count]
docgetall(".td").forEach(function(event){
    event.style.backgroundColor="white"
    event.onclick=function(){
        if(event.style.backgroundColor==""||event.style.backgroundColor=="white"){
            event.style.backgroundColor="black"
            clickcount=clickcount+1
        }else{
            event.style.backgroundColor="white"
            clickcount=clickcount-1
        }
    }
})

docgetid("change").onclick=function(){
    if(count==3){ count=-1 }
    count=count+1
    docgetid("context").innerHTML=data[count]
}

function pass(){
    alert("登入成功")
    location.href="main.html"
}

function check(){
    let td=docgetall(".td")
    if(clickcount==3){
        if(count==0){
            if(td[0].style.backgroundColor=="black"&&td[1].style.backgroundColor=="black"&&td[2].style.backgroundColor=="black"){
                pass()
            }else if(td[3].style.backgroundColor=="black"&&td[4].style.backgroundColor=="black"&&td[5].style.backgroundColor=="black"){
                pass()
            }else if(td[6].style.backgroundColor=="black"&&td[7].style.backgroundColor=="black"&&td[8].style.backgroundColor=="black"){
                pass()
            }else{ alert("驗證碼輸入錯誤") }
        }else if(count==1){
            if(td[0].style.backgroundColor=="black"&&td[3].style.backgroundColor=="black"&&td[6].style.backgroundColor=="black"){
                pass()
            }else if(td[1].style.backgroundColor=="black"&&td[4].style.backgroundColor=="black"&&td[7].style.backgroundColor=="black"){
                pass()
            }else if(td[2].style.backgroundColor=="black"&&td[5].style.backgroundColor=="black"&&td[8].style.backgroundColor=="black"){
                pass()
            }else{ alert("驗證碼輸入錯誤") }
        }else if(count==2&&td[0].style.backgroundColor=="black"&&td[4].style.backgroundColor=="black"&&td[8].style.backgroundColor=="black"){
            pass()
        }else if(count==3&&td[2].style.backgroundColor=="black"&&td[4].style.backgroundColor=="black"&&td[6].style.backgroundColor=="black"){
            pass()
        }else{
            alert("驗證碼輸入錯誤")
        }
    }else{
        alert("驗證碼輸入錯誤")
    }
}