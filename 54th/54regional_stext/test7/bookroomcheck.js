let book=JSON.parse(localStorage.getItem("book"))

if(book!=null&&book["endday"]!=null){
    let totalprice=book["totalprice"]
    document.getElementById("totalday").innerHTML=book["totalday"]
    document.getElementById("startday").innerHTML=book["startday"]
    document.getElementById("endday").innerHTML=book["endday"]
    document.getElementById("room").innerHTML="Room"+book["room"]
    if(6<=book["totalday"]){
        totalprice=totalprice*0.65
    }else if(5<=book["totalday"]){
        totalprice=totalprice*0.8
    }else if(3<=book["totalday"]){
        totalprice=totalprice*0.9
    }
    document.getElementById("totalprice").innerHTML=totalprice
    document.getElementById("desp").innerHTML=totalprice*0.3
}else{
    alert("請先選擇日期")
    location.href=localStorage.getItem("location")
}