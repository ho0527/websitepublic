<?php
    include("link.php");
    if(isset($_POST['name'])){
        $name=$_POST['name'];
        $email=$_POST['email'];
        $tel=$_POST['tel'];
        $msg=$_POST['msg'];
        $msgnum=$_POST['msgnum'];
        if(!preg_match("/[\\\,\`,\~,\!,\#,\$,\%,\^,\&,\*,\(,\),\_,\+,\=,\[,\],\{,\},\:,\;,\?,\>,\<,\/,\|,\,]/",$name.$email.$tel.$msgnum)){
            if(preg_match("/[0-9]{4}/",$msgnum)){
                if(preg_match("/^.+\@.+\..+((\..+)+)?$/",$email)){
                    if(preg_match("/^[0-9]+((\ -[0-9])+)?$/",$tel)){
                        $picture="";
                        if(!empty($_FILES["picture"]["name"])){
                            $rand=rand(0,9999999999);
                            $str=str_pad($rand,10,"0",STR_PAD_LEFT);
                            move_uploaded_file($_FILES["picture"]["tmp_name"],"image/".$str);
                            $picture="image/".$str;
                        }
                        $db->query("INSERT INTO `message`(`name`, `email`, `tel`, `msg`, `img`, `createdate`, `editdate`, `isdel`, `msgnum`) VALUES ('$name','$email','$tel','$msg','$picture','$date','$date','0','$msgnum')");
                        ?>
                            <script>
                                // alert("新增成功!");location.href="chat.php";
                                echo('5');           
                            </script>
                        <?php
                    }else{
                        ?>
                            <script>
                                // alert("電話號碼錯誤.");location.href="chat.php"
                            </script>
                            
                        <?php
                        echo('1');
                    }
                }else{
                    ?>
                        <script>
                            // alert("電子郵件錯誤.");location.href="chat.php"
                        </script>
                        
                    <?php
                    echo('2');
                }
            }else{
                ?>
                    <script>
                        // alert("序號錯誤.");location.href="chat.php"
                    </script>
                    
                <?php
                echo('3');
            }
        }else{
            ?>
                <script>
                    // alert("不能有特殊字元.");location.href="chat.php"
                </script>
                
            <?php
            echo('4');
        }
    }
?>