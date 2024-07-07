let item=document.querySelectorAll(".item")
let indicator=document.querySelectorAll(".indicator")
let prev=document.getElementById("prev")
let next=document.getElementById("next")
let itemcount=0
let count=300

function clearall(){
    for(let i=0;i<item.length;i=i+1){
        item[i].style.display="none"
    }
}

function changepicture(){
    if(count>=200){
        clearall()
        item[itemcount].style.display="block"
        itemcount=(itemcount+1)%item.length
    }
}

let counter=setInterval(function(){
    count=count+1
},10)

changepicture()
let carousel=setInterval(changepicture,2500)

indicator.forEach(function(event){
    event.onclick=function(){
        if(count>300){
            clearInterval(carousel)
            clearall()
            itemcount=event.value-1
            item[itemcount].style.display="block"
            setTimeout(function(){
                count=0
                setInterval(changepicture,2500)
            },250)
        }else{
            count=0
        }
    }
})

document.getElementById("prev").onclick=function(){
    if(count>300){
        clearInterval(carousel)
        clearall()
        itemcount=(itemcount-1+item.length)%item.length
        item[itemcount].style.display="block"
        setTimeout(function(){
            count=0
            setInterval(changepicture,2500)
        },250)
    }else{
        count=0
    }
}

document.getElementById("next").onclick=function(){
    if(count>300){
        clearInterval(carousel)
        changepicture()
        setTimeout(function(){
            count=0
            setInterval(changepicture,2500)
        },250)
    }else{
        count=0
    }
}