function removearrayduplicate(array){
    let uniquearray=[];
    let seenelement={};
    for(let i=0;i<array.length;i=i+1){
        let element=array[i];
        if(!seenelement[element]){
            uniquearray.push(element);
            seenelement[element]=true;
        }
    }
    return uniquearray;
}

document.getElementById("submit").onclick=function(){
    let ans
    let respond
    let ansreader=new FileReader()
    ansreader.onload=function(event){
        ans=event.target.result.split("\n")
    }
    ansreader.readAsText(document.getElementById("inputfileans").files[0])
    let respondreader=new FileReader()
    respondreader.onload=function(event){
        respond=event.target.result.split("\n")
    }
    respondreader.readAsText(document.getElementById("inputfilerespond").files[0])
    setTimeout(function(){
        let data=""
        let score=0
        data=data+"<tr>"+"<td class='td'>"+"題號"+"</td>"+"<td class='td'>"+"ans"+"</td>"+"<td class='td'>"+"respond"+"</td>"+"</tr>"
        for(let i=0;i<ans.length;i=i+1){
            if(ans[i]==respond[i]){
                data=data+"<tr style='background: green'>"+"<td class='td'>"+(i+1)+"</td>"+"<td class='td'>"+ans[i]+"</td>"+"<td class='td'>"+respond[i]+"</td>"+"</tr>"
                score=score+1
            }else{
                data=data+"<tr style='background: red'>"+"<td class='td'>"+(i+1)+"</td>"+"<td class='td'>"+ans[i]+"</td>"+"<td class='td'>"+respond[i]+"</td>"+"</tr>"
            }
        }
        document.getElementById("table").innerHTML=`
            ${data}
        `
        document.getElementById("score").innerHTML=`
            score:${score}/${ans.length}
        `
    },100)
}

document.getElementById("reflashbutton").onclick=function(){
    location.reload()
}