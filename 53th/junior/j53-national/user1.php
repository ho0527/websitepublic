<?php
include("link.php");
$site = $pdo->query("SELECT * FROM `site` WHERE 1")->fetchAll();
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
            </script>
        </div>
    </div>
    <div class="border p-3" style="width: 1200px;position: relative;left: 20%;top: 40px;">
        <input type="button" class="btn btn-outline-secondary mx-2" onclick="location='user.php'" value="接駁車管理">
        <input type="button" class="btn btn-secondary mx-2" onclick="location='user1.php'" value="站點管理">
    </div>
    <div class="border p-3 my-3" style="width: 1200px;position: relative;left: 20%;top: 40px;">
        <div class="d-flex" style="position: relative;width: 500px;left: 40%;">
            <h1 class="mx-3">站點管理</h1>
            <input type="button" class="btn btn-success" onclick="location='./site.html'" value="新增">
        </div>
    </div>
    <div style="width: 1200px;position: absolute;left: 20%;top: 35%;overflow-y: auto;height: 550px;">
        <table class="table table-bordered">
            <thead class="bg-primary text-white" style="z-index: 100;position: sticky;top: 0%;">
                <tr>
                    <th class="text-center">站點名稱</th>
                    <th class="text-center">行駛時間(分鐘)</th>
                    <th class="text-center">停留時間(分鐘)</th>
                    <th class="text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($site as $travel) {
                ?>
                    <tr>
                        <td><?=$travel["site"]?></td>
                        <td><?=$travel["travel"]?></td>
                        <td><?=$travel["stop"]?></td>
                        <td>
                            <button style="margin-right: 30px;" onclick="location='changesite.php?change=<?= $travel["id"] ?>'" class="btn btn-warning">編輯</button>
                            <button class="btn btn-danger" onclick="bbb(<?=$travel["id"]?>)">刪除</button>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
<script>
    function bbb(id) {
        if (confirm("確認刪除")) {
            alert("刪除成功")
            location = "./deletesite.php?delete=" + id
        } else {
            alert("刪除失敗")
        }
    }
</script>

</html>