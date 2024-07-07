document.querySelectorAll(".vercode").forEach(function(event){
    event.ondragstart=function(event2){
        event2.dataTransfer.setData("text",event2.target.id)
    }
})

document.getElementById("dropbox").ondragover=function(event){
    event.preventDefault()
}

document.getElementById("dropbox").ondrop=function(event){
    let data=document.getElementById(event.dataTransfer.getData("text"))
    data.draggable=false
    document.getElementById("ver").value=document.getElementById("ver").value+data.dataset.id
    document.getElementById("dropbox").appendChild(data)
}