<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>A06</title>
    </head>
    <body>
        <form>
            <!-- Display the captcha image -->
            <?php
                $verify="";//定訂字串
                for($i=0;$i<4;$i++){
                    $str=array_merge(range(2,9),range("A","Z"))[rand(0,33)];//將文字加入字串
                    $verify=$verify.$str;
                }
            ?>
            <img src="verifycode.php?val=<?= $verify ?>" alt="Captcha image"><input type="button" onclick="location.reload()" value="重新產生"><br>
            <!-- Captcha code input field -->
            輸入驗證碼:<input type="text" name="verifycode" id="verifycode"><br>
            <input type="hidden" name="verify" value="<?= $verify ?>">
            <!-- Submit button -->
            <input type="submit" name="submit" value="送出">
        </form>
        <?php
            if(isset($_GET["submit"])){
                //判斷輸入值是否與SESSION值相同
                if($_GET["verifycode"]==$_GET["verify"]){
                    //做成功圖片
                    ?>
                    <p style="font-size:48px;font-weight:bold;color:#008800">成功</p>
                    <?php
                }else{
                    // 做失敗圖片
                    ?>
                    <p style="font-size:48px;font-weight:bold;color:#880000">失敗</p>
                    <?php
                }
            }
        ?>
    </body>
</html>
