docgetid("signout").onclick=function(){
    ajax("POST",ajaxurl+"api/v1/auth/signout",function(event,data){
        if(data["status"]=="success"){
            weblsset("worldskill2022MDtoken",null)
            weblsset("worldskill2022MDusername",null)
            location.href="signout.html"
        }else{
            alert(data["message"])
            if(data["message"]=="invalid token"){
                weblsset("worldskill2022MDtoken",null)
                weblsset("worldskill2022MDusername",null)
                location.href="index.html"
            }
        }
    },null,[
        ["Authorization","Bearer "+weblsget("worldskill2022MDtoken")]
    ])
}

ajax("GET",ajaxurl+"api/v1/users/"+weblsget("worldskill2022MDusername"),function(event,data){
    console.log(data)
    // navbar
    docgetid("navigationbartitle2").innerHTML=`
        (User Profile: ${weblsget("worldskill2022MDusername")})
    `

    // 製作的遊戲
    ajax("GET",ajaxurl+"api/v1/games",function(event2,data2){
        ajax("GET",ajaxurl+"api/v1/games?size="+data2["totalElements"],function(event3,data3){
            for(let i=0;i<data3["content"].length;i=i+1){
                if(data3["content"][i]["author"]==weblsget("worldskill2022MDusername")){
                    let pictureurl="material/picture/default.jpg"

                    // game div
                    innerhtml("#profilegamediv",`
                        <div class="game profilegame grid" id="${data3["content"][i]["slug"]}">
                            <div class="title">${data3["content"][i]["title"]}</div>
                            <div class="description">${data3["content"][i]["description"]}</div>
                            <div class="scorecount">score submit: ${data3["content"][i]["scoreCount"]}</div>
                            <div class="imagediv"><img src="${pictureurl}" class="image"></div>
                        </div>
                    `)
                }
            }

            if(docgetid("profilegamediv").innerHTML!=""){
                docgetid("profilegamediv").innerHTML=`
                    <div class="profiletitle">Authored Games</div>
                    ${docgetid("profilegamediv").innerHTML}
                `
            }

            docgetall(".game").forEach(function(event){
                event.onclick=function(){
                    location.href="game.html?game="+event.id
                    weblsset("worldskill2022MDgame",event.id)
                }
            })
        })
    })

    data["highscores"].sort(function(a,b){
        if(b["game"]["title"].toLowerCase()<=a["game"]["title"].toLowerCase()){
            return 1
        }else{
            return -1
        }
    })

    for(let i=0;i<data["highscores"].length;i=i+1){
        innerhtml("#profilehightscore",`
            <div class="profilehightscorediv">
                <input type="button" class="buttonghost" onclick="weblsset('worldskill2022MDgame','${data["highscores"][i]["game"]["slug"]}');location.href='game.html'" value="${data["highscores"][i]["game"]["title"]}">
                <div>${data["highscores"][i]["score"]}</div>
            </div>
        `)
    }
},null,[
    ["Authorization","Bearer "+weblsget("worldskill2022MDtoken")]
])

docgetid("usernamelink").innerHTML=`
    ${weblsget("worldskill2022MDusername")} profile
`

if(!isset(weblsget("worldskill2022MDtoken"))){
    location.href="signinsignup.html?key=signin"
}