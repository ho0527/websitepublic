let verifycodeans
let bigorsmall

function main(){
    if(isset(weblsget("53regionalusername"))){ docgetid("username").value=weblsget("53regionalusername") }
    if(isset(weblsget("53regionalpassword"))){ docgetid("password").value=weblsget("53regionalpassword") }

    bigorsmall=0
    docgetid("key").innerHTML=`'由大排到小'`
    if(Math.floor(Math.random()*2)){
        docgetid("key").innerHTML=`'由小排到大'`
        bigorsmall=1
    }

    verifycodeans=[]
    docgetid("verifycode").innerHTML=``
    docgetid("dropbox").innerHTML=``
    for(let i=0;i<4;i=i+1){
        let small="abcdefghijklmnopqrstuvwxvz"
        let big=small.toUpperCase()
        let number="0123456789"
        let wordlist=small+big+number
        let radom=Math.floor(Math.random()*62)
        let word=wordlist[radom]

        docgetid("verifycode").innerHTML=`
            ${docgetid("verifycode").innerHTML}
            <div class="dragbox">
                <img src="api/verifycode.php?str=${word}" class="dragimage" id="${i}" data-id="${word}" draggable="true">
            </div>
        `

        verifycodeans.push(word)
    }

    if(bigorsmall){
        verifycodeans.sort()
    }else{
        verifycodeans.sort().reverse()
    }

    verifycodeans=verifycodeans.join("")

    docgetall(".dragimage").forEach(function(event){
        event.ondragstart=function(listenerevent){
            listenerevent.dataTransfer.setData("text",listenerevent.target.id)
        }
    })

    docgetid("dropbox").ondragover=function(event){
        event.preventDefault()
    }

    docgetid("dropbox").ondrop=function(event){
        let id=event.dataTransfer.getData("text")
        let data=docgetid(id)
        event.draggable=false
        docgetid("dropbox").appendChild(data)
    }
    console.log("verifycodeans="+verifycodeans)
}

if(!isset(weblsget("53regionalerrortime"))){ weblsset("53regionalerrortime",0) }
main()

docgetid("reflash").onclick=function(){
    main()
}

docgetid("clear").onclick=function(){
    weblsset("53regionalusername",null)
    weblsset("53regionalpassword",null)
    main()
}

docgetid("submit").onclick=function(){
    let verifycodeuserans=""
    docgetall("#dropbox>.dragimage").forEach(function(event){
        verifycodeuserans=verifycodeuserans+event.dataset.id
    })
    oldajax("POST","/backend/53regional/login",JSON.stringify({
        "username": docgetid("username").value,
        "password": docgetid("password").value,
        "verifycodeans": verifycodeans,
        "verifycodeuserans": verifycodeans // verifycodeuserans
    }),[
        ["Content-Type","application/json"]
    ]).onload=function(){
        let data=JSON.parse(this.responseText)
        if(data["success"]){
            alert("登入成功")
            weblsset("53regionalerrortime",null)
            weblsset("53regionaluserid",data["data"]["id"])
            weblsset("53regionalpermission",data["data"]["permission"])
            weblsset("53regionaltime",30)
            oldajax("GET","/backend/53regional/logindatatodb?key=success&id="+data["data"]["id"]).onload=function(){
                let data=JSON.parse(this.responseText)
                if(data["success"]){
                    location.href="verify.html"
                }
            }
        }else{
            alert(data["data"]["message"])
            weblsset("53regionalerrortime",parseInt(weblsget("53regionalerrortime"))+1)
            if(weblsget("53regionalerrortime")>=3){
                oldajax("GET","/backend/53regional/logindatatodb?key=error&id="+data["data"]["id"]).onload=function(){
                    let data=JSON.parse(this.responseText)
                    if(data["success"]){
                        weblsset("53regionalerrortime",null)
                        location.href="usererror.html"
                    }
                }
            }
            main()
        }
    }
}

document.onkeydown=function(event){
    if(event.key=="Enter"){
        event.preventDefault()
        docgetid("submit").click()
    }
}