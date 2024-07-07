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

if(document.getElementById("file")){
    document.getElementById("file").onchange=function(event){
        let file=event.target.files[0]
        let filereader=new FileReader()
    
        filereader.onload=function(){
            document.getElementById("filetext").value=filereader.result
        }
    
        filereader.readAsDataURL(file)
    }
}