<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Madmin</title>
    <link rel="stylesheet" href="../../../bootstrap/bootstrap.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg justify-content-between navbar-light bg-primary fixed-top">
    <div class="navbar-brand">Welcome to Shanghai Battle!</div>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="chat.php" class="nav-link" style="color: white;"><p class="poncolor" id="nocolor">|</p>玩家留言</a>
            </li>
            <li class="nav-item">
                <a href="join.php" class="nav-link" style="color: white;"><p class="poncolor" id="nocolor">|</p>玩家參賽</a>
            </li>
            <li class="nav-item">
                <a href="m.php" class="nav-link active" style="color: white;"><p class="poncolor">|</p>網站管理</a>
            </li>
        </ul>
    </nav>
    <img src="背景.png" style="width: 1920px;" alt="">
    <br><br>
    <div class="container">
        <div class="row">
            <div class="col-6 offset-3">
                <div class="row both-btn">
                    <input type="button" class="btn btn-light col-5" id="managechat" value="留言管理">
                    <input type="button" class="btn btn-light col-5" id="managerace" value="賽制管理">
                </div>
                <div class="row" id="item-in-space">
                    <h3 id="text-in-space">網站管理內容</h3>
                </div>
            </div>
        </div>
    </div>
    <br><br><br><br><br>
    <script>
        function fofmanagechat(){
            disapear()
            document.getElementById("p1").style.display="block"
        }
        function fofmanagerace(){
            disapear()
            document.getElementById("p2").style.display="block"
        }
        function disapear(){
            document.getElementById("text-in-space").style.display="none"
            document.getElementById("p1").style.display="none"
            document.getElementById("p2").style.display="none"
        }
        
    </script>
</body>
</html>