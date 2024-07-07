docgetid("changecolor").onclick=function(){
    if(docgetall("html")[0].classList=="mainlight"){
        docgetall("html")[0].classList="maindark"
    }else{
        docgetall("html")[0].classList="mainlight"
    }
}