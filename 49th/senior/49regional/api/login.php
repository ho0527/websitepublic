<?php
    include("../link.php");
    $error=true;
    $userid="未知";
    $message="未知錯誤請重新登入";

    if(isset($_POST["submit"])){
        $username=$_POST["username"];
        $password=$_POST["password"];
        if($row=query($db,"SELECT*FROM `user` WHERE `username`='$username'")){
            $row=$row[0];
            if($row[3]==$password){
                if($_SESSION["verifycode"]==$_POST["verifycode"]){
                    $error=false;
                    $userid=$row[1];
                    $permission=$row[5];
                }else{
                    $userid=$row[1];
                    $message="圖形驗證碼有誤";
                }
            }else{
                $userid=$row[1];
                $message="密碼有誤";
            }
        }else{
            $userid="未知";
            $message="帳號有誤";
        }
    }

    if($error){
        echo(json_encode([
            "success"=>false,
            "data"=>[
                "id"=>$userid,
                "message"=>$message
            ]
        ]));
    }else{
        echo(json_encode([
            "success"=>true,
            "data"=>[
                "id"=>$userid,
                "permission"=>$permission
            ]
        ]));
    }
?>