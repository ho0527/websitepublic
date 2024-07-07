let read=document.getElementById("readmoreless")
let text=document.getElementById("readmore")

text.style.display="none"

read.onclick=function(){
    if(text.style.display=="none"){
        text.style.display="inline"
        read.value="閱讀更少(read less)"
    }else{
        text.style.display="none"
        read.value="閱讀更多(Read More)"
    }
}
