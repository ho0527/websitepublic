<!-- html架構不解釋 -->
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>新增留言</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="box">
            <h2>玩家留言-新增<button onclick="location.href='message.php'">回留言列表</button><br></h2>
            <!-- 表單不解釋 -->
            <form method="post">
                <div class="div">姓名</div><input type="text" class="input" name="username"><br><br>
                <div class="div">E-mail</div><input type="text" placeholder="需要填一個@和一個." class="input" name="email"><br><br>
                <div class="div">電話</div><input type="text" placeholder="只能填0~9" class="input" name="phone"><br><br>
                <div class="div">留言內容</div><input type="text" class="input" name="comment">
                <input type="file" name="file" id="" accept="image/*"><br><br>
                留言序號:<input type="text" name="number"><br><br>
                <button type="submit" name="submit">送出</button>
                <button type="reset">重設</button>
                <?php
                    // 當送出後
                    if(isset($_POST["submit"])){
                        mail("chris960527ho@gmail.com","test",$_POST["username"],"From: chris960527ho@gmail.com");
                    }
                ?>
            </form>
        </div>
    </body>
</html>