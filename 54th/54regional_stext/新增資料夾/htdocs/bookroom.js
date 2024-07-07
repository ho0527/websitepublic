let roomcount = 1
let datecount = 0
let totalprice = 0
let startdate = null
let enddate = null
let canbookroom = []
let bookroomlist = []


function changeMonth(url) {
    if(startdate){
        localStorage.setItem("booking", JSON.stringify({
            roomcount,
            datecount,
            totalprice,
            startdate,
            enddate,
            canbookroom,
            bookroomlist,
        }))
    }
    location.href = url;
}

if (localStorage.getItem("booking")) {
    let oldData = localStorage.getItem("booking");
    oldData = JSON.parse(oldData);
    roomcount = oldData.roomcount;
    datecount = oldData.datecount;
    totalprice = oldData.totalprice;
    startdate = oldData.startdate;
    enddate = oldData.enddate;
    canbookroom = oldData.canbookroom;
    bookroomlist = oldData.bookroomlist;
    document.getElementById("startdate").value = localStorage.getItem("startdate") || ""
    document.getElementById("enddate").value = localStorage.getItem("enddate") || ""
    document.getElementById("datecount").value = datecount || "0"
    for (let i = startdate; i <= enddate; i = i + 1) {
        if(document.getElementById("date_" + i)){
            document.getElementById("date_" + i).style.backgroundColor = "orange"
            document.getElementById("date_" + i).style.color = "white"
        }
    }
}

function randomroom() {
    if (startdate != null) {
        let canbookroomtemp = [...canbookroom]

        bookroomlist = []
        for (let i = 0; i < parseInt(roomcount); i = i + 1) {
            let random = parseInt(Math.random() * canbookroomtemp.length)

            bookroomlist.push(canbookroomtemp[random])
            canbookroomtemp.splice(random, 1)
        }
        bookroomlist.sort()

        document.getElementById("roomno").value = "Room" + bookroomlist.join(",Room")
    } else {
        alert("請選擇日期")
    }
}

document.querySelectorAll(".calendardate").forEach(function (event) {
    event.onclick = async function () {
        let day = event.dataset.day
        let date = parseInt(event.dataset.date)

        totalprice = 0
        roomcount = document.getElementById("roomcount").value
        if (startdate == null) {
            canbookroom = event.dataset.leftroom.split(",")

            if (roomcount <= canbookroom.length) {
                startdate = date
                localStorage.setItem("startdate", day)
                document.getElementById("startdate").value = day
                randomroom()
            } else {
                canbookroom = []
                alert("房間數量不足")
                return
            }
        } else if (enddate == startdate && startdate < date) {
            let res = await fetch("api.php?leftroom=&startdate=" + document.getElementById("startdate").value + "&enddate=" + day)
            canbookroom = await res.json()
            if (roomcount <= canbookroom.length) {
                // document.getElementById("selectroom").disabled="true"
                randomroom()
            } else {
                alert("房間數量不足")
                return
            }
        } else {
            alert("請先取消選擇日期在選擇")
            return
        }

        document.getElementById("enddate").value = day
        
        enddate = date
        datecount = (new Date(document.getElementById("enddate").value).getTime() - new Date(document.getElementById("startdate").value).getTime()) / (24 * 60 * 60 * 1000) + 1
        localStorage.setItem("enddate", day)

        document.getElementById("datecount").value = datecount
        document.getElementById("roomcount").disabled = true

        for (let i = startdate; i <= enddate; i = i + 1) {
            totalprice = totalprice + parseInt(event.dataset.cost)
            if(document.getElementById("date_" + i)){
                document.getElementById("date_" + i).style.backgroundColor = "orange"
                document.getElementById("date_" + i).style.color = "white"
            }
        }
    }
})

