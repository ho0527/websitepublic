document.getElementById("submit").onclick=function(){
    let search=document.getElementById("search").value; // 獲取搜尋字符串
    let input=document.getElementById("text").innerHTML; // 文字區域

    // 進行關鍵字高亮
    let randomcolor="rgb("+Math.floor(Math.random()*255)+","+Math.floor(Math.random()*255)+","+Math.floor(Math.random()*255)+")"
    let data=input.replace(new RegExp("("+search+")","g"),"<span style='background: "+randomcolor+";'>$1</span>"); // 將匹配的單詞用<span>標籤包裹並添加highlight類

    // 更新文字區域的內容
    document.getElementById("text").innerHTML=data;
}

// let input=document.getElementById("text").innerHTML
// let newtext=""
// for(let i=0;i<input.length;i=i+1){
//     newtext=newtext+"<span>"+input[i]+"</span>"
// }

// console.log(newtext)
// document.getElementById("text").innerHTML=newtext
// input=document.getElementById("text").innerHTML

// document.getElementById("submit").onclick=function(){
//     let search=document.getElementById("search").value; // 文字區域
//     let newsearch=search.split("")
//     let datasearch="";
//     for(let i=0;i<search.length;i=i+1){
//         datasearch=datasearch+"<span>"+search[i]+"</span>"
//     }
//     // 進行關鍵字高亮
//     let randomcolor="rgb("+Math.floor(Math.random()*255)+","+Math.floor(Math.random()*255)+","+Math.floor(Math.random()*255)+")"
//     let textdata=input
//     let data=input.replace(new RegExp("("+datasearch+")","g"),"<span style='background: "+randomcolor+";'>$1</span>"); // 將匹配的單詞用<span>標籤包裹並添加highlight類
//     console.log("datasearch="+datasearch)
//     // let randomcolor="rgb("+Math.floor(Math.random()*255)+","+Math.floor(Math.random()*255)+","+Math.floor(Math.random()*255)+")"
//     // let textdata=input
//     // let data=input.replace(new RegExp("("+search+")","g"),"<span style='background: "+randomcolor+";'>$1</span>"); // 將匹配的單詞用<span>標籤包裹並添加highlight類

//     // 更新文字區域的內容
//     document.getElementById("text").innerHTML=data
//     newtext=textdata
// }