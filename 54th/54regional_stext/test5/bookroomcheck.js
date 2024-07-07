let book=JSON.parse(localStorage.getItem("book"))

if(book!=null&&book["endday"]!=null){
    document.getElementById("totaldate").innerHTML=book["totaldate"]
    document.getElementById("startday").innerHTML=book["startday"]
    document.getElementById("endday").innerHTML=book["endday"]
    document.getElementById("room").innerHTML=book["room"]
    document.getElementById("price").innerHTML=book["price"]
    document.getElementById("decp").innerHTML=book["price"]*0.3
}else{
    alert("請先選擇日期")
    location.href=localStorage.getItem('booklocation')
}