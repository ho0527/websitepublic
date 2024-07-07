function leaderboard(){
    ajax("GET",ajaxurl+"api/v1/games/"+weblsget("worldskill2022MDgame")+"/scores",function(event,data){
        let username=false
        let usercheck=false

        docgetid("gameleaderboard").innerHTML=``

        if(weblsget("worldskill2022MDusername")){ username=weblsget("worldskill2022MDusername") }

        for(let i=0;i<Math.min(10,data["scores"].length);i=i+1){
            if(username==data["scores"][i]["username"]&&username){
                docgetid("gameleaderboard").innerHTML=`
                    ${docgetid("gameleaderboard").innerHTML}
                    <tr class="highlightuser">
                        <td class="gamenotd">#${i+1}</td>
                        <td class="gameusernametd">${data["scores"][i]["username"]}</td>
                        <td class="gamescoretd">${data["scores"][i]["score"]}</td>
                    </tr>
                `
                usercheck=true
            }else{
                docgetid("gameleaderboard").innerHTML=`
                    ${docgetid("gameleaderboard").innerHTML}
                    <tr class="gametr">
                        <td class="gamenotd">#${i+1}</td>
                        <td class="gameusernametd">${data["scores"][i]["username"]}</td>
                        <td class="gamescoretd">${data["scores"][i]["score"]}</td>
                    </tr>
                `
            }
        }

        if(!usercheck){
            for(let i=0;i<data["scores"].length;i=i+1){
                if(username==data["scores"][i]["username"]&&username){
                    docgetid("gameleaderboard").innerHTML=`
                        ${docgetid("gameleaderboard").innerHTML}
                        <tr class="highlightuser">
                            <td class="gamenotd"></td>
                            <td class="gameusernametd">${data["scores"][i]["username"]}</td>
                            <td class="gamescoretd">${data["scores"][i]["score"]}</td>
                        </tr>
                    `
                    usercheck=true
                }
            }
        }
    })
}

if(!isset(weblsget("worldskill2022MDgame"))){
    weblsset("worldskill2022MDgame","demo-game-2")
    // location.href="index.html"
}

ajax("GET",ajaxurl+"api/v1/games/"+weblsget("worldskill2022MDgame"),function(event,data){
    console.log(data)
    docgetid("navigationbartitle2").innerHTML=`
        (Game: ${data["title"]})
    `
    docgetid("gamedescription").innerHTML=`
        ${data["description"]}
    `

    docgetid("game").innerHTML=`
        <iframe src="${data["gamePath"]}" class="iframe" title="這是遊戲"></iframe>
    `

    // 回傳分數
    getmessage(function(element,event){
        if(window.confirm("Are you sure you want to submit your score?")){
            ajax("POST","/backend/worldskill2022modulec/api/v1/games/"+weblsget("worldskill2022MDgame")+"/scores",function(event,data){
                if(data["status"]=="success"){
                    location.reload()
                }else{
                    alert(data["message"])
                }
            },JSON.stringify({
                "score": event.data.score
            }),[
                ["Authorization","Bearer "+weblsget("worldskill2022MDtoken")]
            ])
        }
    })
})

leaderboard()
setInterval(leaderboard,5000)

// show signin/signup || signout button
if(isset(weblsget("worldskill2022MDtoken"))){
    docgetid("navigationbarright").innerHTML=`
        <a href="profile.html" class="a navigationbara">${weblsget("worldskill2022MDusername")} profile</a>
        <input type="button" class="navigationbarbutton" id="signout" value="Sign Out">
    `

    // logout
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
}else{
    docgetid("navigationbarright").innerHTML=`
        <input type="button" class="navigationbarbutton" onclick="location.href='signinsignup.html?key=signup'" value="Sign Up">
        <input type="button" class="navigationbarbutton" onclick="location.href='signinsignup.html?key=signin'" value="Sign In">
    `
}