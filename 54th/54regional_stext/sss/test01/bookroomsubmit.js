let book=JSON.parse(localStorage.getItem("book"))

document.getElementById("main").innerHTML+=`
    <div>
        <input type="hidden" name="totaldate" value="${book["totaldate"]}">
        <input type="hidden" name="startday" value="${book["startday"]}">
        <input type="hidden" name="endday" value="${book["endday"]}">
        <input type="hidden" name="room" value="${book["bookroom"].join(",")}">
        <input type="hidden" name="price" value="${book["totalprice"]}">
    </div>
`