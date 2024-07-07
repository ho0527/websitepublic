let list=["水平線","垂直線","左上右下斜線","右上左下斜線"]
let count=0
let click=0
document.getElementById("show").innerHTML=list[count]

document.querySelectorAll(".verifytd").forEach(function(event){
    event.style.backgroundColor="white"
    event.onclick=function(){
        if(event.style.backgroundColor=="white"){
            event.style.backgroundColor="black"
            click=click+1
        }else{
            event.style.backgroundColor="white"
            click=click-1
        }
    }
})

document.getElementById("swch").onclick=function(){
    if(count==3){ count=-1 }
    count=count+1
    document.getElementById("show").innerHTML=list[count]
}

document.getElementById("submit").onclick=function(){
    let td=document.querySelectorAll(".verifytd")
    if(click==3){
        if(count==0){
            if((td[0].style.backgroundColor=="black"&&td[1].style.backgroundColor=="black"&&td[2].style.backgroundColor=="black")||
            (td[3].style.backgroundColor=="black"&&td[4].style.backgroundColor=="black"&&td[5].style.backgroundColor=="black")||
            (td[6].style.backgroundColor=="black"&&td[7].style.backgroundColor=="black"&&td[8].style.backgroundColor=="black")){
                alert("登入成功")
                location.href="main.php"
            }else{
                alert("驗證碼錯誤")
            }
        }else if(count==1){
            if((td[0].style.backgroundColor=="black"&&td[3].style.backgroundColor=="black"&&td[6].style.backgroundColor=="black")||
            (td[1].style.backgroundColor=="black"&&td[4].style.backgroundColor=="black"&&td[7].style.backgroundColor=="black")||
            (td[3].style.backgroundColor=="black"&&td[5].style.backgroundColor=="black"&&td[8].style.backgroundColor=="black")){
                alert("登入成功")
                location.href="main.php"
            }else{
                alert("驗證碼錯誤")
            }
        }else if(count==2&&(td[0].style.backgroundColor=="black"&&td[4].style.backgroundColor=="black"&&td[8].style.backgroundColor=="black")){
                alert("登入成功")
                location.href="main.php"
        }else if(count==3&&(td[2].style.backgroundColor=="black"&&td[4].style.backgroundColor=="black"&&td[6].style.backgroundColor=="black")){
            alert("登入成功")
            location.href="main.php"
        }else{
            alert("驗證碼錯誤")
        }
    }else{
        alert("驗證碼錯誤")
    }
}