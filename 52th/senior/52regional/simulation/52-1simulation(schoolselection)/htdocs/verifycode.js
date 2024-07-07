let drag0=document.querySelector(".drag0").addEventListener("dragstart",dragstart)
let drag1=document.querySelector(".drag1").addEventListener("dragstart",dragstart)
let drag2=document.querySelector(".drag2").addEventListener("dragstart",dragstart)
let drops=document.querySelectorAll("#dropbox")
let a=[]
let b=["","",""]

drops.forEach(dropbox=>{
    dropbox.addEventListener("dragenter",dragenter)
    dropbox.addEventListener("dragover",dragenter)
    dropbox.addEventListener("dragleave",dragenter)
    dropbox.addEventListener("drop",drop)
})

function dragstart(e){
    e.dataTransfer.setData("text",e.target.id)
    console.log(e.target.id)
}

function dragenter(e){
    e.preventDefault()
}

function drop(e){
    let id=e.dataTransfer.getData("text")
    let draggable=document.getElementById(id)
    a.push(id)
    console.log(id)
    console.log(a)
    e.target.appendChild(draggable)
}

function ggg(key){
    for(i=0;i<3;i=i+1){
        b[i]=a[i]
    }
    if(key==0){
        b.sort()
        let temp=b[0]
        b[0]=b[2]
        b[2]=temp
        if(JSON.stringify(a)==JSON.stringify(b)){

        }else{
            alert("驗證碼輸入錯誤")
        }
    }else{
        b.sort()
        if(JSON.stringify(a)==JSON.stringify(b)){

        }else{
            alert("驗證碼輸入錯誤")
        }
    }
}