function input(id){
    if(id!="description"){
        if(weblsget("53regionalproduct"+id)!=null){
            docgetid(id).value=weblsget("53regionalproduct"+id)
        }
    }else{
        if(weblsget("53regionalproduct"+id)!=null){
            docgetid(id).innerHTML=weblsget("53regionalproduct"+id)
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
        localStorage.setItem("53regionalproductfile",reader.result)
    }
    reader.readAsDataURL(file)
}

docgetid("productsubmit").onclick=function(){
    console.log("ininini")
    if(regexpmatch(/^[0-9]+(\.[0-9]+)?$/,docgetid("cost").value)){
        weblsset("53regionalproductname",docgetid("name").value)
        weblsset("53regionalproductcost",docgetid("cost").value)
        weblsset("53regionalproductlink",docgetid("link").value)
        weblsset("53regionalproductdescription",docgetid("description").value)

        location.href="productpreview.html"
    }else{
        alert("價格只能是數字")
    }

}

docgetid("cancle").onclick=function(){
    location.href="main.html"
}

input("name")
input("cost")
input("link")
input("description")

docgetid("input").classList.add("selectbut")

startmacossection()