let verifyans
let bigorsmall

function main(){
    verifyans=[]
    document.getElementById("verifycode").innerHTML=``
    document.getElementById("drop").innerHTML=``
    bigorsmall=0
    document.getElementById("bigorsmall").innerHTML=`請託動驗證碼'由大排到小'`
    if(Math.floor(Math.random()*2)){
        document.getElementById("bigorsmall").innerHTML=`請託動驗證碼'由小排到大'`
        bigorsmall=1
    }

    for(let i=0;i<4;i=i+1){
        let small="qwertyuiopasdfghjklzxcvbnm"
        let big=small.toUpperCase()
        let number="0123456789"
        let wordlist=small+big+number
        let word=wordlist[Math.floor(Math.random()*62)]
        document.getElementById("verifycode").innerHTML=`
            ${document.getElementById("verifycode").innerHTML}
            <span class="code">
                <img src="api/verifycode.php?str=${word}" class="img" id="${i}" data-id="${word}">
            </span>
        `
        verifyans.push(word)
    }

    if(bigorsmall){
        verifyans.sort()
    }else{
        verifyans.sort().reverse()
    }

    verifyans=verifyans.join("")

    document.querySelectorAll(".img").forEach(function(event){
        event.ondragstart=function(event2){
            event2.dataTransfer.setData("a",event2.target.id)
        }
    })

    document.getElementById("drop").ondragover=function(event){
        event.preventDefault()
    }

    document.getElementById("drop").ondrop=function(event){
        event.preventDefault()
        let id=event.dataTransfer.getData("a")
        document.getElementById("drop").appendChild(document.getElementById(id))
    }
}

main()

function ref(){
    main()
}

function cacle(){
    location.reload()
}

function login(){
    let username=document.getElementById("username").value
    let password=document.getElementById("password").value
    let ans=""
    document.querySelectorAll("#drop>.img").forEach(function(event){
        ans=ans+event.dataset.id
    })
    location.href="login.php?username="+username+"&password="+password+"&verifyans="+verifyans+"&ans="+ans
}
