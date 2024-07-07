let book={
    totalprice: 0,
    totaldate: 0,
    firstday: null,
    lastday: null,
    firstdate: null,
    lastdate: null,
    room: "",
    canbookroom: []
}

function load(){
    if(localStorage.getItem("book")&&JSON.parse(localStorage.getItem("book"))["lastdate"]!=null){
        book=JSON.parse(localStorage.getItem("book"))

        book["totaldate"]=book["lastdate"]-book["firstdate"]+1
        book["totalprice"]=book["totaldate"]*5000
        document.getElementById("totaldate").innerHTML=book["totaldate"]
        document.getElementById("firstday").innerHTML=book["firstday"]
        document.getElementById("lastday").innerHTML=book["lastday"]
        document.getElementById("room").innerHTML="Room"+book["room"]

        document.getElementById("prevmonth").disabled=true
        document.getElementById("nextmonth").disabled=true

        for(let i=book["firstdate"];i<=book["lastdate"];i=i+1){
            document.getElementById(i).classList.add("select")
        }
    }
}

function auto(){
    if(book["lastdate"]!=null){
        let rand=Math.floor(Math.random()*book["canbookroom"].length)
        book["room"]=book["canbookroom"][rand]

        localStorage.setItem("book",JSON.stringify(book))
        load()
    }else{
        alert("請先選擇日期")
    }
}

load()

document.querySelectorAll(".datediv").forEach(function(event){
    event.onclick=async function(){
        let date=parseInt(event.id)
        let day=event.dataset.day

        if(book["firstdate"]==null||(book["totaldate"]<2&&book["lastdate"]<=date)){
            if(!book["firstdate"]){
                book["firstdate"]=date
                book["firstday"]=day
            }
            
            let request=await fetch("api.php?getleftroom=&firstday="+book["firstday"]+"&lastday="+day+"")
            book["canbookroom"]=await request.json()

            if(0<book["canbookroom"].length){
                book["lastdate"]=date
                book["lastday"]=day

                auto()
            }else{
                if(!book["lastdate"]){
                    book["firstdate"]=null
                    book["firstday"]=null
                }
                book["canbookroom"]=[]
                alert("無空房! 房間數量不足")
            }
        }else{
            alert("請先取消選擇")
        }
    }
})