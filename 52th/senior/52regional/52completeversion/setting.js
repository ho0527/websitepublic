let del=document.getElementById("del-acc")
let form=document.getElementById("del-acc-form")
let cancle=document.getElementById("cancle")
let comform=document.getElementById("comform")

del.style.display="inline"
form.style.display="none"
cancle.style.display="none"
comform.style.display="none"

del.onclick=function(){
    del.style.display="none"
    form.style.display="inline"
    cancle.style.display="inline"
    comform.style.display="inline"
}

cancle.onclick=function(){
    document.location.href="setting.php"
}