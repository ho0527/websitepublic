let verifydiva=document.getElementById("verifydiva")
let verifydivb=document.getElementById("verifydivb")
let verifydivc=document.getElementById("verifydivc")
let verify1=""
let verify2=""
let verify3=""

function random(div,result){
    let characters="XYZ"
    let charactersLength=characters.length
    result=result+characters.charAt(Math.floor(Math.random()*charactersLength))
    div.innerHTML=result
    return result
}

function change(div,result){
    if(result=="X"){
        result="Y"
    }else if(result=="Y"){
        result="Z"
    }else{
        result="X"
    }
    div.innerHTML=result
    return result
}

verify1=random(verifydiva,verify1)
verify2=random(verifydivb,verify2)
verify3=random(verifydivc,verify3)

verifydiva.addEventListener("click",function(){
    verify1=change(verifydiva,verify1)
})
verifydivb.addEventListener("click",function(){
    verify2=change(verifydivb,verify2)
})
verifydivc.addEventListener("click",function(){
    verify3=change(verifydivc,verify3)
})

function verifysubmit(){
    if((verify1==verify2)&&(verify1==verify3)){
        location.href="admin.php"
    }else{
        alert("驗證碼輸入失敗!")
        location.href="verify.php"
    }
}