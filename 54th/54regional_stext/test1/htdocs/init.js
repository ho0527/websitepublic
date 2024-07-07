let file=location.href.split("/")[location.href.split("/").length-1].split("?")[0]

if(file==""){
    location.href="index.php"
}

if(document.getElementById("nav")){
    document.getElementById("nav").innerHTML=`
        <img src="icon.png" class="logo">
        <div class="right">
            <input type="button" class="btn-outline-primary" onclick="location.href='index.php'" id="index.php" value="首頁">
            <input type="button" class="btn-outline-primary" onclick="location.href='comment.php'" id="comment.php" value="訪客留言">
            <input type="button" class="btn-outline-primary" onclick="location.href='bookroom.php'" id="bookroom.php" value="訪客訂房">
            <input type="button" class="btn-outline-primary" onclick="location.href='orderfood.php'" id="orderfood.php" value="訪客訂餐">
            <input type="button" class="btn-outline-primary" onclick="location.href='transpion.php'" id="transpion.php" value="交通資訊">
            <input type="button" class="btn-outline-primary" onclick="location.href='signup.php'" id="signup.php" value="網站管理">
        <div>
    `

    document.getElementById(file).classList.add("active")
}else{
    document.getElementById("admnav").innerHTML=`
        <img src="icon.png" class="logo">
        <div class="right">
            <input type="button" class="btn-outline-primary" onclick="location.href='index.php'" id="index.php" value="首頁">
            <input type="button" class="btn-outline-primary" onclick="location.href='comment.php'" id="comment.php" value="訪客留言">
            <input type="button" class="btn-outline-primary" onclick="location.href='bookroom.php'" id="bookroom.php" value="訪客訂房">
            <input type="button" class="btn-outline-primary" onclick="location.href='orderfood.php'" id="orderfood.php" value="訪客訂餐">
            <input type="button" class="btn-outline-primary" onclick="location.href='transpion.php'" id="transpion.php" value="交通資訊">
            <input type="button" class="btn-outline-primary" onclick="location.href='api.php?logout='" id="signup.php" value="登出">
        <div>
    `
}