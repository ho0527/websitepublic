let file=location.href.split("/")[location.href.split("/").length-1].split("?")[0]

if(file!="bookroom.php"&&file!="bookroomroom.php"&&file!="bookroomsubmit.php"&&file!="bookroomcheck.php"){
    localStorage.removeItem("book")
    localStorage.removeItem("location")
}

if(document.getElementById("nav")){
    document.getElementById("nav").innerHTML+=`
        <div class="btn-group">
            <input type="button" class="btn btn-outline-light" onclick="location.href='index.php'" value="首頁">
            <input type="button" class="btn btn-outline-light" onclick="location.href='comment.php'" value="訪客留言">
            <input type="button" class="btn btn-outline-light" onclick="location.href='bookroom.php'" value="訪客訂房">
            <input type="button" class="btn btn-outline-light" onclick="location.href='orderfood.php'" value="訪客訂餐">
            <input type="button" class="btn btn-outline-light" onclick="location.href='info.php'" value="交通資訊">
            <input type="button" class="btn btn-outline-light" onclick="location.href='signin.php'" value="網站管理">
        </div>
    `
}else{
    document.getElementById("adminnav").innerHTML+=`
        <div class="btn-group">
            <input type="button" class="btn btn-outline-light" onclick="location.href='index.php'" value="首頁">
            <input type="button" class="btn btn-outline-light" onclick="location.href='comment.php'" value="訪客留言">
            <input type="button" class="btn btn-outline-light" onclick="location.href='bookroom.php'" value="訪客訂房">
            <input type="button" class="btn btn-outline-light" onclick="location.href='orderfood.php'" value="訪客訂餐">
            <input type="button" class="btn btn-outline-light" onclick="location.href='info.php'" value="交通資訊">
            <input type="button" class="btn btn-outline-light" onclick="location.href='api.php?signout='" value="登出">
        </div>
    `
}