document.getElementById("selectroom").onclick = function () {
    if (startdate != null) {
        let bookroomcount = 0
        let innerhtml = ``

        document.getElementById("titletext").innerHTML = `選擇房間`
        document.getElementById("main").style.display = "none"
        document.getElementById("selectroomdiv").style.display = "block"
        document.getElementById("check").style.display = "none"

        for (let i = 1; i <= 8; i = i + 1) {
            if (bookroomlist.includes((String(i)))) {
                innerhtml += `
                    <input type="button" class="roomselectbutton roomselectbuttonlight" id="roombutton_${i}" data-id="${i}" value="Room${i}(空房)">
                `
            } else if (canbookroom.includes(String(i))) {
                innerhtml += `
                    <input type="button" class="roomselectbutton" id="roombutton_${i}" data-id="${i}" value="Room${i}(空房)">
                `
            } else {
                innerhtml += `
                    <input type="button" class="disabled light" id="roombutton_${i}" data-id="${i}" value="Room${i}(已訂)">
                `
            }
        }

        document.getElementById("selectroomdiv").innerHTML = `
            ${innerhtml}
            <div class="leftroomtext">還剩 <span id="leftroom">0</span> 間房可選</div>
            <div class="textcenter">
                <input type="button" class="button" id="closeselectbutton" value="返回">
                <input type="button" class="button" id="clearselectbutton" value="清除">
                <input type="button" class="button" id="submitselectbutton" value="確定">
            </div>
        `

        document.querySelectorAll(".roomselectbutton").forEach(function (event2) {
            event2.onclick = function () {
                if (event2.classList.contains("roomselectbuttonlight")) {
                    event2.classList = "roomselectbutton"
                    bookroomcount = bookroomcount + 1
                } else {
                    if (bookroomcount > 0) {
                        event2.classList.add("roomselectbuttonlight")
                        bookroomcount = bookroomcount - 1
                    } else {
                        alert("沒有房間可選了")
                    }
                }
                document.getElementById("leftroom").innerHTML = bookroomcount
            }
        })

        document.getElementById("closeselectbutton").onclick = function () {
            document.getElementById("titletext").innerHTML = `選擇訂房資訊`
            document.getElementById("main").style.display = "flex"
            document.getElementById("selectroomdiv").style.display = "none"
            document.getElementById("check").style.display = "none"
        }

        document.getElementById("clearselectbutton").onclick = function () {
            bookroomcount = roomcount
            document.querySelectorAll(".roomselectbutton").forEach(function (event2) {
                event2.classList = "roomselectbutton"
            })
            document.getElementById("leftroom").innerHTML = roomcount
        }

        document.getElementById("submitselectbutton").onclick = function () {
            if (bookroomcount == 0) {
                bookroomlist = []

                document.querySelectorAll(".roomselectbuttonlight").forEach(function (event3) {
                    bookroomlist.push(event3.dataset.id)
                })

                document.getElementById("titletext").innerHTML = `選擇訂房資訊`
                document.getElementById("main").style.display = "flex"
                document.getElementById("selectroomdiv").style.display = "none"
                document.getElementById("check").style.display = "none"
                document.getElementById("roomno").value = "Room" + bookroomlist.join(",Room")
            } else {
                alert("還需要選 " + bookroomcount + " 間房")
            }
        }
    } else {
        alert("請先取消選擇日期在選擇")
    }
}

document.getElementById("submit").onclick = function () {
    if (startdate) {
        document.getElementById("titletext").innerHTML = `已選擇的訂房資訊在確認`
        document.getElementById("main").style.display = "none"
        document.getElementById("selectroomdiv").style.display = "none"
        document.getElementById("check").style.display = "block"

        document.getElementById("check").innerHTML = `
            訂房間數: <input type="text" class="notext" value="${roomcount}" disabled><br><br>
            入住天數: <input type="text" class="notext" value="${datecount}" disabled><br><br>
            入住日期: <input type="text" class="notext" value="${document.getElementById("startdate").value}~${document.getElementById("enddate").value}" disabled><br><br>
            房間號碼: <input type="text" class="notext" value="${"Room" + bookroomlist.join(",")}" disabled><br><br>
            總 價 格: <input type="text" class="notext" value="${totalprice}" disabled><br><br>
            需付訂金: <input type="text" class="notext" value="${totalprice * 0.3} (總價格30%)" disabled><br><br>
            <div class="textcenter">
                <input type="button" class="button" id="close" value="取消">
                <input type="button" class="button" id="checksubmit" value="送出">
            </div>
        `

        document.getElementById("close").onclick = function () {
            document.getElementById("titletext").innerHTML = `選擇訂房資訊`
            document.getElementById("main").style.display = "flex"
            document.getElementById("selectroomdiv").style.display = "none"
            document.getElementById("check").style.display = "none"
        }

        document.getElementById("checksubmit").onclick = function () {
            localStorage.setItem("roomcount", roomcount)
            localStorage.setItem("datecount", datecount)
            localStorage.setItem("bookroomlist", bookroomlist.join(","))
            localStorage.setItem("price", totalprice)
            localStorage.setItem("deposit", totalprice * 0.3)
            location.href = "bookroomsubmit.php"
        }
    } else {
        alert("請先選擇日期")
    }
}


function reset() {
    localStorage.removeItem("booking");
    localStorage.removeItem("startdate");
    localStorage.removeItem("enddate");
    location.reload();
}