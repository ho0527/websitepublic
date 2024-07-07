let book=JSON.parse(localStorage.getItem("book"))

if(book!=null&&book["lastdate"]!=null){
    document.getElementById("totaldate").innerHTML=book["totaldate"]
    document.getElementById("firstday").innerHTML=book["firstday"]
    document.getElementById("lastday").innerHTML=book["lastday"]
    document.getElementById("room").innerHTML="Room"+book["room"]
    document.getElementById("totalprice").innerHTML=book["totalprice"]
    document.getElementById("descipt").innerHTML=book["totalprice"]*0.3
}else{
    alert("請先選擇日期")
    if(localStorage.getItem("location")){
        location.href=localStorage.getItem("location")
    }else{
        location.href="bookroom.php"
    }
}