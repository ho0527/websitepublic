let file=location.href.split("/")[location.href.split("/").length-1].split("?")[0]

if(file==""){
    location.href="index.php"
}

document.getElementById("nav").innerHTML=`
    ${document.getElementById("nav").innerHTML}
    <div></div>
    <div class="navdiv">
        <input type="button" class="btn-primary" id="index.php" onclick="location.href='index.php'" value="首頁">
        <input type="button" class="btn-primary" id="comment.php" onclick="location.href='comment.php'" value="訪客留言">
        <input type="button" class="btn-primary" id="bookroom.php" onclick="location.href='bookroom.php'" value="訪客訂房">
        <input type="button" class="btn-primary" id="orderfood.php" onclick="location.href='orderfood.php'" value="訪客訂餐">
        <input type="button" class="btn-primary" id="info.php" onclick="location.href='info.php'" value="交通資訊">
        <input type="button" class="btn-primary" id="signin.php" onclick="location.href='signin.php'" value="網站管理">
    </div>
`

document.getElementById(file).classList.add("active")