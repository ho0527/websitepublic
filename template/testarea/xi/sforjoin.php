<?php
include("link.php");
if(isset($_POST['name'])){
    $name=$_POST['name'];
    $email=$_POST['email'];
    $tel=$_POST['tel'];
    if(!preg_match("/[\\\,\`,\~,\!,\#,\$,\%,\^,\&,\*,\(,\),\_,\+,\=,\[,\],\{,\},\:,\;,\?,\>,\<,\/,\|,\,]/",$email.$tel)){
        if(preg_match("/^[0-9]+((\-[0-9])+)?$/",$tel)){
            if(preg_match("/^.+\@.+\..+((\..+)+)?$/",$email)){
                $picture="";
                if(!empty($_FILES["picture"]["name"])){
                    $rand=rand(0,9999999999);
                    $str=str_pad($rand,10,"0",STR_PAD_LEFT);
                    move_uploaded_file($_FILES["picture"]["tmp_name"],"image/".$str);
                    $picture="image/".$str;
                }
                $db->query("INSERT INTO `message`(`name`,`email`,`tel`,`img`) VALUES ('$name','$email','$tel','$picture')");
                ?><script>alert("正確.");location.href="chat.php"</script><?php
            }else{
                ?><script>alert("電話號碼錯誤.");location.href="join.php"</script><?php
            }
        }else{
            ?><script>alert("電子郵件錯誤.");location.href="join.php"</script><?php
        }
    }else{
        ?><script>alert("不能有特殊字元.");location.href="join.php"</script><?php
    }
}
?>