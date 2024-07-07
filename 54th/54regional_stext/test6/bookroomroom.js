let book=JSON.parse(localStorage.getItem("book"))

if(book!=null&&book["lastdate"]!=null){
    let count=0

    for(let i=1;i<=8;i=i+1){
        document.getElementById("main").innerHTML+=`
            ${
                (book["room"]==i)?
                    (`<input type="button" class="btn btn-warning roombutton" id="${i}" value="Room${i}(空房)">`):
                    (book["canbookroom"].includes(i))?
                        (`<input type="button" class="btn btn-light roombutton" id="${i}" value="Room${i}(空房)">`):
                        (`<input type="button" class="btn btn-danger" id="${i}" value="Room${i}(已訂)" disabled>`)
            }
        `
    }
    document.getElementById("main").innerHTML+=`
        <div class="div text-center">
            <input type="button" class="btn btn-light" onclick="location.href=localStorage.getItem('location')" value="放棄離開">
            <input type="button" class="btn btn-light" id="cancel" value="取消">
            <input type="button" class="btn btn-warning" id="submit" value="確定訂房">
        </div>
    `

    document.querySelectorAll(".roombutton").forEach(function(event){
        event.onclick=function(){
            if(event.classList=="btn btn-warning roombutton"){
                event.classList="btn btn-light roombutton"
                count=1
            }else{
                if(count==1){
                    event.classList="btn btn-warning roombutton"
                    count=0
                }else{
                    alert("沒有房間可以選了")
                }
            }
        }
    })

    document.getElementById("cancel").onclick=function(){
        document.querySelectorAll(".roombutton").forEach(function(event){
            event.classList="btn btn-light roombutton"
        })
    }

    document.getElementById("submit").onclick=function(){
        if(count==0){
            book["room"]=parseInt(document.querySelectorAll(".btn-warning.roombutton")[0].id)
            localStorage.setItem("book",JSON.stringify(book))
            location.href=localStorage.getItem("location")
        }else{
            alert("還有 1 間房要選")
        }
    }
}else{
    alert("請先選擇日期")
    if(localStorage.getItem("location")){
        location.href=localStorage.getItem("location")
    }else{
        location.href="bookroom.php"
    }
}