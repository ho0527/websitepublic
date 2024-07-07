document.getElementById("submit").onclick=function(){
    let name=document.getElementById("name").value
    let cost=document.getElementById("cost").value
    if(name!=""&&cost!=""){
        document.getElementById("main").innerHTML=document.getElementById("main").innerHTML+`
            <li>
                name=${name},cost=${cost}
            </li>
        `
        document.getElementById("respone").innerHTML=`Correct`
        setTimeout(function(){
            document.getElementById("name").value=""
            document.getElementById("cost").value=""
        },3000);
    }else{
        document.getElementById("respone").innerHTML=`There was an error`
    }
}
