let file=location.href.split("/")[location.href.split("/").length-1].split("?")[0]

if(file!="bookroom.php"&&file!="bookroomroom.php"&&file!="bookroomcheck.php"&&file!="bookroomsubmit.php"){
    localStorage.removeItem("book")
    localStorage.removeItem("location")
}

if(document.getElementById("nav")){
    document.getElementById("nav").innerHTML=`
        ${document.getElementById("nav").innerHTML}
        <div class="btn-group">
            <a href="index.php" class="btn btn-outline-light">首頁</a>
            <a href="comment.php" class="btn btn-outline-light">訪客留言</a>
            <a href="bookroom.php" class="btn btn-outline-light">訪客訂房</a>
            <a href="orderfood.php" class="btn btn-outline-light">訪客訂餐</a>
            <a href="info.php" class="btn btn-outline-light">交通資訊</a>
            <a href="signin.php" class="btn btn-outline-light">網站管理</a>
        </div>
    `
}else{
    document.getElementById("adminnav").innerHTML=`
        ${document.getElementById("adminnav").innerHTML}
        <div class="btn-group">
            <a href="index.php" class="btn btn-outline-light">首頁</a>
            <a href="comment.php" class="btn btn-outline-light">訪客留言</a>
            <a href="bookroom.php" class="btn btn-outline-light">訪客訂房</a>
            <a href="orderfood.php" class="btn btn-outline-light">訪客訂餐</a>
            <a href="info.php" class="btn btn-outline-light">交通資訊</a>
            <a href="api.php?signout=" class="btn btn-outline-light">登出</a>
        </div>
    `
}