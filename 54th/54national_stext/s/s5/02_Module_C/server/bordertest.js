document.getElementById("page6").innerHTML=``
for(let i=0;i<15;i=i+1){
    for(let j=0;j<15;j=j+1){
        // ${data["game"]["border"][i][j]}
        document.getElementById("page6").innerHTML+=`
            <div class="borderdiv B " data-id="${i}_${j}">
                <div class="border right bottom"></div>
                <div class="border left bottom"></div>
                <div class="border right top"></div>
                <div class="border left top"></div>
            </div>
        `
    }
}

document.querySelectorAll(".borderdiv").forEach(function(event){
    event.onclick=function(){
        let i=event.dataset.id.split("_")[0]
        let j=event.dataset.id.split("_")[1]
        if((data["game"]["turn"]=="B"&&data["game"]["player"][0]==userid)||(data["game"]["turn"]=="W"&&data["game"]["player"][1]==userid)){
            if(data["game"]["border"][i][j]=="-"){
                data["game"]["border"][i][j]=data["game"]["turn"]
                websocket.send(JSON.stringify({
                    "key": "updategame",
                    "border": data["game"]["border"],
                    "turn": data["game"]["turn"]=="B"?"W":"B",
                    "roomid": roomid
                }))
            }else{
                alert("無法放置在目前位置。")
            }
        }else{
            alert("不是你的回合。")
        }
    }
})