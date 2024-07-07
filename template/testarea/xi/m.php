<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>網站管理</title>
    <link rel="stylesheet" href="../../../bootstrap/bootstrap.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .card{
            border-radius: 40px !important;
        }
        .form-control{
            border: 1px solid black !important;
            width: 400px;
        }
        .card-head{
            border-bottom: black dashed 3px;
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
                <a href="join.php" class="nav-link"><p class="line" id="nonec">|</p>玩家參賽</a>
            </li>
            <li class="nav-item">
                <a href="m.php" class="nav-link active"><p class="line">|</p>網站管理</a>
            </li>
        </ul>
    </nav>
    <img src="banner.png" class="bg-img" alt="">
    <br><br>
    <div class="container">
        <div class="row" id="hbroad">
            <div class="col-8 offset-2">
                <div class="card shadow p-3">
                    <form action="submit.php" method="post">
                        <div class="card-head">
                            <h3 class="t-c">網站管理--登入</h3>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-4">
                                <h3 class="t-c">帳號</h3>
                            </div>
                            <div class="col-8">
                                <label class="form-group">
                                    <input type="text" class="form-control" name="acount" id="acount">
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <h3 class="t-c">密碼</h3>
                            </div>
                            <div class="col-8">
                                <label class="form-group">
                                    <input type="text" class="form-control" name="password" id="password">
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <h3 class="t-c">驗證碼</h3>
                            </div>
                            <div class="col-8">
                                <label class="form-group">
                                    <input type="text" class="form-control" name="verification" id="verification">
                                </label>
                            </div>
                        </div>
                        <br>
                    <div class="row mb-2">
                        <div class="col-2 offset-5">
                            <p name="ver" id="verifi" class="mt-3"></p>
                            <input type="hidden" name="ver" id="ver">
                            <script>
                                function rn(){
                                    let randnum=Math.floor(Math.random()*(9999-1111+1))+1111
                                    document.getElementById("ver").value=randnum
                                    document.getElementById("verifi").innerHTML=randnum
                                }
                                rn()
                                function restart(){
                                    document.getElementById("verification").value=''
                                    rn()
                                }
                            </script>
                        </div>
                        <input type="button" class="btn btn-secondary col-3 offset-1 " id="reset-randnum-btn" onclick="restart()" value="驗證碼重新產生">
                    </div>
                <div class="row">
                    <div class="col-3 offset-4">
                        <input type="submit" id="submit-btn" class="btn btn-dark" value="送出">
                    </div>
                    <div class="col-3 offset-2">
                        <input type="reset" id="reset-btn" class="btn btn-dark" value="重設">
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <br><br><br><br><br><br><br><br><br><br>
</body>
</html>