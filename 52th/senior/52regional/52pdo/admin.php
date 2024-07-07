<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>管理者專區</title>
        <link href="index.css" rel="Stylesheet">
    </head>
    <body>
         <table>
            <tr>
               <td class="admin-title">
                  <form>
                     管理者專區
                     <button type="button" onclick="location.href='signup.php'">新增</button>
                     <button name="logout">登出</button>
                  </form>
               </td>
               <td class="admin-find">
                  <form>
                     <input type="search" name="search" placeholder="查詢">
                     <button name="enter">送出</button>
                  </form>
               </td>
            </tr>
            <tr>
               <td>
                  <div class="admin">
                     <table class="main-table">
                        <form>
                           <tr>
                              <td class="admin-table-num">編號<input type="submit" name="num-up-down" id="num-up-down" value="升冪"></td>
                              <td class="admin-table">使用者帳號<input type="submit" name="user-up-down" id="user-up-down" value="升冪"></td>
                              <td class="admin-table">密碼<input type="submit" name="code-up-down" id="code-up-down" value="升冪"></td>
                              <td class="admin-table">名稱<input type="submit" name="name-up-down" id="name-up-down" value="升冪"></td>
                              <td class="admin-table">權限</td>
                              <td class="admin-table">登入時間</td>
                              <td class="admin-table">登出時間</td>
                              <td class="admin-table">動作</td>
                              <td class="admin-table">動作時間</td>
                           </tr>
                           <?php
                              include("link.php");
                              include("admindef.php");
                              @$admin_data=$_SESSION["data"];
                              if(isset($_GET["logout"])){
                                 if(isset($admin_data)){
                                    $row=query($db,"SELECT*FROM `admin` WHERE `adminnumber`='$admin_data'")[0];
                                    query($db,"UPDATE `data` SET `logouttime`='$time' WHERE `usernumber`='$admin_data' AND `logouttime`=''");
                                    query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)VALUES('$admin_data','$row[1]','$row[2]','$row[3]','管理者','-','-','登出','$time')");
                                    session_unset();
                                    ?><script>alert("登出成功!");location.href="index.php"</script><?php
                                 }else{
                                    query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)VALUES('null','','','','','','','登出','$time')");
                                    session_unset();
                                    ?><script>alert("登出成功!");location.href="index.php"</script><?php
                                 }
                              }
                              if(isset($_GET["enter"])){
                                 $_SESSION["type"]=$_GET["search"];
                                 header("location:admin.php");
                              }
                              if(isset($_SESSION["type"])){
                                 $type=$_SESSION["type"];
                                 if($type==""){
                                    unset($_SESSION["type"]);
                                    header("location:admin.php");
                                 }else{
                                    $data=query($db,"SELECT*FROM `data` WHERE `usernumber`LIKE'%$type%' or `username`LIKE'%$type%' or `password`LIKE'%$type%' or `name`LIKE'%$type%' or `permission`LIKE'%$type%' or `logintime`LIKE'%$type%' or `logouttime`LIKE'%$type%' or `move`LIKE'%$type%' or `movetime`LIKE'%$type%'");
                                    issetgetupdown($data);
                                 }
                              }else{
                                 $data=query($db,"SELECT*FROM `data`");
                                 issetgetupdown($data);
                              }
                              if(isset($_GET["del"])){
                                 $number=$_GET["del"];
                                 if($row=query($db,"SELECT*FROM `user` WHERE `userNumber`='$number'")){
                                    $row=$row[0];
                                    query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)VALUES('$number','$row[1]','$row[2]','$row[3]','一般使用者','-','-','管理員刪除','$time')");
                                    query($db,"DELETE FROM `user` WHERE `userNumber`='$number'");
                                    ?><script>alert("刪除成功!");location.href="admin.php"</script><?php
                                 }elseif($row=query($db,"SELECT*FROM `admin` WHERE `adminnumber`='$number'")){
                                    $row=$row[0];
                                    query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)VALUES('$number','$row[1]','$row[2]','$row[3]','管理者','-','-','管理員刪除','$time')");
                                    query($db,"DELETE FROM `admin` WHERE `adminnumber`='$number'");
                                    ?><script>alert("刪除成功!");location.href="admin.php"</script><?php
                                 }else{
                                    ?><script>alert("帳號已被刪除!");location.href="admin.php"</script><?php
                                 }
                              }
                           ?>
                        </form>
                     </table>
                  </div>
               </td>
            </tr>
         </table>
    </body>
</html>