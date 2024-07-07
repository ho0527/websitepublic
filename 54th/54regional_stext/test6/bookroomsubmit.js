let book=JSON.parse(localStorage.getItem("book"))

if(book!=null&&book["lastdate"]!=null){
    document.getElementById("main").innerHTML+=`
        <input type="hidden" name="name" value="${book["totaldate"]}">
        <input type="hidden" name="firstday" value="${book["firstday"]}">
        <input type="hidden" name="lastday" value="${book["lastday"]}">
        <input type="hidden" name="room" value="${book["room"]}">
        <input type="hidden" name="totalprice" value="${book["totalprice"]}">
    `
}else{
    alert("請先選擇日期")
    if(localStorage.getItem("location")){
        location.href=localStorage.getItem("location")
    }else{
        location.href="bookroom.php"
    }
}