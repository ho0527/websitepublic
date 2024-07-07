<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="bootstrap.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
        ?>
        <div class="nav" id="nav">
            <div class="title">訪客訂房 - 已選擇的訂房資訊再確認</div>
        </div>

        <div class="maindiv" id="main">
            <div class="div">
                訂房間數: 1
            </div><br>
            <div class="div">
                入住天數
                <div id="totaldate">請先選擇日期</div>
            </div><br>
            <div class="div">
                入住日期
                <div id="startday">請先選擇日期</div>
            </div>
            <div class="div">
                ~
                <div id="endday">請先選擇日期</div>
            </div><br>
            <div class="div">
                房間號碼
                <div id="room">請先選擇日期</div>
            </div><br>
            <div class="div">
                總金額
                <div id="price">請先選擇日期</div>
            </div><br>
            <div class="div">
                需付討金
                <div id="decp">請先選擇日期</div>
            </div><br>
            <div class="div text-center">
                <input type="button" class="btn btn-light" onclick="location.href=localStorage.getItem('booklocation')" value="取消">
                <input type="button" class="btn btn-warning" onclick="location.href='bookroomsubmit.php'" value="確定訂房">
            </div>
        </div>

        <script src="init.js"></script>
        <script src="bookroomcheck.js"></script>
    </body>
</html>