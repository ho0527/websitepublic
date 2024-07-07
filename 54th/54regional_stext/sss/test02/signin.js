document.querySelectorAll(".vercode").forEach(function(event){
    event.ondragstart=function(event2){
        event2.dataTransfer.setData("t",event.id)
    }
})

document.getElementById("dropbox").ondragover=function(event){
    event.preventDefault()
}

document.getElementById("dropbox").ondrop=function(event){
    event.preventDefault()
    document.getElementById("ans").value=document.getElementById("ans").value+document.getElementById(event.dataTransfer.getData("t")).dataset.id
    document.getElementById(event.dataTransfer.getData("t")).draggable="false"
    document.getElementById("dropbox").appendChild(document.getElementById(event.dataTransfer.getData("t")))
}