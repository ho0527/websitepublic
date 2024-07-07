function ref(){
    document.getElementById("verifycode").value=Math.floor(Math.random()*8999)+1000
    document.getElementById("ans").value=""
}