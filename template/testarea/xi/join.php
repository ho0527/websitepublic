<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>玩家參賽</title>
    <link rel="stylesheet" href="../../../bootstrap/bootstrap.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .card{
            background-color: lightgray !important;
        }
        .form-control{
            width:300px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg justify-content-between navbar-light bg-primary fixed-top">
        <div class="navbar-brand"><img src="logo.png" height="50px" alt=""></div>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="chat.php" class="nav-link"><p class="line" id="nonec">|</p>玩家留言</a>
            </li>
            <li class="nav-item">
                <a href="join.php" class="nav-link active"><p class="line">|</p>玩家參賽</a>
            </li>
            <li class="nav-item">
                <a href="m.php" class="nav-link"><p class="line" id="nonec">|</p>網站管理</a>
            </li>
        </ul>
    </nav>
    <img src="banner.png" class="bg-img" alt="">
    <br><br>
    <form action="sforjoin.php" method="post" enctype="multipart/form-data">
        <div class="container martop-10">
            <div class="row">
                <div class="col-6 offset-3">
                    <div class="card shadow" id="join-card">
                        <div class="card-head t-c">
                            <h2>玩家參賽畫面</h2>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-4 t-c" id="black-part">
                                <h3>姓名</h3>
                            </div>
                            <label class="form-group">
                                <input type="text" name="name" id="name" class="form-control" placeholder="您的姓名">
                            </label>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-4 t-c" id="black-part">
                                <h3>E-mail</h3>
                            </div>
                            <label class="form-group">
                                <input type="text" name="email" id="email" class="form-control" placeholder="您的E-mail(得包含@和.各至少一個)">
                            </label>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-4 t-c" id="black-part">
                                <h3>電話</h3>
                            </div>
                            <label class="form-group">
                                <input type="text" name="tel" id="tel" class="form-control" placeholder="您的電話號碼(可以輸入-及數字)">
                            </label>
                        </div>
                        <br>
                        <div class="row mb-2">
                            <input type="file" name="picture" id="updateimg" value="上傳頭像" class="btn btn-secondary">
                            <input type="button" name="picture" onclick="filebtn()" class="btn btn-secondary offset-1" value="上傳頭像">
                            <input type="submit" name="btn-2" id="" value="參賽" class="btn btn-secondary offset-3">
                            <input type="reset" name="btn-3" id="btn-3" value="重設" class="btn btn-secondary offset-2">
                        </div>
                        <script>
                            function filebtn(){
                                document.getElementById("updateimg").click()
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <br><br><br><br><br>
</html>