let item=document.querySelectorAll(".item")
let indicator=document.querySelectorAll(".indicator")
let prev=document.getElementById("prev")
let next=document.getElementById("next")
let count=0

function clearall(){
    for(let i=0;i<item.length;i=i+1){
        item[i].style.display="none"
    }
}

function changepicture(){
    clearall()
    item[count].style.display="block"
    count=(count+1)%item.length
}

changepicture()
let carousel=setInterval(changepicture,2500)

indicator.forEach(function(event){
    event.onclick=function(){
        clearInterval(carousel)
        clearall()
        count=event.value-1
        item[count].style.display="block"
        carousel=setInterval(changepicture,2500)
    }
})

document.getElementById("prev").onclick=function(){
    clearInterval(carousel)
    clearall()
    count=(count-1+item.length)%item.length
    item[count].style.display="block"
    carousel=setInterval(changepicture,2500)
}

document.getElementById("next").onclick=function(){
    clearInterval(carousel)
    changepicture()
    carousel=setInterval(changepicture,2500)
}