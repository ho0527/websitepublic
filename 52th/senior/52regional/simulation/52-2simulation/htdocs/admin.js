let num=document.getElementById("num")
let user=document.getElementById("user")
let password=document.getElementById("password")
let uname=document.getElementById("uname")
let data=[num,user,password,uname]

data.forEach(function(buttons){
    buttons.value="升冪"
    buttons.onclick=function(){
        if(buttons.value=="升冪"){
            buttons.value="降冪"
        }else{
            buttons.value="升冪"
        }
        data.forEach(function(outherbuttons){
            if(outherbuttons!=buttons){
                outherbuttons.value="升冪"
            }
        })
    }
})