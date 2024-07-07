let key=location.href.split("?key=")[1]

if(key=="signin"){
    docgetid("navigationbartitle2").innerHTML=`(Sign In)`
}else if(key=="signup"){
    docgetid("navigationbartitle2").innerHTML=`(Sign Up)`
}else{ alert("key error");location.href="index.html" }

if(isset(weblsget("worldskill2022MDtoken"))){
    location.href="index.html"
}

docgetid(key).classList.add("navigationbarselect")
docgetid("submit").value=`${key}`

docgetid("submit").onclick=function(){
    if(this.value=="signin"){
        let ajax=oldajax("POST",ajaxurl+"api/v1/auth/signin",JSON.stringify({
            "username": docgetid("username").value,
            "password": docgetid("password").value
        }),[
            ["Content-Type", "application/json"]
        ])
        ajax.onload=function(){
            let data=JSON.parse(ajax.responseText)

            docgetid("error").innerHTML=``
            if(data["status"]=="success"){
                alert("Sign in successfully!")
                weblsset("worldskill2022MDtoken",data["token"])
                weblsset("worldskill2022MDusername",docgetid("username").value)
                location.href="index.html"
            }else{
                if(data["message"]=="Wrong username or password"){
                    docgetid("error").innerHTML=`
                        wrong username or password
                    `
                }else{
                    if(isset(data["violations"]["username"])){
                        docgetid("error").innerHTML=`
                            username is required
                        `
                    }
                    if(isset(data["violations"]["password"])){
                        if(docgetid("error").innerHTML==""){
                            docgetid("error").innerHTML=`
                                password is required
                            `
                        }else{
                            docgetid("error").innerHTML=`
                                username and password<br>
                                is required
                            `
                        }
                    }
                }
            }
        }
    }else{
        ajax("POST",ajaxurl+"api/v1/auth/signup",function(event,data){
            docgetid("error").innerHTML=``
            if(data["status"]!="invalid"){
                alert("Sign up successfully!")
                weblsset("worldskill2022MDtoken",data["token"])
                weblsset("worldskill2022MDusername",docgetid("username").value)
                location.href="index.html"
            }else{
                if(data["message"]=="Request body is not valid."){
                    if(isset(data["violations"]["username"])){
                        if(data["violations"]["password"]["message"]=="required"){
                            docgetid("error").innerHTML=`
                                username is required
                            `
                        }else{
                            docgetid("error").innerHTML=`
                                ${docgetid("error").innerHTML}
                                username ${data["violations"]["username"]["message"]}
                            `
                        }
                    }
                    if(isset(data["violations"]["password"])){
                        if(data["violations"]["password"]["message"]=="required"){
                            if(docgetid("error").innerHTML==""){
                                docgetid("error").innerHTML=`
                                    password is required
                                `
                            }else{
                                docgetid("error").innerHTML=`
                                    username and password<br>
                                    is required
                                `
                            }
                        }else{
                            docgetid("error").innerHTML=`
                                ${docgetid("error").innerHTML}<br>
                                password ${data["violations"]["password"]["message"]}
                            `
                        }
                    }
                }else{
                    docgetid("error").innerHTML=`
                        wrong username or password
                    `
                }
            }
        },JSON.stringify({
            "username": docgetid("username").value,
            "password": docgetid("password").value
        }),[
            ["Content-Type", "application/json"]
        ])
    }
}

document.onkeydown=function(event){
    if(event.key=="Enter"){
        docgetid("submit").click()
    }
}

docgetid("cancel").onclick=function(){
    location.href="index.html"
}