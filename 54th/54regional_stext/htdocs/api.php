<?php
    include("link.php");
    if(isset($_GET["logout"])){
        $data=$_SESSION["data"];
        if($row=query($db,"SELECT*FROM `user` WHERE `id`='$data'")){
            query($db,"INSERT INTO `data`(`number`, `username`, `name`, `move1`, `move2`, `time`) VALUES (?,?,?,'登出','成功','$time')",[
                $row[0][4],$row[0][1],$row[0][3]
            ]);
            session_unset();
            ?><script>alert("登出成功");location.href="index.php"</script><?php
        }else{
            query($db,"INSERT INTO `data`(`number`, `username`, `name`, `move1`, `move2`, `time`) VALUES ('未知','','','登出','成功','$time')");
            session_unset();
            ?><script>alert("登出成功");location.href="index.php"</script><?php
        }
    }

?>