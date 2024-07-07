let book={
    price: null,
    totaldate: null,
    startday: null,
    endday: null,
    startdate: null,
    enddate: null,
    room: "",
    canbookroom: [],
}

function load(){
    if(localStorage.getItem("book")&&JSON.parse(localStorage.getItem("book"))["enddate"]){
        book=JSON.parse(localStorage.getItem("book"))
        book["totaldate"]=book["enddate"]-book["startdate"]+1
        book["price"]=book["totaldate"]*5000
        document.getElementById("totaldate").innerHTML=book["totaldate"]
        document.getElementById("startday").innerHTML=book["startday"]
        document.getElementById("endday").innerHTML=book["endday"]
        document.getElementById("room").innerHTML="Room"+book["room"]
        document.getElementById("prevmonth").disabled=true
        document.getElementById("nextmonth").disabled=true
        localStorage.setItem("book",JSON.stringify(book))
        for(let i=book["startdate"];i<=book["enddate"];i=i+1){
            if(document.getElementById(i))
                document.getElementById(i).classList.add("select")
        }
    }
}

function auto(){
    if(book["endday"]!=null){
        let rand=Math.floor(Math.random()*book["canbookroom"].length)
        book["room"]=book["canbookroom"][rand]
        localStorage.setItem("book",JSON.stringify(book))
        load()
    }else{
        alert("請先選擇日期")
    }
}

load()

document.querySelectorAll(".cdivdate").forEach(function(event){
    event.onclick=async function(){
        let date=parseInt(event.id)
        let day=event.dataset.day

        if(book["startdate"]==null||(book["startdate"]<=date&&book["enddate"]==book["startdate"])){
            if(book["startdate"]==null){
                book["startdate"]=date
                book["startday"]=day
            }
            let req=await fetch("api.php?getleftroom=&startday="+book["startday"]+"&endday="+day)
            book["canbookroom"]=await req.json()

            if(book["canbookroom"].length>0){
                book["enddate"]=date
                book["endday"]=day
                auto()
            }else{
                if(book["enddate"]==null){
                    book["startdate"]=null
                    book["startday"]=null
                }
                book["canbookroom"]=[]
                alert("房間數量不足")
            }
        }else{
            alert("請先取消選擇")
        }
    }
})