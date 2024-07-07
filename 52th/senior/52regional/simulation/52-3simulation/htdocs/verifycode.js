let drag=document.querySelectorAll(".drag")
let drop=document.querySelectorAll("#dropbox")
let a=[]
let b=["","",""]

drag.forEach((drags)=> {
    drags.addEventListener("dragstart",drs)
})
drop.forEach((drops)=> {
    drops.addEventListener("dragover",dr)
    drops.addEventListener("dragenter",dr)
    drops.addEventListener("dragleave",dr)
    drops.addEventListener("drop",dropf)
})

function drs(e){
    e.dataTransfer.setData("text",e.target.id)
}

function dr(e){
    e.preventDefault()
}

function dropf(e){
    let id=e.dataTransfer.getData("text")
    let draggable=document.getElementById(id)
    a.push(id)
    e.target.appendChild(draggable)
}

function login(key){
    let user=document.getElementById("user").value
    let code=document.getElementById("code").value
    for(let i=0;i<3;i++){
        b[i]=a[i]
    }
    console.log(b)
    console.log(a)
    if(key==0){
        b.sort()
        temp=b[0]
        b[0]=b[2]
        b[2]=temp
        if(JSON.stringify(a)==JSON.stringify(b)){
            location.href="login.php?submit=&username="+user+"&pass="+code
        }else{
            location.href="login.php?submit=&vererror=&username="+user+"&pass="+code
        }
    }else{
        b.sort()
        if(JSON.stringify(a)==JSON.stringify(b)){
            location.href="login.php?submit=&username="+user+"&pass="+code
        }else{
            location.href="login.php?submit=&vererror=&username="+user+"&pass="+code
        }
    }
}