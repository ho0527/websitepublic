<!DOCTYPE html>
<html lang="en">
<?php
    session_start();
    include("link.php");
    $row=$db->query("SELECT `id`, `name`, `email`, `tel`, `msg`, `img`, `createdate`, `editdate`, `isdel`, `msgnum` FROM `message`")->fetchall();
    $rowid=$db->query("SELECT `id` FROM `message`")->fetchall();
    $lenOffFile=count($rowid);
    if(!isset($_SESSION['passcode'])){
        $_SESSION['passcode']=""; 
    }
    $i=0;
    // for($i=0;$i<$lenOffFile;$i++){
        
    // }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>玩家留言</title>
    <link rel="stylesheet" href="../../../bootstrap/bootstrap.css">
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
                            <input type="button" value="新增留言" id="NewMsg" onclick="apear2()" class="col-2 offset-1 btn btn-secondary">
                            <h1 class="col-6 t-c">玩家留言列表</h1>
                            <input type="button" value="玩家留言管理" onclick="location.href='index.php'" id="ControlMsg" class="col-2 btn btn-secondary">
                        </div>
                    </div>

                    <div class="card-body">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 以上是留言列表 -->
    <!-- 以下是新增留言功能 -->
    <div class="container" id="p2">
        <div class="row">
            <div class="card shadow p-3 col-12">
                <div class="card-title row">
                    <h2 class="col-6 offset-3 t-c">玩家留言-新增</h2>
                    <input type="button" class="btn btn-secondary col-3" onclick="apear1()" value="回留言列表">
                </div>
                <form action="newmsg.php" method="post" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row my-5">
                            <div class="col-2 sbd t-c cw bg-b p-1" id="inpblock"><h2>姓名</h2></div>
                            <label class="form-group">
                                <input type="text" class="form-control col-6 offset-4 sbd mt-2" name="name" id="inpblocks">
                            </label>
                        </div>
                        <div class="row my-5">
                            <div class="col-2 sbd t-c cw bg-b p-1" id="inpblock"><h2>E-mail</h2></div>
                            <label class="form-group">
                                <input type="text" class="form-control col-6 offset-4 sbd mt-2" name="email" id="inpblocks">
                            </label>
                        </div>
                        <div class="row my-5">
                            <div class="col-2 sbd t-c cw bg-b p-1" id="inpblock"><h2>電話</h2></div>
                            <label class="form-group">
                                <input type="text" class="form-control col-6 offset-4 sbd mt-2" name="tel" id="inpblocks">
                            </label>
                        </div>
                        <div class="row my-5">
                            <div class="col-2 sbd t-c cw bg-b p-1" id="inpblock"><h2>留言內容</h2></div>
                            <label class="form-group">
                                <input type="text" class="form-control col-6 offset-4 sbd mt-2" name="msg" id="inpblockss">
                            </label>
                            <!-- 下面是"圖片上傳"的btn -->
                            <input type="file" name="picture" id="updateimg" value="上傳頭像" accept="image/*" name="img" class="btn btn-secondary">
                            <input type="button" value="圖片上傳" class="col-2" onclick="filebtn()">
                        </div>
                        <div class="row my-5">
                            <div class="col-2 sbd t-c cw bg-b p-1" id="inpblock"><h2>留言序號</h2></div>
                            <label class="form-group">
                                <input type="text" class="form-control col-4 offset-4 sbd mt-2" name="msgnum" id="inpblock">
                            </label>
                            <input type="submit" class="col-3" value="送出">
                            <input type="reset" class="col-3 offset-1" value="重設">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- 以上是新增留言功能 -->
    <div class="container" id="p3">
        <div class="card shadow p-2">
            <div class="card-head row">
                <h2 class="offset-2 col-8 t-c">玩家留言-編輯</h2>
                <input type="button" class="sbd bg-b col-2 cw" value="回留言列表" onclick="apear1()">
            </div>
        </div>
    </div>
    <!-- 以下是js區塊 -->
    <script>
        function disapear(){
            document.getElementById("p1").style.display="none"
            document.getElementById("p2").style.display="none"
            document.getElementById("p3").style.display="none"
        }
        function apear1(){
            disapear()
            document.getElementById("p1").style.display="block"
        }
        function apear2(){
            disapear()
            document.getElementById("p2").style.display="block"
        }
        function apear3(){
            disapear()
            document.getElementById("p3").style.display="block"
        }
        apear1()
        let passcode="<?= $_SESSION['passcode']; ?>"
        if(passcode=="pass"){
            apear3()
            <?php
                unset($_SESSION['passcode']);
            ?>
        }
        function filebtn(){
            document.getElementById("updateimg").click()
        }
    </script>
    <!-- 以上是js區塊 -->
</body>
</html>