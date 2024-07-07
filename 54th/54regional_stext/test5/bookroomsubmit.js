let book=JSON.parse(localStorage.getItem("book"))

if(book!=null&&book["endday"]!=null){
    document.getElementById("totaldate").value=book["totaldate"]
    document.getElementById("startday").value=book["startday"]
    document.getElementById("endday").value=book["endday"]
    document.getElementById("room").value=book["room"]
    document.getElementById("price").value=book["price"]
}else{
    alert("請先選擇日期")
    location.href=localStorage.getItem('booklocation')
}