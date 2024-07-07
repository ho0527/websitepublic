document.querySelectorAll(".editbutton").forEach(function(event){
    event.onclick=function(){
        if(document.getElementById(event.dataset.id).value==event.dataset.no){
            location.href="editcomment.php?id="+event.dataset.id
        }else{
            alert("序號錯誤")
        }
    }
})

document.querySelectorAll(".deletebutton").forEach(function(event){
    event.onclick=function(){
        if(document.getElementById(event.dataset.id).value==event.dataset.no){
            if(confirm("confirm?")){
                location.href="api.php?deletecomment="+event.dataset.id
            }
        }else{
            alert("序號錯誤")
        }
    }
})

if(document.getElementById("file")){
    document.getElementById("file").onchange=function(event){
        let file=event.target.files[0]
        let filereader=new FileReader()
        filereader.onload=function(){
            document.getElementById("image").value=filereader.result
        }
        filereader.readAsDataURL(file)
    }
}