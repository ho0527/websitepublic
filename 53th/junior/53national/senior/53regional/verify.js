let verifytd=document.querySelectorAll(".verifytd")

verifytd.forEach(function(e){
    e.style.backgroundColor="white"
    e.addEventListener("click",function(){
        if(e.style.backgroundColor=="white")
            e.style.backgroundColor="black"
        else
            e.style.backgroundColor="white"
    })
})

function pass(){
    alert("登入成功")
    location.href="main.php"
}

function check(){
    if(verifytd[0].style.backgroundColor=="black"&&verifytd[1].style.backgroundColor=="black"&&verifytd[2].style.backgroundColor=="black"&&verifytd[3].style.backgroundColor=="white"&&verifytd[4].style.backgroundColor=="white"&&verifytd[5].style.backgroundColor=="white"&&verifytd[6].style.backgroundColor=="white"&&verifytd[7].style.backgroundColor=="white"&&verifytd[8].style.backgroundColor=="white") pass()
    else if(verifytd[0].style.backgroundColor=="white"&&verifytd[1].style.backgroundColor=="white"&&verifytd[2].style.backgroundColor=="white"&&verifytd[3].style.backgroundColor=="black"&&verifytd[4].style.backgroundColor=="black"&&verifytd[5].style.backgroundColor=="black"&&verifytd[6].style.backgroundColor=="white"&&verifytd[7].style.backgroundColor=="white"&&verifytd[8].style.backgroundColor=="white") pass()
    else if(verifytd[0].style.backgroundColor=="white"&&verifytd[1].style.backgroundColor=="white"&&verifytd[2].style.backgroundColor=="white"&&verifytd[3].style.backgroundColor=="white"&&verifytd[4].style.backgroundColor=="white"&&verifytd[5].style.backgroundColor=="white"&&verifytd[6].style.backgroundColor=="black"&&verifytd[7].style.backgroundColor=="black"&&verifytd[8].style.backgroundColor=="black") pass()
    else if(verifytd[0].style.backgroundColor=="black"&&verifytd[1].style.backgroundColor=="white"&&verifytd[2].style.backgroundColor=="white"&&verifytd[3].style.backgroundColor=="black"&&verifytd[4].style.backgroundColor=="white"&&verifytd[5].style.backgroundColor=="white"&&verifytd[6].style.backgroundColor=="black"&&verifytd[7].style.backgroundColor=="white"&&verifytd[8].style.backgroundColor=="white") pass()
    else if(verifytd[0].style.backgroundColor=="white"&&verifytd[1].style.backgroundColor=="black"&&verifytd[2].style.backgroundColor=="white"&&verifytd[3].style.backgroundColor=="white"&&verifytd[4].style.backgroundColor=="black"&&verifytd[5].style.backgroundColor=="white"&&verifytd[6].style.backgroundColor=="white"&&verifytd[7].style.backgroundColor=="black"&&verifytd[8].style.backgroundColor=="white") pass()
    else if(verifytd[0].style.backgroundColor=="white"&&verifytd[1].style.backgroundColor=="white"&&verifytd[2].style.backgroundColor=="black"&&verifytd[3].style.backgroundColor=="white"&&verifytd[4].style.backgroundColor=="white"&&verifytd[5].style.backgroundColor=="black"&&verifytd[6].style.backgroundColor=="white"&&verifytd[7].style.backgroundColor=="white"&&verifytd[8].style.backgroundColor=="black") pass()
    else if(verifytd[0].style.backgroundColor=="black"&&verifytd[1].style.backgroundColor=="white"&&verifytd[2].style.backgroundColor=="white"&&verifytd[3].style.backgroundColor=="white"&&verifytd[4].style.backgroundColor=="black"&&verifytd[5].style.backgroundColor=="white"&&verifytd[6].style.backgroundColor=="white"&&verifytd[7].style.backgroundColor=="white"&&verifytd[8].style.backgroundColor=="black") pass()
    else if(verifytd[0].style.backgroundColor=="white"&&verifytd[1].style.backgroundColor=="white"&&verifytd[2].style.backgroundColor=="black"&&verifytd[3].style.backgroundColor=="white"&&verifytd[4].style.backgroundColor=="black"&&verifytd[5].style.backgroundColor=="white"&&verifytd[6].style.backgroundColor=="black"&&verifytd[7].style.backgroundColor=="white"&&verifytd[8].style.backgroundColor=="white") pass()
    else alert("驗證碼輸入錯誤")
}