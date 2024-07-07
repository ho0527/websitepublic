let file=location.href.split("/")[location.href.split("/").length-1].split("?")[0]

if(file==""){
    location.href="index.php"
}

if(document.getElementById("nav")){
    document.getElementById("nav").innerHTML=`
        ${document.getElementById("nav").innerHTML}
        <div class="btn-group">
            <input type="button" class="btn btn-outline-light" id="index.php" onclick="location.href='index.php'" value="首頁">
            <input type="button" class="btn btn-outline-light" id="comment.php" onclick="location.href='comment.php'" value="訪客留言">
            <input type="button" class="btn btn-outline-light" id="bookroom.php" onclick="location.href='bookroom.php'" value="訪客訂房">
            <input type="button" class="btn btn-outline-light" id="orderfood.php" onclick="location.href='orderfood.php'" value="訪客訂餐">
            <input type="button" class="btn btn-outline-light" id="info.php" onclick="location.href='info.php'" value="交通資訊">
            <input type="button" class="btn btn-outline-light" id="signin.php" onclick="location.href='signin.php'" value="網站管理">
        </div>
    `

    document.getElementById(file).classList.add("active")
}else{
    document.getElementById("adminnav").innerHTML=`
        ${document.getElementById("adminnav").innerHTML}
        <div class="btn-group">
            <input type="button" class="btn btn-outline-light" id="index.php" onclick="location.href='index.php'" value="首頁">
            <input type="button" class="btn btn-outline-light" id="comment.php" onclick="location.href='comment.php'" value="訪客留言">
            <input type="button" class="btn btn-outline-light" id="bookroom.php" onclick="location.href='bookroom.php'" value="訪客訂房">
            <input type="button" class="btn btn-outline-light" id="orderfood.php" onclick="location.href='orderfood.php'" value="訪客訂餐">
            <input type="button" class="btn btn-outline-light" id="info.php" onclick="location.href='info.php'" value="交通資訊">
            <input type="button" class="btn btn-outline-light" id="signin.php" onclick="location.href='api.php?signout='" value="登出">
        </div>
    `
}

if(file!="bookroom.php"&&file!="bookroomsubmit.php"){
    localStorage.removeItem("book")
}