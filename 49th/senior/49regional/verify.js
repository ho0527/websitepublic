let verifytd=document.querySelectorAll(".verifytd")
let show=false
let click=0
let tdword1
let tdword2
let tdid1
let tdid2

function showall(){
    if(!show){
        verifytd.forEach(function(td){
            if(td.style.backgroundColor=="white"){
                td.style.color="black"
            }
        })
        document.getElementById("open").value="全部蓋牌"
        show=true
    }else{
        verifytd.forEach(function(td){
            if(td.style.backgroundColor=="white"){
                td.style.color="white"
            }
        })
        document.getElementById("open").value="全部翻牌"
        show=false
    }
    clear()
}

function clear(){
    tdid1=""
    tdid2=""
    tdword1=""
    tdword2=""
    click=0
}

verifytd.forEach(function(td){
    td.style.backgroundColor="white"
    td.style.color="white"
    clear()
    td.addEventListener("click",function(){
        td.style.color="black"
        if(!show){
            if(td.style.backgroundColor=="white"){
                if(tdid1!=this.id){
                    click=click+1
                    if(click==2){
                        tdword2=this.innerHTML
                        tdid2=this.id
                        setTimeout(function(){
                            if(tdword1==tdword2){
                                verifytd[tdid1].style.backgroundColor="yellow"
                                verifytd[tdid1].style.color="black"
                                verifytd[tdid2].style.backgroundColor="yellow"
                                verifytd[tdid2].style.color="black"
                                if(tdword1=="E"&&tdword2=="E"){
                                    alert("登入成功")
                                    location.href="main.html"
                                }
                            }else{
                                verifytd[tdid1].style.backgroundColor="white"
                                verifytd[tdid1].style.color="white"
                                verifytd[tdid2].style.backgroundColor="white"
                                verifytd[tdid2].style.color="white"
                            }
                            clear()
                        },200)
                    }else{
                        tdword1=this.innerHTML
                        tdid1=this.id
                    }
                }else{
                    alert("禁止點選同一格")
                }
            }else{
                alert("禁止點選已翻牌的格子")
            }
        }else{
            alert("請先蓋牌")
        }
    })
})