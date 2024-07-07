let dragimg=document.querySelectorAll(".dragimg")
let drops=document.querySelectorAll("#dropbox")
let a=[]
let b=["","",""]

dragimg.forEach(function(dragimgs){
    dragimgs.addEventListener("dragstart",dragstart)
})

drops.forEach(function(dropbox){
    dropbox.addEventListener("dragover",drag)
    dropbox.addEventListener("dragenter",drag)
    dropbox.addEventListener("dragleave",drag)
    dropbox.addEventListener("drop",drop)
})

function dragstart(e){
    e.dataTransfer.setData("text",e.target.id)
}

function drag(e){
    e.preventDefault()
}

function drop(e){
    let id=e.dataTransfer.getData("text")
    let draggable=document.getElementById(id)
    a.push(id)
    e.target.appendChild(draggable)
}

function loginclick(key){
    let username=document.getElementById("username").value
    let code=document.getElementById("code").value
    for(let i=0;i<3;i=i+1){
        b[i]=a[i]
    }
    if(key==0){
        b.sort()
        let temp=b[0]
        b[0]=b[2]
        b[2]=temp
        if(JSON.stringify(a)==JSON.stringify(b)){
            location.href="login.php?username="+username+"&code="+code
        }else{
            location.href="login.php?vererror=&username="+username+"&code="+code
        }
    }else{
        b.sort()
        if(JSON.stringify(a)==JSON.stringify(b)){
            location.href="login.php?username="+username+"&code="+code
        }else{
            location.href="login.php?vererror=&username="+username+"&code="+code
        }
    }
}