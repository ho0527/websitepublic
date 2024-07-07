let page=0
let size=10
let sortby="title"
let sorttype="asc"

function main(){
    ajax("GET",ajaxurl+"api/v1/games?page="+page+"&size="+size+"&sortBy="+sortby+"&sortDir="+sorttype,function(event,data){
        let total=data["totalElements"]
        for(let i=0;i<data["content"].length;i=i+1){
            let pictureurl="material/picture/default.jpg"

            // 不會接image
            if(data["content"][i]["thumbnail"]){
                pictureurl=data["content"][i]["thumbnail"]
            }

            // game div
            innerhtml("#main",`
                <div class="game grid" data-id="${data["content"][i]["slug"]}">
                    <div class="title">${data["content"][i]["title"]}</div>
                    <div class="author">by ${data["content"][i]["author"]}</div>
                    <div class="description">${data["content"][i]["description"]}</div>
                    <div class="scorecount">score submit: ${data["content"][i]["scoreCount"]}</div>
                    <div class="imagediv"><img src="${pictureurl}" class="image"></div>
                </div>
            `)
        }
        docgetid("gamecount").innerHTML=`${total}`

        docgetall(".game").forEach(function(event){
            event.onclick=function(){
                weblsset("worldskill2022MDgame",event.dataset.id)
                location.href="game.html"
            }
        })
    })
    page=page+1
}

function clearselectbutton(){
    docgetall(".indexfunctionbutton").forEach(function(event){
        event.classList.remove("bluebuttonselect")
    })
}

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

if(!isset(weblsget("worldskill2022MDindexsortby"))){
    weblsset("worldskill2022MDindexsortby","title")
}

if(!isset(weblsget("worldskill2022MDindexsorttype"))){
    weblsset("worldskill2022MDindexsorttype","asc")
}

clearselectbutton()
docgetid(weblsget("worldskill2022MDindexsortby")).classList.add("bluebuttonselect")
docgetid(weblsget("worldskill2022MDindexsorttype")).classList.add("bluebuttonselect")
sortby=weblsget("worldskill2022MDindexsortby")
sorttype=weblsget("worldskill2022MDindexsorttype")


docgetall(".indexfunctionbutton").forEach(function(event){
    event.onclick=function(){
        if(event.id=="asc"||event.id=="desc"){
            weblsset("worldskill2022MDindexsorttype",event.id)
            sorttype=event.id
        }else{
            weblsset("worldskill2022MDindexsortby",event.id)
            sortby=event.id
        }
        clearselectbutton()
        docgetid(weblsget("worldskill2022MDindexsortby")).classList.add("bluebuttonselect")
        docgetid(weblsget("worldskill2022MDindexsorttype")).classList.add("bluebuttonselect")
        docgetid("main").innerHTML=``
        page=page-1
        main()
    }
})


main()

document.onscroll=function(){
    if((window.innerHeight+Math.round(window.scrollY))>=document.body.offsetHeight){
        setTimeout(main,500)
    }
}


startmacossection()