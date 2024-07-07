let index=0
let count=0
let array=["水平線","垂直線","左上右下斜線","右上左下斜線"]

document.querySelectorAll(".verifytd").forEach(function(event){
    event.onclick=function(){
        if(event.style.backgroundColor=="black"){
            event.style.backgroundColor="white"
            count=count-1
        }else{
            event.style.backgroundColor="black"
            count=count+1
        }
    }
})

document.getElementById("next").onclick=function(){
    index=index+1
    if(index>=4){
        index=0
    }
    document.getElementById("vtext").innerHTML=array[index]
}

document.getElementById("submit").onclick=function(){
    let td=document.querySelectorAll(".verifytd")
    if(count==3){
        if(index==0){
            if(td[0].style.backgroundColor=="black"&&td[1].style.backgroundColor=="black"&&td[2].style.backgroundColor=="black"){
                alert("登入成功")
                location.href="main.php"
            }else if(td[3].style.backgroundColor=="black"&&td[4].style.backgroundColor=="black"&&td[5].style.backgroundColor=="black"){
                alert("登入成功")
                location.href="main.php"
            }else if(td[6].style.backgroundColor=="black"&&td[7].style.backgroundColor=="black"&&td[8].style.backgroundColor=="black"){
                alert("登入成功")
                location.href="main.php"
            }else{
                alert("驗證碼錯誤")
            }
        }else if(index==1){
            if(td[0].style.backgroundColor=="black"&&td[3].style.backgroundColor=="black"&&td[6].style.backgroundColor=="black"){
                alert("登入成功")
                location.href="main.php"
            }else if(td[1].style.backgroundColor=="black"&&td[4].style.backgroundColor=="black"&&td[7].style.backgroundColor=="black"){
                alert("登入成功")
                location.href="main.php"
            }else if(td[2].style.backgroundColor=="black"&&td[5].style.backgroundColor=="black"&&td[8].style.backgroundColor=="black"){
                alert("登入成功")
                location.href="main.php"
            }else{
                alert("驗證碼錯誤")
            }
        }else if(index==2){
            if(td[0].style.backgroundColor=="black"&&td[4].style.backgroundColor=="black"&&td[8].style.backgroundColor=="black"){
                alert("登入成功")
                location.href="main.php"
            }else{
                alert("驗證碼錯誤")
            }
        }else if(index==3){
            if(td[2].style.backgroundColor=="black"&&td[4].style.backgroundColor=="black"&&td[6].style.backgroundColor=="black"){
                alert("登入成功")
                location.href="main.php"
            }else{
                alert("驗證碼錯誤")
            }
        }else{
            alert("驗證碼錯誤")
            location.reload()
        }
    }else{
        alert("驗證碼錯誤")
    }
}

document.getElementById("vtext").innerHTML=array[index]