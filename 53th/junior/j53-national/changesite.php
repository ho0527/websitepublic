<?php
    include("link.php");
    if(isset($_GET["change"])){
        $id = $_GET["change"];
        $site = $pdo->query("SELECT * FROM `site` WHERE `id`=$id")->fetch();
        if(!$id){
            ?><script>alert("無此倆公車");location="./user1.php"</script><?php
        }
    }else{

    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="bootstrap.css">
    <link rel="stylesheet" href="style.css">
    <script src="jquery.js"></script>
</head>

<body>
    <div class="p-3 d-flex border-bottom">
    <img style="height: 60px;cursor: pointer;" onclick="location='index.php'" src="./picture/123456.png" alt="">
        <h1 class="mx-3 m-1 strong">南港展覽館接駁專車系統</h1>
        <div style="position: absolute;right:0% ">
            <h3 onclick="location='./user.php'" class="m-3 btn btn-outline-primary">系統管理</h3>
            <h3 onclick="out()" class="m-3 btn btn-outline-primary">登出</h3>
            <script>
                function out(){
                    location="./login.html"
                    alert("登出成功")
                }
            </script>        </div>
    </div>
    <div class="border p-3" style="width: 1200px;position: relative;left: 20%;top: 40px;">
    <input type="button" class="btn btn-outline-secondary mx-2" onclick="location='user.php'" value="接駁車管理">
        <input type="button" class="btn btn-secondary mx-2" onclick="location='user1.php'" value="站點管理">
    </div>
    <div class="border p-3 my-3" style="width: 1200px;position: relative;left: 20%;top: 40px;">
        <div class="d-flex" style="position: relative;width: 500px;left: 30%;">
            <h1 class="mx-3">修改「<?=$site["site"]?>」接駁車</h1>
        </div>
    </div>
    <form action="change2.php" method="post" id="form">
        <div style="width: 1200px;position: relative;top: 100px;left: 20%;">
            <div class="d-flex my-3">
                <label for="travel" style="font-size: 30px;" class="w-25">已行駛時間(分鐘)</label>
                <input type="text" class="w-75 form-control" id="travel" name="travel" value="<?=$site["travel"]?>">
                <input type="hidden" class="w-75 form-control" id="id" name="id" value="<?=$site["id"]?>">
            </div>
            <div class="d-flex my-3">
                <label for="stop" style="font-size: 30px;" class="w-25">停留時間(分鐘)</label>
                <input type="text" class="w-75 form-control" id="stop" name="stop" value="<?=$site["stop"]?>">
            </div>
            <input type="button" class="btn btn-success w-100 my-3" onclick="aaa()" value="送出" id="">
            <input type="button" class="btn btn-secondary w-100 my-3" onclick="location='./user1.php'" value="回上頁" id="">
        </div>
    </form>
</body>
<script>
    function aaa(){
        alert("修改成功")
        document.getElementById("form").submit()
}
</script>
</html>