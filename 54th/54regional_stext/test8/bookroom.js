let book={
    roomcount: 1,
    totalday: 0,
    totalprice: 0,
    startday: null,
    endday: null,
    startdate: null,
    enddate: null,
    canbookroom: [],
    room: [],
    booked: false
}

function load(){
    if(localStorage.getItem("book")&&JSON.parse(localStorage.getItem("book"))["booked"]){
        book=JSON.parse(localStorage.getItem("book"))

        book["totalday"]=book["enddate"]-book["startdate"]+1
        book["totalprice"]=0
        document.getElementById("totalday").innerHTML=book["totalday"]
        document.getElementById("startday").innerHTML=book["startday"]
        document.getElementById("endday").innerHTML=book["endday"]
        document.getElementById("room").innerHTML="Room"+book["room"].join("，Room")
        document.getElementById("roomcount").value=book["roomcount"]

        document.getElementById("roomcount").disabled=true

        for(let i=book["startdate"];i<=book["enddate"];i=i+1){
            if(document.getElementById(i)){
                document.getElementById(i).classList.add("select")
                book["totalprice"]=book["totalprice"]+(parseInt(document.getElementById(i).dataset.cost)*book["room"].length)
            }
        }
    }
}

function auto(){
    if(book&&book["booked"]){
        if(book["roomcount"]<=book["canbookroom"].length){
            let canbookroom=[...book["canbookroom"]]
            book["room"]=[]

            for(let i=0;i<book["roomcount"];i=i+1){
                let rand=Math.floor(Math.random()*canbookroom.length)
                book["room"].push(canbookroom[rand])
                canbookroom.splice(rand,1)
            }

            book["room"].sort()

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

        if(!book["booked"]||(book["totalday"]<2&&book["startdate"]<date)){
            if(book["startdate"]==null){
                book["startdate"]=date
                book["startday"]=day
            }

            let request=await fetch("api.php?getleftroom=&startday="+book["startday"]+"&endday="+day)
            book["canbookroom"]=await request.json()

            if(book["roomcount"]<=book["canbookroom"].length){
                book["enddate"]=date
                book["endday"]=day
                book["booked"]=true
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

document.getElementById("roomcount").onchange=function(){
    book["roomcount"]=document.getElementById("roomcount").value
}