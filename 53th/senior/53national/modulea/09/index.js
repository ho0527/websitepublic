let input=document.getElementById("input")
let show=document.getElementById("show")
let change=document.getElementById("change")

function doinput(){
    let value=input.value

    // 段落和換行格式
    if(change.value=="ON"){ value=value.replace(/\r?\n/g,"<br>") }
    else{ value=value.replace(/\\n/g,"<br>") }

    // 標題格式
    value=value.replace(/(#{1,6})\s(.*)/gm,function(match,hash,context){ return "<h"+hash.length+">"+context+"</h"+hash.length+">" })

    // 粗斜體格式
    value=value.replace(/\*\*\*(.*?)\*\*\*/g,"<strong><i>$1</i></strong>")

    // 粗體格式
    value=value.replace(/\*\*(.*?)\*\*/g,"<strong>$1</strong>")

    // 斜體格式
    value=value.replace(/\*(.*?)\*/g,"<i>$1</i>")

    // 水平規則格式
    value=value.replace(/---/g,"")

    // 列表格式
    value=value.replace(/^-\s(.*)/gm,"<li>$1</li>")
    // value=value.replace(/(<ul>^)<li>(.*)<\/li>/gm,"<ul><li>$1</li></ul>")
    value=value.replace(/^<li>(?!.*<\/li>)/gm,"<ul><li>$1</li></ul>")

    // 圖片格式
    value=value.replace(/!\[(.*?)\]\((.*?)\)/g,"<img src='$2' alt='$1'>")

    // 連結格式
    value=value.replace(/\[(.*?)\]\((.*?)\)/g,"<a href='$2'>$1</a>")

    console.log(value)
    show.innerHTML=value
}

input.addEventListener("keydown",function(event){
    if(event.key=="Tab"){
        event.preventDefault()
        this.value=this.value.substring(0,this.selectionStart)+"    "+this.value.substring(this.selectionEnd)
        this.selectionStart=this.selectionStart+4
        this.selectionEnd=this.selectionStart+4
    }
})

change.onclick=function(){
    if(change.value=="ON"){ change.value="OFF" }
    else{ change.value="ON" }
    doinput()
}

input.addEventListener("input",function(){ doinput() })