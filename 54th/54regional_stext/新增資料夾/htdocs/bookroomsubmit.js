document.getElementById("main").innerHTML=`
    ${document.getElementById("main").innerHTML}
    <input type="hidden" class="notext" name="roomcount" value="${localStorage.getItem("roomcount")}" readonly>
    <input type="hidden" class="notext" name="datecount" value="${localStorage.getItem("datecount")}" readonly>
    <input type="hidden" class="notext" name="startdate" value="${localStorage.getItem("startdate")}" readonly>
    <input type="hidden" class="notext" name="enddate" value="${localStorage.getItem("enddate")}" readonly>
    <input type="hidden" class="notext" name="roomno" value="${localStorage.getItem("bookroomlist")}" readonly>
    <input type="hidden" class="notext" name="price" value="${parseInt(localStorage.getItem("roomcount"))*parseInt(localStorage.getItem("datecount"))*5000}" readonly>
    <input type="hidden" class="notext" name="deposit" value="${parseInt(localStorage.getItem("roomcount"))*parseInt(localStorage.getItem("datecount"))*5000*0.3}" readonly>
`