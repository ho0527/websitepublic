let coffeetable=document.querySelectorAll(".coffeetable")
let val

coffeetable.forEach(function(e){
    e.style.backgroundColor=""
    e.addEventListener("click",function(){
        coffeetable.forEach(function(e){
            e.style.backgroundColor=""
        })
        e.style.backgroundColor="rgb(255, 255, 159)"
        val=e.id
    })
})

function check(href){
    if(val==undefined){
        location.href=href+"?val=no"
    }else{
        location.href=href+"?val="+val
    }
}

function sub(){
    document.getElementById("form").submit.click()
}

document.querySelectorAll(".select").forEach(function(e){
    e.addEventListener("change",function(){
        let id=e.id
        document.querySelectorAll(".coffee")[id-1].innerHTML=`
            ${ e.value }
        `
    })
})