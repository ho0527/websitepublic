let todo=document.getElementById("todo")
let move=false
let check=false

todo.addEventListener("mousedown",function(){
    if(move==false)
        move=true
})

todo.addEventListener("mousemove",function(){
    if(move==true)
        check=true
})

todo.addEventListener("mouseup",function(){
    if(check==true)
        location.href="useradd.php"
})

let upusertablediv=document.querySelectorAll(".useruptablediv")
let downusertablediv=document.querySelectorAll(".userdowntablediv")
let boxid
var box
document.querySelectorAll('.todobox').forEach((e)=>{
    e.addEventListener("mousedown",function(){
        boxid=this.id
        console.log(boxid)
        down=false
        move=false
        box=document.getElementById(boxid).addEventListener("dragstart",dragstart)
    })
})

upusertablediv.forEach((up)=>{
    up.addEventListener("dragenter",dragenter)
    up.addEventListener("dragover",dragover)
    up.addEventListener("dragleave",dragleave)
    up.addEventListener("drop",updrop)
})

downusertablediv.forEach((down)=>{
    down.addEventListener("dragenter",dragenter)
    down.addEventListener("dragover",dragover)
    down.addEventListener("dragleave",dragleave)
    down.addEventListener("drop",downdrop)
})

function dragstart(e){
    e.dataTransfer.setData("text",boxid)
    console.log("in")
}

function dragenter(e){
    e.preventDefault()
    e.target.classList.add("dragover")
    console.log("inin")
}

function dragover(e){
    e.preventDefault()
    e.target.classList.add("dragover")
    console.log("ininin")
}

function dragleave(e){
    e.preventDefault()
    e.target.classList.remove("dragover")
    console.log("inininin")
}

function updrop(e){
    console.log("ininininin")
    e.target.classList.remove("dragover")
    let id=e.dataTransfer.getData("text")
    let box=document.getElementById(id)
    e.target.appendChild(box)
    box.style.top="0px"
    box.style.left="10px"
    let height=box.style.height
}


function downdrop(e){
    e.preventDefault()
    e.target.classList.remove("dragover")
    const id=e.dataTransfer.getData("text")
    const box=document.getElementById(id)
    e.target.appendChild(box)
    box.style.top="0px"
    box.style.left="10px"
    let height=box.style.height
}