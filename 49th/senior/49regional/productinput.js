function input(id){
    if(id!="description"){
        if(weblsget("49regionalproduct"+id)!=null){
            docgetid(id).value=weblsget("49regionalproduct"+id)
        }
    }else{
        if(weblsget("49regionalproduct"+id)!=null){
            docgetid(id).innerHTML=weblsget("49regionalproduct"+id)
        }
    }
}

docgetid("picture").onclick=function(){
    docgetid("file").click()
}

docgetid("file").onchange=function(event){
    let file=event.target.files[0]
    let reader=new FileReader()
    reader.onload=function(){
        localStorage.setItem("49regionalproductfile",reader.result)
    }
    reader.readAsDataURL(file)
}

docgetid("submit").onclick=function(){
    weblsset("49regionalproductname",docgetid("name").value)
    weblsset("49regionalproductdate",docgetid("date").value)
    weblsset("49regionalproductlink",docgetid("link").value)
    weblsset("49regionalproductdescription",docgetid("description").value)
    weblsset("49regionalproductsignupbutton",docgetid("signupbutton").value)

    if(!docgetid("visibility").checked){
        weblsset("49regionalproductvisibility","false")
    }

    location.href="productpreview.html"
}

docgetid("cancle").onclick=function(){
    location.href="main.php"
}


input("name")
input("date")
input("link")
input("signupbutton")
input("description")


if(weblsget("49regionalproductvisibility")=="false"){
    docgetid("visibility").checked=false
}