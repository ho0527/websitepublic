let book=JSON.parse(localStorage.getItem("book"))

if(book!=null&&book["enddate"]!=null){
    let room=1

    document.getElementById("date").innerHTML=`
        <div class="title2">${book["startday"]}~${book["endday"]}</div>
    `
    for(let i=1;i<=8;i=i+1)
        document.getElementById("main").innerHTML+=`
            ${
                (book["room"]==i)?
                    (`<input type="button" class="btn btn-warning room" id="${i}" value="Room${i}(空房)">`):
                    (book["canbookroom"].includes(i))?
                        (`<input type="button" class="btn btn-light room" id="${i}" value="Room${i}(空房)">`):
                        (`<input type="button" class="btn btn-danger" id="${i}" value="Room${i}(已訂)" disabled>`)
            }
        `
    document.getElementById("main").innerHTML+=`
        <div class="text-center">
            <input type="button" class="btn btn-light" onclick="location.href=localStorage.getItem('booklocation')" value="放棄離開">
            <input type="button" class="btn btn-light" id="cancel" value="取消">
            <input type="button" class="btn btn-warning" id="submit" value="確定選取">
        </div>
    `

    document.querySelectorAll(".room").forEach(function(event){
        event.onclick=function(){
            if(event.classList=="btn btn-warning room"){
                event.classList="btn btn-light room"
                room=0
            }else{
                if(room==0){
                    event.classList="btn btn-warning room"
                    room=1
                }else{
                    alert("沒有房間可以選了")
                }
            }
        }
    })

    document.getElementById("cancel").onclick=function(){
        document.querySelectorAll(".btn-warning.room")[0].classList="btn btn-light room"
        room=0
    }

    document.getElementById("submit").onclick=function(){
        if(room==1){
            book["room"]=document.querySelectorAll(".btn-warning.room")[0].id
            localStorage.setItem("book",JSON.stringify(book))
            location.href=localStorage.getItem('booklocation')
        }else{
            alert("還剩 1 間房要選")
        }
    }
}else{
    alert("請先選擇日期")
    location.href=localStorage.getItem('booklocation')
}