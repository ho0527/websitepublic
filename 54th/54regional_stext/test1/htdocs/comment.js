document.querySelectorAll(".edit").forEach(function(event){
    event.onclick=function(){
        if(document.getElementById(event.dataset.id).dataset.code==document.getElementById(event.dataset.id).value){
            location.href="editcomment.php?editcomment="+event.dataset.id
        }else{
            alert("輸入錯誤")
        }
    }
})

document.querySelectorAll(".delete").forEach(function(event){
    event.onclick=function(){
        if(document.getElementById(event.dataset.id).dataset.code==document.getElementById(event.dataset.id).value){
            if(confirm("confirm?")){
                location.href="api.php?deletecomment="+event.dataset.id
            }
        }else{
            alert("輸入錯誤")
        }
    }
})