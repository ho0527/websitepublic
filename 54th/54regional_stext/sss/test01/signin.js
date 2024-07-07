document.querySelectorAll(".vercode").forEach(function(event){
    event.ondragstart=function(event2){
        event2.dataTransfer.setData("text",event.id)
    }
})

document.getElementById("dropbox").ondragover=function(event){
    event.preventDefault()
}

document.getElementById("dropbox").ondrop=function(event){
    let id=event.dataTransfer.getData("text")
    event.preventDefault()
    document.getElementById("ver").value=document.getElementById("ver").value+document.getElementById(id).dataset.id
    document.getElementById(id).draggable="false"
    document.getElementById("dropbox").appendChild(document.getElementById(id))
}