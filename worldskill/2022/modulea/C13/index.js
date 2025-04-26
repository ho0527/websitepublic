let input=document.getElementById("input")
let show=document.getElementById("show")

function inputchange(){
    let value=input.value

    // 段落和換行格式
    value=value.replace(/\n/g,"<br>") 

    // 標題格式
    value=value.replace(/(#{1,6})\s(.*?)<br>/g,function(match,hash,context){ return "<h"+hash.length+">"+context+"</h"+hash.length+">" })
    value=value.replace(/(#{1,6})\s(.*?)/g,function(match,hash,context){ return "<h"+hash.length+">"+context+"</h"+hash.length+">" })

    // 粗體格式
    value=value.replace(/\*\*(.*?)\*\*/g,"<strong>$1</strong>")

    // 水平規則格式
    value=value.replace(/---/g,"<hr>")

    // 列表格式
    value=value.replace(/-\s(.+?)<br>/g,"<li>$1</li>")
    value=value.replace(/-\s(.+)/g,"<li>$1</li>")
    value=value.replace(/<li>(.*)<\/li>/g,"<ul><li>$1</li></ul>")

    // 圖片格式
    value=value.replace(/!\[(.*?)\]\((.*?)\)/g,"<img src='$2' alt='$1'>")

    // 連結格式
    value=value.replace(/\[(.*?)\]\((.*?)\)/g,"<a href='$2'>$1</a>")

    console.log(value)
    show.innerHTML=value
}

input.addEventListener("input",function(){ inputchange() })