let username=document.getElementById("username")
let password=document.getElementById("password")
let drags=document.querySelectorAll(".drag")
let drops=document.querySelectorAll("#dropbox")
let a=[]
let b=["","",""]

drags.forEach(function(dragbox){
    dragbox.addEventListener("dragstart",dragstart)
})

drops.forEach(function(dropbox){
    dropbox.addEventListener("dragover",drag)
    dropbox.addEventListener("dragenter",drag)
    dropbox.addEventListener("dragleave",drag)
    dropbox.addEventListener("drop",drop)
})

function dragstart(event){
    event.dataTransfer.setData("text",event.target.id)
    console.log(event.target.id)
}

function drag(event){
    event.preventDefault()
}
// array_push
function drop(event){
    console.log("in")
    event.preventDefault()
    let id=event.dataTransfer.getData("text")
    let draggable=document.getElementById(id)
    a.push(id)
    console.log(a)
    event.target.appendChild(draggable)
}

function login(key){
    for(let i=0;i<3;i=i+1){
        b[i]=a[i]
    }
    if(key==0){
        b.sort()
        let temp=b[0]
        b[0]=b[2]
        b[2]=temp
        console.log(b)
        if(JSON.stringify(b)==JSON.stringify(a)){
            location.href="login.php?username="+username.value+"&password="+password.value
        }else{
            location.href="login.php?vererror="
        }
    }else{
        b.sort()
        console.log(b)
        if(JSON.stringify(b)==JSON.stringify(a)){
            location.href="login.php?username="+username.value+"&password="+password.value
        }else{
            location.href="login.php?vererror="
        }
    }
}