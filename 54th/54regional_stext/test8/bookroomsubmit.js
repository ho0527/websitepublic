let book=JSON.parse(localStorage.getItem("book"))

if(book&&book["booked"]){
    document.getElementById("main").innerHTML+=`
        <input type="hidden" name="startday" value="${book["startday"]}">
        <input type="hidden" name="endday" value="${book["endday"]}">
        <input type="hidden" name="room" value="${book["room"]}">
    `
}else{
    alert("請先選擇日期")
    location.href=localStorage.getItem("location")
}