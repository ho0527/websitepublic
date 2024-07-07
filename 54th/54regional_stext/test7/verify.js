let wordid=""

document.querySelectorAll("td").forEach(function(event){
    let alf="ABCDEFGH"
    let rand=Math.floor(Math.random()*8)
    let word=alf[rand]
    event.dataset.word=word
})

document.querySelectorAll("td").forEach(function(event){
    event.onclick=function(){
        event.innerHTML=event.dataset.word
        setTimeout(function(){
            if(wordid==""){
                wordid=event.id
            }else{
                if(document.getElementById(wordid).dataset.word==event.dataset.word){
                    alert("登入成功")
                    location.href="admincomment.php"
                }else{
                    alert("驗證失敗")
                    event.innerHTML=``
                    document.getElementById(wordid).innerHTML=``
                    wordid=""
                }
            }
        },100)
    }
})