<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <link rel="stylesheet" href="bootstrap.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg justify-content-between navbar-light bg-primary fixed-top">
        <div class="navbar-brand"><img src="logo.png" height="50px" alt=""></div>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="chat.php" class="nav-link active"><p class="line">|</p>玩家留言</a>
            </li>
            <li class="nav-item">
                <a href="join.php" class="nav-link"><p class="line" id="nonec">|</p>玩家參賽</a>
            </li>
            <li class="nav-item">
                <a href="m.php" class="nav-link"><p class="line" id="nonec">|</p>網站管理</a>
            </li>
        </ul>
    </nav>
    <img src="banner.png" class="bg-img" alt="">
    <div class="container" id="p1">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-title bg-card-g">
                        <div class="row my-2">
                            <!-- <input type="button" value="新增留言" id="NewMsg" onclick="apear3()" class="col-2 offset-1 btn btn-secondary"> -->
                            <h1 class="col-12 t-c">玩家留言版區塊</h1>
                            <!-- <input type="button" value="玩家留言管理" id="ControlMsg" class="col-2 btn btn-secondary"> -->
                        </div>
                    </div>
                    <!-- <div class="card-body">
                        <div class="row">
                            <div>
                                <div><?php echo($row)[$i][1] ?></div>
                                <div><?php echo($row)[$i][4] ?></div>
                                <div>發表於：<?php echo($row)[$i][6] ?>&nbsp;&nbsp;&nbsp;修改於：<?php echo($row)[$i][7] ?></div>
                                <div>E-mail：<?php echo($row)[$i][2] ?>&nbsp;&nbsp;&nbsp;電話：<?php echo($row)[$i][3] ?></div>
                            </div>
                            <div>
                                <form action="edit.php" method="post">
                                    <h3>留言序號</h3>
                                    <input type="number" name="snum" id="snum">
                                    <input type="submit" name="edit" value="編輯">
                                    <input type="submit" name="del" value="刪除">
                                </form>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
    <div class="container" id="p2">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-title bg-card-b">
                        <div class="row my-2">
                            <h1 class="col-6 offset-3 cw t-c">最新消息與賽制公告區塊</h1>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <img src="banner.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>