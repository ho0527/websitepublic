document.querySelectorAll(".verifycode").forEach(function(event){
    event.ondragstart=function(event2){
        event2.dataTransfer.setData("text",event.id)
    }
})

document.getElementById("dropbox").ondragover=function(event){
    event.preventDefault()
}

document.getElementById("dropbox").ondrop=function(event){
    document.getElementById(event.dataTransfer.getData("text")).draggable=false
    document.getElementById("ver").value=document.getElementById("ver").value+document.getElementById(event.dataTransfer.getData("text")).dataset.id
    document.getElementById("dropbox").appendChild(document.getElementById(event.dataTransfer.getData("text")))
}