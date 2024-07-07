function ref(){
    let data=[]
    document.getElementById("div").innerHTML=`
        圖片驗證碼<br>
    `
    for(let i=0;i<4;i=i+1){
        let rand=Math.floor(Math.random()*9)
        document.getElementById("div").innerHTML+=`
            <img src="verifycode.php?str=${rand}" draggable="false">
        `
        data.push(rand)
    }
    data.sort()
    document.getElementById("ans").value=""
    document.getElementById("verifycode").value=data.join("")
}

ref()