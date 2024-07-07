let book=JSON.parse(localStorage.getItem("book"))

if(book!=null&&book["endday"]!=null){
    let count=1

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
            <input type="button" class="btn btn-light" id="back" value="放棄離開">
            <input type="button" class="btn btn-light" id="cancel" value="取消">
            <input type="button" class="btn btn-warning" id="submit" value="確定選取">
        </div>
    `

    document.querySelectorAll(".roombutton").forEach(function(event){
        event.onclick=function(){
            if(event.classList=="btn btn-warning roombutton"){
                event.classList="btn btn-light roombutton"
                count=0
            }else{
                if(count==0){
                    event.classList="btn btn-warning roombutton"
                    count=1
                }else{
                    alert("以無房間可以選擇")
                }
            }
        }
    })

    document.getElementById("back").onclick=function(){
        location.href=localStorage.getItem("location")
    }

    document.getElementById("cancel").onclick=function(){
        document.querySelectorAll(".roombutton").forEach(function(event){
            event.classList="btn btn-light roombutton"
        })
        count=0
    }

    document.getElementById("submit").onclick=function(){
        if(count==1){
            book["room"]=document.querySelectorAll(".btn-warning.roombutton")[0].id
            localStorage.setItem("book",JSON.stringify(book))
            location.href=localStorage.getItem("location")
        }else{
            alert("還剩 1 間房要選")
        }
    }
}else{
    alert("請先選擇日期")
    location.href=localStorage.getItem("location")
}