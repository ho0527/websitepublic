let book={
    totalday: 0,
    totalprice: 0,
    startday: null,
    endday: null,
    startdate: null,
    enddate: null,
    canbookroom: [],
    room: ""
}

function load(){
    if(localStorage.getItem("book")&&JSON.parse(localStorage.getItem("book"))["enddate"]!=null){
        book=JSON.parse(localStorage.getItem("book"))

        book["totalday"]=book["enddate"]-book["startdate"]+1
        book["totalprice"]=0
        document.getElementById("totalday").innerHTML=book["totalday"]
        document.getElementById("startday").innerHTML=book["startday"]
        document.getElementById("endday").innerHTML=book["endday"]
        document.getElementById("room").innerHTML="Room"+book["room"]

        document.getElementById("prevmonth").disabled=true
        document.getElementById("nextmonth").disabled=true

        for(let i=book["startdate"];i<=book["enddate"];i=i+1){
            if(document.getElementById(i)){
                document.getElementById(i).classList.add("select")
                book["totalprice"]=book["totalprice"]+parseInt(document.getElementById(i).dataset.cost)
            }
        }
    }
}

function auto(){
    if(book["endday"]!=null){
        if(1<=book["canbookroom"].length){
            let rand=Math.floor(Math.random()*book["canbookroom"].length)
            book["room"]=book["canbookroom"][rand]
            localStorage.setItem("book",JSON.stringify(book))
            load()
        }else{
            alert("無空房")
        }
    }else{
        alert("請先選擇日期")
    }
}

load()

document.querySelectorAll(".cdatediv").forEach(function(event){
    event.onclick=async function(){
        let date=parseInt(event.id)
        let day=event.dataset.day

        if(book["startdate"]==null||(book["totalday"]<2&&book["startdate"]<date)){
            if(book["startdate"]==null){
                book["startdate"]=date
                book["startday"]=day
            }
            let request=await fetch("api.php?getleftroom=&startday="+book["startday"]+"&endday="+day)
            book["canbookroom"]=await request.json()

            if(1<=book["canbookroom"].length){
                book["enddate"]=date
                book["endday"]=day
                auto()
            }else{
                if(book["enddate"]==null){
                    book["startdate"]=null
                    book["startday"]=null
                }
                alert("已無空房")
            }
        }else{
            alert("請先取消選擇")
        }
    }
})