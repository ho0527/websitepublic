let check=0
let wordid=""
let wordlist=[]

document.querySelectorAll(".verify div").forEach(function(event){
    event.onclick=function(){
        if(wordid!=event.id&&!wordlist.includes(parseInt(event.id))){
            event.innerHTML=event.dataset.word
            setTimeout(function(){
                if(wordid==""){
                    wordid=event.id
                }else{
                    if(document.getElementById(wordid).dataset.word==event.dataset.word){
                        wordlist.push(parseInt(wordid))
                        wordlist.push(parseInt(event.id))
                        check=check+1
                        wordid=""
                    }else{
                        event.innerHTML=``
                        document.getElementById(wordid).innerHTML=``
                        wordid=""
                    }
                }
                if(check==4){
                    alert("驗證成功")
                    location.href="admincomment.php"
                }
            },100)
        }
    }
})