let id

if(localStorage.getItem("username")){
    document.getElementById("username").value=localStorage.getItem("53regionals1username")
}
if(localStorage.getItem("password")){
    document.getElementById("password").value=localStorage.getItem("53regionals1password")
}

document.getElementById("ref").onclick=function(){
    localStorage.setItem("53regionals1username",document.getElementById("username").value)
    localStorage.setItem("53regionals1password",document.getElementById("password").value)
    location.reload()
}

document.getElementById("ref2").onclick=function(){
    localStorage.removeItem("53regionals1username")
    localStorage.removeItem("53regionals1password")
    location.reload()
}

document.querySelectorAll(".verifycodeimage").forEach(function(event){
    event.ondragstart=function(){
        id=this.id
    }
})

document.getElementById("drop").ondragover=function(event){
    event.preventDefault()
}

document.getElementById("drop").ondrop=function(event){
    let data=""

    document.getElementById("drop").appendChild(document.getElementById(id))

    document.querySelectorAll("#drop>.verifycodeimage").forEach(function(event2){
        data=data+event2.dataset.id
    })

    document.getElementById("ans").value=data
}