<?php
    session_start();
    include("link.php");
    $id=$_SESSION["id-for-edit"];
    $row=$db->query("SELECT*FROM `message`")->fetchall();
    $name=$_POST['name'];
    $email=$_POST['email'];
    $tel=$_POST['tel'];
    $msg=$_POST['msg'];
    if(!preg_match("/[\\\,\`,\~,\!,\#,\$,\%,\^,\&,\*,\(,\),\_,\+,\=,\[,\],\{,\},\:,\;,\?,\>,\<,\/,\|,\,]/",$name.$email.$tel)){
        if(preg_match("/^.+\@.+\..+((\..+)+)?$/",$email)){
            if(preg_match("/^[0-9]+((\ -[0-9])+)?$/",$tel)){
                $db->query("UPDATE `message` SET `name`='$name',`email`='$email',`tel`='$tel',`msg`='$msg',`editdate`='$date' WHERE `id`='$id'");// bug
                ?>
                    <script>
                        alert("修改成功!");location.href="chat.php";
                    </script>
                <?php
            }else{
                ?>
                    <script>
                        alert("電話號碼錯誤.");location.href="chat.php"
                    </script>
                <?php
            }
        }else{
            ?>
                <script>
                    alert("電子郵件錯誤.");location.href="chat.php"
                </script>
            <?php
        }
    }else{
        ?>
            <script>
                alert("帳號,密碼,電話號碼中不能有特殊字元.");location.href="chat.php"
            </script>
        <?php
    }

?>